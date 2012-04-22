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
 * @author petercv
 *
 * @copyright (c)2007 Ibuildings.nl BV
 * @license see doc/LICENSE
 *

 */

/**
 * Includes
 * @access private
 */
include_once(Adapto_Config::getGlobal("atkroot") . "atk/ext/spyc/spyc.php");

/**
 * ATK YAML wrapper.
 *
 * @author petercv
 * @package adapto
 * @subpackage utils
 */
class Adapto_Util_YAML
{

    /**
     * Convert an array to it's YAML string representation.
     *
     * @param array $array the array
     * @return string YAML string representation
     *
     * @static
     */
    function dump($array)
    {
        return Spyc::YAMLDump($array);
    }

    /**
     * Convert an YAML string representation to an array.
     *
     * @param string $string the YAML string
     * @return array converted array
     *
     * @static
     */
    function load($string)
    {
        return Spyc::YAMLLoad($string);
    }

    /**
     * Create a YAML entity string for the given key and value
     * at the given indent level.
     *
     * @param string $key The name of the key
     * @param mixed $value The value of the item
     * @param int $indent The indent of the current entity
     */
    function entity($key, $value, $indent = 0)
    {
        $spyc = new Spyc();
        $spyc->_dumpIndent = 2;
        $spyc->_dumpWordWrap = 40;
        return $spyc->_yamlize($key, $value, $indent);
    }
}
