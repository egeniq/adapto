<?php
/**
 * This file is part of the Adapto Toolkit.
 * Detailed copyright and licensing information can be found
 * in the doc/COPYRIGHT and doc/LICENSE files which should be
 * included in the distribution.
 *
 * @package adapto
 * @subpackage test
 *
 * @copyright (c)2005 Ibuildings.nl
 * @license http://www.achievo.org/atk/licensing ATK Open Source License
 *

 */

/**
 * @internal includes
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * Adapto_Test_Case is a specialization of SimpleTest's UnitTestCase. It
 * contains utility methods that can be used by testcases, such as
 * the ability to swap the default database driver with a mock
 * version
 *
 * @author ijansch
 * @package adapto
 * @subpackage test
 *
 */
abstract class Adapto_Test_Case extends PHPUnit_Framework_TestCase
{
    public $useTestDatabase = false;

    private $m_path = null;

    public $m_restoreDb = array(); // defaulted to public
    public $m_restoreEntity = array(); // defaulted to public
    public $m_restoreSecMgr = NULL; // defaulted to public

    public $m_fixtureData = array(); // defaulted to public

    /**
     * Constructor.
     *
     * @return Adapto_Test_Case
     */

    public function __construct($label = null)
    {
        parent::__construct($label);
    }

    /**
     * Called before each test method to setup some
     * data etc. By default this means the fixture data
     * will be loaded into the test database. Make sure
     * you call the parent if you override this method!
     */

    public function setUp()
    {
        if ($this->useTestDatabase && !$this->_validateTestDb()) {
            $this->markTestSkipped('Test database isn\'t configured properly.');
            return;
        }

        // don't let test-cases influence each other, so make sure
        // the entity repository etc. is empty when we start a new test-case
        global $g_entityRepository, $g_moduleRepository, $g_entityHandlers, $g_entityListeners, $g_entityControllers;
        $g_entityRepository = array();
        $g_moduleRepository = array();
        $g_entityHandlers = array();
        $g_entityListeners = array();
        $g_entityControllers = array();

        if ($this->useTestDatabase) {
            // switch to the test databases
            $mapping = Adapto_Config::getGlobal("test_db_mapping");
            foreach ($mapping as $test) {
                $testDb = atkGetDb($test);
                $testDb->toggleForeignKeys(false);
                $testDb->deleteAll();
                $testDb->commit();
                $testDb->toggleForeignKeys(true);
            }

            atkDb::useMapping($mapping);

            $this->setUpTestDatabase();
        }
    }

    /**
     * Set-up test database data.
     */

    public function setUpTestDatabase()
    {

    }

    /**
     * Check if we have a test database for each database.
     *
     * @return boolean
     */

    protected function _validateTestDb()
    {
        $db = Adapto_Config::getGlobal('db');
        $mapping = Adapto_Config::getGlobal("test_db_mapping");

        foreach (array_keys($db) as $normal) {
            if (in_array($normal, $mapping)) {
                continue;
            }

            if (!isset($db[$mapping[$normal]])) {
                return false;
            }

            $conn = atkGetDb($mapping[$normal]);
            if ($conn->getDbStatus() != DB_SUCCESS) {
                return false;
            }
        }

        return true;
    }

    /**
     * Called after each test method. By default this
     * means the fixture data and any other data added
     * to the database will be removed from the test
     * database. Make sure you call the parent if you
     * override this method!
     */
    function tearDown()
    {
        if ($this->useTestDatabase) {
            atkDb::clearMapping();
        }
    }

    /**
     * Adds the given fixture to the given database.
     *
     * @param string $name     fixture name
     * @param string $database database name
     */
    function addFixture($name, $database = "default")
    {
        $this->addFixtures(array($name), $database);
    }

