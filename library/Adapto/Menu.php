<?php

/**
 * This file is part of the Adapto Toolkit.
 * Detailed copyright and licensing information can be found
 * in the doc/COPYRIGHT and doc/LICENSE files which should be
 * included in the distribution.
 *
 * @package adapto
 * @subpackage menu
 *
 * @copyright (c)2012 Egeniq BV
 *

 */

/**
 * Menu class
 * @package adapto
 * @subpackage menu
 */
class Adapto_Menu
{
    private $_subMenus = array();
    private $_items = array();
    private $_title;
    
    public function __construct($title)
    {
        $this->_title = $title;
    }
    
    public function addSubMenu(Adapto_Menu $menu)
    {
        $this->_subMenus[] = $menu;
    }
    
    public function addItem(Adapto_Menu_Item_Abstract $item)
    {
        $this->_items[] = $item;
    }
    
    public function getSubMenus()
    {
        return $this->_subMenus;
    }
    
    public function getItems()
    {
        return $this->_items;
    }
    
    public function getTitle()
    {
        return $this->_title;
    }
    
}

?>
