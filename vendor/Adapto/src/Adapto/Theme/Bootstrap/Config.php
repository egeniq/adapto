<?php
/**
 * This file is part of the Adapto distribution.
 * Detailed copyright and licensing information can be found
 * in the doc/COPYRIGHT and doc/LICENSE files which should be
 * included in the distribution.
 *
 * @package adapto
 * @subpackage themes
 *
 * @copyright (c)2012 Egeniq
 */

namespace Adapto\Theme\Bootstrap;

class Config extends \Adapto\Theme\Config
{
    public $baseTheme = 'standard';
    
    public $parameters = array('stylesheets' => array('bootstrap.css', 'style.css'));
}