    /**
     * Adds the given fixtures to the given database.
     *
     * @param array $names     fixture names
     * @param string $database database name
     */
    function addFixtures($names, $database = "default")
    {
        atkdebug("Load fixtures into test database...");

        $db = $this->_getTestDb($database);
        $db->toggleForeignKeys(false);

        $manager = atkFixtureManager::getInstance();

        foreach ($names as $fullname) {
            $result = $manager->load($fullname, $db, $this->_getPath());
            if ($result === false) {
                throw new Exception("Error loading fixture '$fullname'!");
            }

            $table = $result['table'];
            $data = $result['data'];

            if (!isset($this->m_fixtureData[$database][$table]))
                $this->m_fixtureData[$database][$table] = array();

            $this->m_fixtureData[$database][$table] = array_merge($this->m_fixtureData[$database][$table], $data);
        }

        $db->toggleForeignKeys(true);
        $db->commit();
    }

    /**
     * Returns the fixture for the given table name with the given name.
     * If the fixture doesn't exist NULL is returned. If no database is specified
     * all databases will be searched. If no name is specified all fixtures for
     * the given table will be returned.
     *
     * @param string $table table name
     * @param string $name  fixture name
     *
     * @return array fixture data
     */
    function fixture($table, $name = NULL, $database = NULL)
    {
        if ($database != NULL && $name == NULL && isset($this->m_fixtureData[$database][$table])) {
            return $this->m_fixtureData[$database][$table];
        } else if ($database != NULL && $name != NULL && isset($this->m_fixtureData[$database][$table][$name])) {
            return $this->m_fixtureData[$database][$table][$name];
        } else if ($database == NULL) {
            foreach (array_keys($this->m_fixtureData) as $database) {
                $data = $this->fixture($table, $name, $database);
                if ($data != NULL)
                    return $data;
            }
        }

        return NULL;
    }

    /**
     * Override __call to intercept method calls for fixture data.
     * This makes it possible to access fixtures if there is a method
     * with the same name as the table the fixture is defined for. Only
     * works properly in PHP5.
     *
     * @param string $method method name (table name)
     * @param array  $args   method arguments
     *
     * @return array fixture data
     */
    function __call($method, $args)
    {
        foreach (array_keys($this->m_fixtureData) as $database) {
            if (isset($this->m_fixtureData[$database][$method]))
                return $this->fixture($method, isset($args[0]) ? $args[0] : NULL, isset($args[1]) ? $args[1] : NULL);
        }

        throw new Exception("Invalid method name $method for " . get_class($this) . " (fixture not loaded?)!");
    }

    /**
     * Get path for this test-case.
     *
     * @return returns the path for this test-case
     */
    function _getPath()
    {
        if (!isset($this->m_path)) {
            $ref = new ReflectionClass(get_class($this));
            $this->m_path = dirname($ref->getFilename()) . '/';
        }

        return $this->m_path;
    }

    /**
     * Get test database.
     */
    function &_getTestDb($database)
    {
        return atkGetDb($database);
    }

    function setMockDb($conn = NULL)
    {
        global $config_db;

        if ($conn === NULL)
            $conn = "default";

        $config_db["mock"]["driver"] = "atk.test.mocks.atkmockdb";

        $mockdb = &atkGetDb("mock");
        $this->m_restoreDb[$conn] = &atkDb::setInstance($conn, $mockdb);
    }

    function restoreDb($conn = NULL)
    {
        if ($conn === NULL)
            $conn = "default";
        atkDb::setInstance($conn, $this->m_restoreDb[$conn]);
    }

    function &setMockEntity($entityname, &$mockentity)
    {
        $this->m_restoreEntity[$entityname] = &atkSetEntity($entityname, $mockentity);
    }

    function restoreEntity($entityname)
    {
        atkSetEntity($entityname, $this->m_restoreEntity[$entityname]);
    }

    function &setMockSecurityManager(&$mockmanager)
    {
        $this->m_restoreSecMgr = &atkSetSecurityManager($mockmanager);
    }

    function restoreSecurityManager()
    {
        atkSetSecurityManager($this->m_restoreSecMgr);
    }

