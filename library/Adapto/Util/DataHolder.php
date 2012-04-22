<?php

/**
 * This file is part of the Adapto Toolkit.
 * Detailed copyright and licensing information can be found
 * in the doc/COPYRIGHT and doc/LICENSE files which should be
 * included in the distribution.
 *
 * @package adapto
 * @subpackage utils
 *
 * @copyright (c)2007 Ibuildings.nl BV
 * @license http://www.achievo.org/atk/licensing ATK Open Source License
 *

 */

/**
 * The Adapto_Util_DataHolder class represents a class
 * that contains a key-value array .
 *
 * @author Peter Verhage <peter@ibuildings.nl>
 * @package adapto
 * @subpackage utils
 *
 */
class Adapto_Util_DataHolder
{
    protected $m_vars;

    /**
     * Constructor.
     *
     * @param array $vars
     */
    function __construct(&$vars)
    {
        $this->m_vars = &$vars;
    }

    /**
     * Is variable set?
     *
     * @param string $name variable name
     * @return boolean is set?
     */
    function __isset($name)
    {
        return isset($this->m_vars[$name]);
    }

    /**
     * Returns a reference to the given variable.
     *
     * @param string $name variable name
     * @return mixed reference to var
     */
    function &__get($name)
    {
        return $this->m_vars[$name];
    }

    /**
     * Set variable.
     *
     * @param string $name variable name
     * @param mixed $value variable value
     */
    function __set($name, $value)
    {
        $this->m_vars[$name] = $value;
    }

    /**
     * Retrieve variable value through method call.
     *
     * NOTE: this will always return a *copy* of the variable value!
     *
     * @param string $method method name
     * @param array $args arguments
     * @return mixed variabel value (copy!)
     */
    function __call($method, $args)
    {
        return $this->m_vars[$name];
    }

    /**
     * Convert to array, by default returns a copy
     * of the original array. If $copy is set to false
     * will return a reference to the internal array.
     *
     * @param bool $copy return a copy or the original array?
     *
     * @return array array
     */

    public function &toArray($copy = true)
    {
        if ($copy) {
            $copy = $this->m_vars;
            return $copy;
        } else {
            return $this->m_vars;
        }
    }
}
?>
