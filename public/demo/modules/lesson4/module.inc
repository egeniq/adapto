<?php
  
  /**
   * The module definition class.
   *
   * The module file is identical to that of lesson 3.
   */
  class mod_lesson4 extends Adapto_Module
  {           
    function getMenuItems()
    {          
      $this->menuitem("lesson4"); 
               
      $this->menuitem("departments", dispatch_url("lesson4.department", "admin"), "lesson4"); 
      $this->menuitem("employees", dispatch_url("lesson4.employee", "admin"), "lesson4"); 
      
      $this->menuitem("-", "", "lesson4");
      
      $this->menuitem("modulesource", moduleSourceUrl("lesson4"), "lesson4");
    }
  }

?>