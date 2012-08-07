<?php

/**
 * This file is part of the Adapto Toolkit.
 * Detailed copyright and licensing information can be found
 * in the doc/COPYRIGHT and doc/LICENSE files which should be
 * included in the distribution.
 *
 * @package adapto
 * @subpackage ui
 *
 * @copyright (c)2000-2004 Ivo Jansch
 * @license http://www.achievo.org/atk/licensing ATK Open Source License
 *
 */

namespace Adapto\View\Helper;

/**
 * Page renderer.
 *
 * This class is used to render output as an html page. It takes care of
 * creating a header, loading javascripts and loading stylesheets.
 * Since any script will output exactly one page to the browser, this is
 * a singleton. Use getInstance() to retrieve the one-and-only instance.
 *
 * @todo This should actually not be a singleton. HTML file generation
 *       scripts may need an instance per page generated.
 *
 * @author ijansch
 * @package adapto
 * @subpackage ui
 *
 */
class Theme extends \Zend\View\Helper\AbstractHelper
{
    public function __invoke() {
        $this->_applyMeta();
        $this->_applyBaseJs();
        $this->_applyStyles();
    }
    
    protected function _applyMeta()
    {
        $version = \Adapto\About::getVersion();
       
        $this->getView()->headMeta()->appendName('adapto_version', $version);
    }
        
    protected function _applyStyles()
    {
        $theme = \Adapto\ClassLoader::getInstance('Adapto\Ui\Theme');
        foreach ($theme->pageStyles() as $style) {
            $this->getView()->headLink()->prependStylesheet($style);
        }
    }
    
    protected function _applyBaseJs()
    {
        $this->getView()->headScript()->prependFile('/adapto_static/standard/js/jquery-1.7.2.min.js');
    }
     
}

?>
