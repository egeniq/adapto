<?php
  
/**
 * The module definition class.
 *
 * The module file is similar to that of lesson 1, with an extra 
 * 'department' menu item. 
 */
class mod_lesson2 extends Adapto_Module
{           
    public function getMenuItems()
    {          
        $this->menuitem("lesson2"); 
      
        /**
         * The following line adds the new department menu item
         */       
        $this->menuitem("departments", dispatch_url("lesson2.department", "admin"), "lesson2"); 
        $this->menuitem("employees", dispatch_url("lesson2.employee", "admin"), "lesson2"); 
      
        $this->menuitem("-", "", "lesson2");
      
        $this->menuitem("modulesource", moduleSourceUrl("lesson2"), "lesson2");
    }
}

