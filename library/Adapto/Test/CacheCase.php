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
 * @copyright (c)2008 Sandy Pleyte
 * @author Sandy Pleyte <sandy@achievo.org>
 * 
 * @license http://www.achievo.org/atk/licensing ATK Open Source License
 *

 */ 
class Adapto_Test_CacheCase extends Adapto_TestCase
{

    protected $m_cache;
    protected $m_lifetime = 5;
    protected $m_prefix;
    protected $m_type = '';

    public function setup()
    {

        // Set lifetime for testcases
        $GLOBALS['config_cache'][$this->m_type]['lifetime'] = $this->m_lifetime;

        // Generate a unique prefix
        $this->m_prefix = uniqid('test_' . $this->m_type, true);

        try {
            $this->m_cache = atkCache::getInstance($this->m_type, false, true);
        } catch (Exception $ex) {
            $this->markTestSkipped($ex->getMessage());
            return;
        }

        // We need to make sure that the cache is empty
        // It's not correct, but I don't know an other 
        // way at te moment
        $this->m_cache->deleteAll();
    }

    /**
     * Check if we have the correct object
     */

    public function test_construct()
    {
        $classname = "atkCache_" . $this->m_type;
        $this->assertTrue($this->m_cache instanceof $classname, "Do we have the correct object (" . $this->m_type . ") ?");
    }

    /**
     * Check the isActive function
     */

    public function test_isActive()
    {
        // should be active by default
        $this->assertTrue($this->m_cache->isActive(), "Is cache active ?");

        // turn it off
        $this->m_cache->setActive(false);
        $this->assertFalse($this->m_cache->isActive(), "Is cache deactivated ?");

        // turn it back on
        $this->m_cache->setActive(true);
        $this->assertTrue($this->m_cache->isActive(), "Is cache active ?");
    }

    /**
     * Check the getLifeTime function
     */

    public function test_getLifetime()
    {

        $data = 'Lifetime data';

        // configured from setup
        $this->assertEquals($this->m_cache->getLifetime(), $this->m_lifetime);

        // store something

        // wait until just before the lifetime,
        // we should still get data
        sleep($this->m_cache->getLifetime() - 2);

        // wait until just after the lifetime,
        // we should get nothing
        sleep(3);

        // with custom lifetime on the set function

        //make sure that the default time isn't used
        sleep(2);

        sleep(2);

    }

    /**
     * Test if adding data to cache works
     */

    public function test_add()
    {

        $data = 'Add data';

        $data2 = 'Add data2';

        // add for the first time

        // add for the second time with a different value, should fail

        // make sure it really didn't overwrite the data

    }

    /**
     * Test if we can get data from the cache
     */

    public function test_get()
    {

        $data = 'Get data';

        // data has not been stored yet

        // store it

        // and we should be able to get now

        // deactivate then try to get
        $this->m_cache->setActive(false);
        $this->assertFalse($this->m_cache->isActive());

        // re-activate then try to get
        $this->m_cache->setActive(true);
        $this->assertTrue($this->m_cache->isActive(), 'Is cache still active ?');

    }

    /**
     * Test if we can save array's to the cache
     */

    public function test_set_Array()
    {

        $data = array('name' => 'atkCache', 'parent' => 'atk', 'year' => '2008',);

    }

    /**
     * Test if we can save objects's to the cache
     */

    public function test_set_Object()
    {

        $data = atknew("atk.atklanguage");

    }

    /**
     * Test if we can save strings to the cache
     */

    public function test_set_String()
    {

        $data = 'Save string data';

    }

    /**
     * Test if we can remove data from the cache
     */

    public function test_delete()
    {

        $data = 'Delete data';

        // data has not been stored yet

        // store it

        // and we should be able to get now

        // delete it, should not be able to get again

    }

    /**
     * Test if we can delete the complete cache
     */

    public function test_deleteAll()
    {
        $list = array($this->m_prefix . 'one', $this->m_prefix . 'two',);
        $data = 'Delete all data';

        foreach ($list as $id) {
            // data has not been stored yet
            $this->assertFalse($this->m_cache->get($id));
            // so store some data
            $this->assertTrue($this->m_cache->set($id, $data), "Save data");
            // and we should be able to get now
            $this->assertEquals($this->m_cache->get($id), $data);
        }

        // delete everything
        $this->m_cache->deleteAll();

        // should not be able to get again
        foreach ($list as $id) {
            $this->assertFalse($this->m_cache->get($id));
        }
    }

    /**
     * Test if we got the correct type
     */

    public function test_getType()
    {
        $this->assertTrue($this->m_cache->getType() == $this->m_type, "Is the cache of the correct type (" . $this->m_cache->getType() . " == " . $this->m_type
                        . ")?");
    }

}

