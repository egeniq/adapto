<?php

namespace Lesson1; 

use \Adapto\Menu;

class Module extends \Adapto\Module
{
    public function initMenu(Menu\Menu $menu)
    {
        $lesson1Menu = new Menu\Menu("Lesson1");
        
        $lesson1Menu->addItem(new Menu\Item\Link("http://www.google.com", "Google"));
        $lesson1Menu->addItem(new Menu\Item\Separator());
        $lesson1Menu->addItem(new Menu\Item\Entity("lesson1", "employee"));
        
        $menu->addSubMenu($lesson1Menu);
    }
}