<?php
/**
 * This file is part of the Adapto Toolkit.
 * Detailed copyright and licensing information can be found
 * in the doc/COPYRIGHT and doc/LICENSE files which should be
 * included in the distribution.
 *
 * @package adapto
 * @subpackage meta
 *
 * @copyright (c) 2004-2005 Peter C. Verhage
 * @license http://www.achievo.org/atk/licensing ATK Open Source License
 *

 */

/**
 * The basic (dutch) "grammar" rules.
 *
 * @author petercv
 *
 * @package adapto
 * @subpackage meta
 */
class Adapto_Meta_Grammar_DutchMeta extends Adapto_MetaGrammar
{
    /**
     * Returns the list of singular rules.
     *
     * @return list of singular rules
     */

    public function getSingularRules()
    {
        return array('/ven$/i' => 'f', '/ia$/i' => 'ium', '/onen$/i' => 'oon', '/aren$/i' => 'aar', '/ieen$/i' => 'ie', '/ingen$/i' => 'ing',
                '/([aoeiu])s$/i' => '\1', '/([^aoeiu])en$/i' => '\1');
    }

    /**
     * Returns the list of plural rules.
     *
     * @return list of plural rules
     */

    public function getPluralRules()
    {
        return array('/f$/i' => 'ven', '/ium$/i' => 'ia', '/oon$/i' => 'onen', '/aar$/i' => 'aren', '/([^t])ie$/i' => '\1ieen', '/ing$/i' => 'ingen',
                '/([aoeiu])$/i' => '\1s', '/([^aoeiu])$/i' => '\1en');
    }
}