    /**
     * Asserts the given attribute(s) value did not cause the given validation error.
     *
     * $attribName can contain the name of a single attributename, or an array with
     * attributenames.
     *
     * @param array  $record
     * @param mixed $attribName
     * @param string $error
     */
    function _hasValidationError($record, $attribName, $error)
    {
        if (!isset($record['atkerror']))
            return false;

        $errors = $record['atkerror'];

        $found = false;

        foreach ($errors as $entry) {
            //does the error match?
            if ($entry['err'] == $error) {
                //If both $attribName and $entry["attrib_name"] are arrays, we could have a match.
                if (is_array($attribName) && is_array($entry["attrib_name"])) {
                    //if the number of elements is not the same, we do not have a match.
                    if (count($attribName) == count($entry["attrib_name"])) {
                        //check if the attributes in the arrays are the same
                        $allIn = true;
                        foreach ($attribName as $att) {
                            if (!in_array($att, $entry["attrib_name"])) {
                                $allIn = false;
                                break;
                            }
                        }

                        //if the arrays are the same, we have found a match
                        if ($allIn) {
                            $found = true;
                            break;
                        }
                    }
                }
                //If neither is an array, we could have a match.
 elseif (!is_array($attribName) && !is_array($error["attrib_name"])) {
                    //If the names are the same, we have a match.
                    if ($entry['attrib_name'] == $attribName) {
                        $found = true;
                        break;
                    }
                }
            }
        }

        return $found;
    }

    /**
     * Asserts the given attribute(s) value did not cause the given validation error.
     *
     * $attribName can contain the name of a single attributename, or an array with
     * attributenames.
     *
     * @param array  $record
     * @param mixed $attribName
     * @param string $error
     */
    function assertNoValidationError($record, $attribName, $error)
    {
        $found = $this->_hasValidationError($record, $attribName, $error);
        if (is_array($attribName))
            $this->assertTrue(!$found, 'Validation error ' . $error . ' not found for attributes ' . implode(", ", $attribName));
        else
            $this->assertTrue(!$found, 'Validation error ' . $error . ' not found for attribute ' . $attribName);
    }

    /**
     * Asserts the given attribute(s) value caused the given validation error.
     *
     * $attribName can contain the name of a single attributename, or an array with
     * attributenames.
     *
     * @param array  $record
     * @param mixed $attribName
     * @param string $error
     */
    function assertValidationError($record, $attribName, $error)
    {
        $found = $this->_hasValidationError($record, $attribName, $error);
        if (is_array($attribName))
            $this->assertTrue($found, 'Validation error ' . $error . ' for attributes ' . implode(", ", $attribName));
        else
            $this->assertTrue($found, 'Validation error ' . $error . ' for attribute ' . $attribName);
    }

    /**
     * Asserts if a certain attribute in the given entity has the given flag.
     * You should pass the flag name to this method!
     *
     * @param atkEntityType $entity
     * @param string $attribName
     * @param string $flagName
     */
    function assertAttributeHasFlag($entity, $attribName, $flagName)
    {
        $flag = eval("return " . $flagName . ";");
        $hasFlag = $entity->getAttribute($attribName)->hasFlag($flag);
        $this->assertTrue($hasFlag, "Attribute " . $entity->atkEntityType() . "::$attribName has flag $flagName");
    }

    /**
     * Asserts if a certain attribute in the given entity doesn't have the given flag.
     * You should pass the flag name to this method!
     *
     * @param atkEntityType $entity
     * @param string $attribName
     * @param string $flagName
     */
    function assertAttributeNotHasFlag($entity, $attribName, $flagName)
    {
        $flag = eval("return " . $flagName . ";");
        $hasFlag = $entity->getAttribute($attribName)->hasFlag($flag);
        $this->assertFalse($hasFlag, "Attribute " . $entity->atkEntityType() . "::$attribName not has flag $flagName");
    }
}
?>