<?php
/**
 * This file is part of the Adapto Toolkit.
 * Detailed copyright and licensing information can be found
 * in the doc/COPYRIGHT and doc/LICENSE files which should be
 * included in the distribution.
 *
 * @package adapto
 * @subpackage db
 *
 * @copyright (c)2008 Ibuildings.nl BV
 * @license http://www.achievo.org/atk/licensing ATK Open Source License
 *

 */

/**
 * ATK driver for clustered databases. This class proxies queries
 * to correct read/write slaves.
 * 
 * @author Boy Baukema <boy@ibuildings.nl>
 * @package adapto
 * @subpackage db
 */
class Adapto_Db_Cluster extends Adapto_Db
{
    protected $m_name;

    /**
     * Array of read-only slaves
     * 
     * @var array
     */
    protected $m_readonly_entitys_config = array();

    /**
     * Array of write-only slaves
     * 
     * @var array
     */ 
    protected $m_writeonly_entitys_config = array();
    protected $m_entitys_config = array();

    /**
     * Cluster entity (database) that we are currently proxying for
     *
     * @var atkDb
     */
    protected $m_current_clusterentity;

    ////////////////////////////// ATKDB METHODS //////////////////////////////

    /**
     * Initialize the Adapto_Db_Cluster
     * 
     * @param string $connectionname The name of the database connection
     * @param string $mode			Mode can be r, w or rw
     * 
     * @access public 
     * @return void
     */

    public function init($connectionname, $mode = 'rw')
    {
        $this->m_name = $connectionname;
        $this->setConfig();
        $this->setCurrentClusterEntity($mode);
        return $this;
    }

    /**
     * Connects to a cluster entity and sets the entity as "current entity" 
     * 
     * @param string $mode Mode can be r, w or rw
     * 
     * @access public
     * @return bool Whether the connect succeded or not
     */

    public function connect($mode = 'rw')
    {
        $this->setCurrentClusterEntity($mode);
        return $this->m_current_clusterentity->connect($mode);
    }

    /**
     * Returns entitys that have a specific mode set
     * 
     * @param string $mode Mode can be r, w or rw
     * 
     * @access public
     * @return array
     */

    public function hasMode($mode)
    {
        static $s_modes = array();
        if (isset($s_modes[$mode]))
            return $s_modes[$mode];

        $configtypes = array($this->m_readonly_entitys_config, $this->m_writeonly_entitys_config, $this->m_entitys_config);
        foreach ($configtypes as $configtype) {
            foreach ($configtype as $entity) {
                if (isset($entity['mode']) && strstr($entity['mode'], $mode)) {
                    $s_modes[$mode] = true;
                }
            }
        }
        if (!isset($s_modes[$mode]))
            $s_modes[$mode] = false;
        return $s_modes[$mode];
    }

    /**
     * Query method, first detects the query mode (read/write)
     * and connects to the proper database before executing the query on it.
     *
     * @return bool Wether or not the query was executed successfully
     */

    public function query()
    {
        $args = func_get_args();

        $this->connect(atkDb::getQueryMode($args[0]));

        return call_user_method_array('query', $this->m_current_clusterentity, $args);
    }

    /**
     * Creates a new a new query object based on the current entitys type
     * 
     * @access public
     * @return object
     */

    public function createQuery()
    {
        $query = &Adapto_ClassLoader::create("atk.db.atk{$this->m_current_clusterentity->m_type}query");
        $query->m_db = $this;
        return $query;
    }

    /**
     * Creates a new new Adapto_DDL based on current cluster entitys type
     * 
     * @access public 
     * @return atkDDL
     */

    public function createDDL()
    {

        $ddl = &atkDDL::create($this->m_current_clusterentity->m_type);
        $ddl->m_db = $this;
        return $ddl;
    }

    /**
     * Gets the next available id 
     *
     * @access public
     * @return int
     */

    public function nextid()
    {
        $args = func_get_args();
        $this->connect('w');
        return call_user_method_array('nextid', $this->m_current_clusterentity, $args);
    }

    ////////////////////////////// CLUSTER METHODS //////////////////////////////

    /**
     * Sets config and mode for all configured entitys
     *
     * @access protected 
     * @return void
     */

    protected function setConfig()
    {
        $dbconfig = Adapto_Config::getGlobal('db');
        $config = $dbconfig[$this->m_name];
        foreach ($config['entitys'] as $mode => $entitys) {
            if (is_array($entitys)) {
                foreach ($entitys as $entity)
                    $this->setEntityConfig($entity, $dbconfig[$entity], $mode);
            } else
                $this->setEntityConfig($entitys, $dbconfig[$entitys], $mode);
        }
    }

    /**
     * Sets the config and mode for a named entity
     *
     * @param string $entityname
     * @param array  $entityconfig
     * @param string $mode
     * 
     * @access protected
     * @return void
     */

    protected function setEntityConfig($entityname, $entityconfig, $mode)
    {
        if ($mode === 'r') {
            $this->m_readonly_entitys_config[$entityname] = $entityconfig;
        } else if ($mode === 'w') {
            $this->m_writeonly_entitys_config[$entityname] = $entityconfig;
        } else {
            $this->m_entitys_config[$entityname] = $entityconfig;
        }
    }

