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
 * YAML fixture loader. Loads YAML fixtures. In the YAML
 * fixture you can optionally use PHP code to output the fixtures
 * PHP code should be enclosed in PHP tags. Script has full access
 * to all of ATK. Scripts output will be interpreted as YAML.
 *
 * @author petercv
 * @package adapto
 * @subpackage fixture
 */
class Adapto_Fixture_YAMLLoader extends Adapto_AbstractFixtureLoader
{

    /**
     * Loads and returns the fixture data from the given file.
     *
     * @param string $path fixture file path
     * @return array fixture data
     */
    function load($path)
    {
        $contents = file_get_contents($path);
        $contents = $this->parse($contents);

        $data = atkYAML::load($contents);
        return $data;
    }
}
