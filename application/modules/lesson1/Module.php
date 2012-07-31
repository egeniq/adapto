<?php

class Lesson1_Module extends Adapto_Module
{
    public function initMenu(Adapto_Menu $menu)
    {
        $lesson1Menu = new Adapto_Menu("Lesson1");
        
        $lesson1Menu->addItem(new Adapto_Menu_Item_Link("http://www.google.com", "Google"));
        $lesson1Menu->addItem(new Adapto_Menu_Item_Separator());
        $lesson1Menu->addItem(new Adapto_Menu_Item_Entity("lesson1", "employee"));
        
        $menu->addSubMenu($lesson1Menu);
    }
}