    /**
     * Sets a random cluster entity as the current entity based on the mode provided
     *
     * @param string $mode
     * 
     * @access protected
     * @return void
     */

    protected function setCurrentClusterEntity($mode)
    {
        if (!$this->m_current_clusterentity || !$this->m_current_clusterentity->hasMode($mode)) {
            if ($mode === 'r' && !empty($this->m_readonly_entitys_config)) {
                $this->setRandomEntityFromEntityConfigs($this->m_readonly_entitys_config, $mode);
            } else if ($mode === 'w' && !empty($this->m_writeonly_entitys_config)) {
                $this->setRandomEntityFromEntityConfigs($this->m_writeonly_entitys_config, $mode);
            } else {
                $this->setRandomEntityFromEntityConfigs($this->m_entitys_config, $mode);
            }
        }
    }

    /**
     * Selects a random entity from the entity configuration based
     * on the mode.
     *
     * @param array  $entityconfigs
     * @param string $mode
     * 
     * @access protected
     * @return void
     */

    protected function setRandomEntityFromEntityConfigs($entityconfigs, $mode)
    {
        $entitynames = array_keys($entityconfigs);
        $number = mt_rand(0, count($entityconfigs) - 1);
        $entityname = $entitynames[$number];

        $this->m_current_clusterentity = atkGetDb($entityname, false, $mode);
    }

    ////////////////////////////// OVERLOADING METHODS //////////////////////////////

    /**
     * Allows setting key/value pairs for the current entity
     *
     * @param string $name
     * @param mixed  $value
     * 
     * @access public
     * @return void
     */

    public function __set($name, $value)
    {
        $this->m_current_clusterentity->$name = $value;
    }

    /**
     * Gets a value from current entitys properties based on key
     *
     * @param string $name
     * @return void
     */

    public function __get($name)
    {
        return $this->m_current_clusterentity->$name;
    }

    /**
     * Checks if current entity has the property set
     *
     * @param string $name
     * @return bool
     */

    public function __isset($name)
    {
        return isset($this->m_current_clusterentity->$name);
    }

    /**
     * Magic unset function
     *
     * @param string $name
     */

    public function __unset($name)
    {
        unset($this->m_current_clusterentity->$name);
    }

    /**
     * Magic call function
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     */

    public function __call($name, $arguments)
    {
        return call_user_method_array($name, $this->m_current_clusterentity, $arguments);
    }

    /**
     * Magic callstatic function
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     */

    public static function __callStatic($name, $arguments)
    {
        return call_user_func_array(array(__NAMESPACE__ . '::' . get_class($this->m_current_clusterentity), $name), $arguments);
    }

    /**
     * Because we extend atkDb __call won't be called for the atkDb public methods as they
     * are already implemented. We can't not-extend atkDb because this would break typehinting
     * (people are using atkDb as typehints everywhere). The most decent way to fix this issue
     * would be to make atkDb into an interface and then have Adapto_Db_Cluster and atkDb implement it
     * (and use 'atkDbInterface' for typehinting instead of atkDb), but this would break backward
     * compatibility (maybe other people use atkDb in their code as well for typehinting) and since 
     * Adapto_Db_Cluster doesn't seem to be used very often anyway for now we just solved this issue 
     * using some vogon poetry. Continue reading at your own risk.
     */

    public function setSequenceValue()
    {
        $args = func_get_args();
        return $this->__call(__FUNCTION__, $args);
    }

    public function useMapping()
    {
        $args = func_get_args();
        return $this->__call(__FUNCTION__, $args);
    }

    public function getMapping()
    {
        $args = func_get_args();
        return $this->__call(__FUNCTION__, $args);
    }

    public function clearMapping()
    {
        $args = func_get_args();
        return $this->__call(__FUNCTION__, $args);
    }

    public function getTranslatedDatabaseName()
    {
        $args = func_get_args();
        return $this->__call(__FUNCTION__, $args);
    }

    public function _getOrUseMapping()
    {
        $args = func_get_args();
        return $this->__call(__FUNCTION__, $args);
    }

    public function getType()
    {
        $args = func_get_args();
        return $this->__call(__FUNCTION__, $args);
    }

    public function link_id()
    {
        $args = func_get_args();
        return $this->__call(__FUNCTION__, $args);
    }

    public function hasError()
    {
        $args = func_get_args();
        return $this->__call(__FUNCTION__, $args);
    }

    public function getErrorType()
    {
        $args = func_get_args();
        return $this->__call(__FUNCTION__, $args);
    }

    public function getAtkDbErrno()
    {
        $args = func_get_args();
        return $this->__call(__FUNCTION__, $args);
    }

    public function getDbErrno()
    {
        $args = func_get_args();
        return $this->__call(__FUNCTION__, $args);
    }

    public function getDbError()
    {
        $args = func_get_args();
        return $this->__call(__FUNCTION__, $args);
    }

