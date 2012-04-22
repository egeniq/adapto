<?php
  
  /**
   * The module definition class.
   *
   * The module file is identical to that of lesson 2.
   */
  class mod_lesson3 extends Adapto_Module
  {           
    function getMenuItems()
    {          
      $this->menuitem("lesson3"); 
      
      /**
       * The following line adds the new department menu item
       */       
      $this->menuitem("departments", dispatch_url("lesson3.department", "admin"), "lesson3"); 
      $this->menuitem("employees", dispatch_url("lesson3.employee", "admin"), "lesson3"); 
      
      $this->menuitem("-", "", "lesson3");
      
      $this->menuitem("modulesource", moduleSourceUrl("lesson3"), "lesson3");
    }
  }

?>