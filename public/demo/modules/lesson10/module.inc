<?php
  
  /**
   * The module definition class.
   *
   * The module file is equal to that of lesson 2.
   */
  class mod_lesson10 extends Adapto_Module
  {           
    function getMenuItems()
    {          
      $this->menuitem("lesson10"); 
      
      /**
       * The following line adds the new department menu item
       */       
      $this->menuitem("departments", dispatch_url("lesson10.department", "admin"), "lesson10"); 
      $this->menuitem("employees", dispatch_url("lesson10.employee", "admin"), "lesson10"); 
      
      $this->menuitem("-", "", "lesson10");
      
      $this->menuitem("modulesource", moduleSourceUrl("lesson10"), "lesson10");
    }
  }

?>
