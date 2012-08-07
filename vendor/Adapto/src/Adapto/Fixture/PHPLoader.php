<?php

/**
 * This file is part of the Adapto Toolkit.
 * Detailed copyright and licensing information can be found
 * in the doc/COPYRIGHT and doc/LICENSE files which should be
 * included in the distribution.
 *
 * @package adapto
 * @subpackage fixture
 *
 * @copyright (c)2007 Ibuildings.nl BV
 * @license http://www.achievo.org/atk/licensing ATK Open Source License
 *

 */

/**
 * PHP fixture loader. Loads fixtures that are plain PHP
 * files. PHP files have access to a "global" variable named
 * $data that should be filled with the fixture data.
 *
 * @package adapto
 * @subpackage fixture
 * @author petercv
 */
class Adapto_Fixture_PHPLoader extends Adapto_AbstractFixtureLoader
{

    /**
     * Loads and returns the fixture data from the given file.
     *
     * @param string $path fixture file path
     * @return array fixture data
     */
    function load($path)
    {
        $data = array();
        include_once($path);
        return $data;
    }
}
?>