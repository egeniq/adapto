<?php
  
/**
 * The module definition class.
 *
 * The classname always starts with 'mod_', and ends with the name of the
 * module.
 *
 * Each module extends Adapto_Module, to tell the system that this is a module
 * definition.
 */
class mod_lesson1 extends Adapto_Module
{       
    /**
     * The getMenuItems() method is called by ATK to determine which menuitems
     * this module has. The method usually contains one or more calls to 
     * the menuitem() function.
     */
    public function getMenuItems()
    {    
        /**
         * The next line adds a submenu called 'lesson1' to the main menu.
         */
        $this->menuitem("lesson1"); 
      
        /**
         * The following line adds a menuitem called 'employees' to the 
         * 'lesson1' menu. The menuitems opens the 'admin' screen for the
         * 'employee' entity of the 'lesson1' module.
         */       
        $this->menuitem("employees", dispatch_url("lesson1.employee", "admin"), "lesson1"); 
      
        /** 
         * The next line is a separator in the lesson 1 menu. If the name is '-'
         * and the url is blank, the menuitem is considered a separator. It 
         * results in a spacing between the previous and the next menuitem.
         */
        $this->menuitem("-", "", "lesson1");
      
        /**
         * The next line adds a link to a syntax-highlighted view this source 
         * file. It uses the global moduleSourceUrl function (defined in the
         * lesson_utils module) to calculate the url.
         */
         $this->menuitem("modulesource", moduleSourceUrl("lesson1"), "lesson1");
    }
}

