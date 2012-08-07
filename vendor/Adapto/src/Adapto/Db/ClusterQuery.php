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
 * A clustered query driver
 *
 * Proxies a query, creates a query bound to the current database entity
 * and performs the query on current database entity.
 *
 * @author Boy Baukema <boy@ibuildings.nl>
 * @package adapto
 * @subpackage db
 */
class Adapto_Db_ClusterQuery
{
    private $m_query;

    /**
     * Constructs a new Adapto_Db_ClusterQuery object
     * 
     * @return void
     */

    public function __construct()
    {
        $this->m_query = atkGetDb()->createQuery();
    }

    ////////////////////////////// OVERLOADING METHODS //////////////////////////////

    /**
     * Magic set method
     *
     * @param string $name
     * @param mixed $value
     */

    public function __set($name, $value)
    {
        $this->m_query->$name = $value;
    }

    /**
     * Magic get function
     *
     * @param string $name
     * @return mized
     */

    public function __get($name)
    {
        return $this->m_query->$name;
    }

    /**
     * Magic isset function
     *
     * @param string $name
     * @return bool
     */

    public function __isset($name)
    {
        return isset($this->m_query->$name);
    }

    /**
     * Magic unset method
     *
     * @param string $name
     */

    public function __unset($name)
    {
        unset($this->m_query->$name);
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
        return call_user_method_array($name, $this->m_query, $arguments);
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
        return call_user_func_array(array(__NAMESPACE__ . '::' . get_class($this->m_query), $name), $arguments);
    }
}

?>