    public function setUserError()
    {
        $args = func_get_args();
        return $this->__call(__FUNCTION__, $args);
    }

    public function getQueryMode()
    {
        $args = func_get_args();
        return $this->__call(__FUNCTION__, $args);
    }

    public function errorLookup()
    {
        $args = func_get_args();
        return $this->__call(__FUNCTION__, $args);
    }

    public function getErrorMsg()
    {
        $args = func_get_args();
        return $this->__call(__FUNCTION__, $args);
    }

    public function halt()
    {
        $args = func_get_args();
        return $this->__call(__FUNCTION__, $args);
    }

    public function query_id()
    {
        $args = func_get_args();
        return $this->__call(__FUNCTION__, $args);
    }

    public function doConnect($host, $user, $password, $database, $port, $charset)
    {
        $args = func_get_args();
        return $this->__call(__FUNCTION__, $args);
    }

    public function _translateError()
    {
        $args = func_get_args();
        return $this->__call(__FUNCTION__, $args);
    }

    public function disconnect()
    {
        $args = func_get_args();
        return $this->__call(__FUNCTION__, $args);
    }

    public function commit()
    {
        $args = func_get_args();
        return $this->__call(__FUNCTION__, $args);
    }

    public function savepoint()
    {
        $args = func_get_args();
        return $this->__call(__FUNCTION__, $args);
    }

    public function rollback()
    {
        $args = func_get_args();
        return $this->__call(__FUNCTION__, $args);
    }

    public function next_record()
    {
        $args = func_get_args();
        return $this->__call(__FUNCTION__, $args);
    }

    public function lock()
    {
        $args = func_get_args();
        return $this->__call(__FUNCTION__, $args);
    }

    public function unlock()
    {
        $args = func_get_args();
        return $this->__call(__FUNCTION__, $args);
    }

    public function affected_rows()
    {
        $args = func_get_args();
        return $this->__call(__FUNCTION__, $args);
    }

    public function metadata()
    {
        $args = func_get_args();
        return $this->__call(__FUNCTION__, $args);
    }

    public function table_names()
    {
        $args = func_get_args();
        return $this->__call(__FUNCTION__, $args);
    }

    public function tableExists()
    {
        $args = func_get_args();
        return $this->__call(__FUNCTION__, $args);
    }

    public function getrows()
    {
        $args = func_get_args();
        return $this->__call(__FUNCTION__, $args);
    }

    public function getValue()
    {
        $args = func_get_args();
        return $this->__call(__FUNCTION__, $args);
    }

    public function getValues()
    {
        $args = func_get_args();
        return $this->__call(__FUNCTION__, $args);
    }

    public function getSearchModes()
    {
        $args = func_get_args();
        return $this->__call(__FUNCTION__, $args);
    }

    public function tableMeta()
    {
        $args = func_get_args();
        return $this->__call(__FUNCTION__, $args);
    }

    public function func_now()
    {
        $args = func_get_args();
        return $this->__call(__FUNCTION__, $args);
    }

    public function func_substring()
    {
        $args = func_get_args();
        return $this->__call(__FUNCTION__, $args);
    }

    public function func_datetochar()
    {
        $args = func_get_args();
        return $this->__call(__FUNCTION__, $args);
    }

    public function func_concat()
    {
        $args = func_get_args();
        return $this->__call(__FUNCTION__, $args);
    }

    public function func_concat_ws()
    {
        $args = func_get_args();
        return $this->__call(__FUNCTION__, $args);
    }

    public function vendorDateFormat()
    {
        $args = func_get_args();
        return $this->__call(__FUNCTION__, $args);
    }

    public function func_datetimetochar()
    {
        $args = func_get_args();
        return $this->__call(__FUNCTION__, $args);
    }

    public function maxIdentifierLength()
    {
        $args = func_get_args();
        return $this->__call(__FUNCTION__, $args);
    }

    public function escapeSQL()
    {
        $args = func_get_args();
        return $this->__call(__FUNCTION__, $args);
    }

    public function toggleForeignKeys()
    {
        $args = func_get_args();
        return $this->__call(__FUNCTION__, $args);
    }

    public function deleteAll()
    {
        $args = func_get_args();
        return $this->__call(__FUNCTION__, $args);
    }

    public function dropAll()
    {
        $args = func_get_args();
        return $this->__call(__FUNCTION__, $args);
    }

    public function cloneAll()
    {
        $args = func_get_args();
        return $this->__call(__FUNCTION__, $args);
    }

    public function &getInstance()
    {
        $args = func_get_args();
        return $this->__call(__FUNCTION__, $args);
    }

    public function &setInstance()
    {
        $args = func_get_args();
        return $this->__call(__FUNCTION__, $args);
    }

    public function setHaltOnError()
    {
        $args = func_get_args();
        return $this->__call(__FUNCTION__, $args);
    }

    public function getDbStatus()
    {
        $args = func_get_args();
        return $this->__call(__FUNCTION__, $args);
    }

    public function quoteIdentifier()
    {
        $args = func_get_args();
        return $this->__call(__FUNCTION__, $args);
    }
}
?>