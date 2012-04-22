<?php
  
  class mod_lesson6 extends Adapto_Module
  {       
    function getMenuItems()
    {    
      $this->menuitem("lesson6"); 
      
      $this->menuitem("employees_1", dispatch_url("lesson6.lesson6_employee1", "admin"), "lesson6"); 
      $this->menuitem("employees_2", dispatch_url("lesson6.lesson6_employee2", "admin"), "lesson6"); 
      $this->menuitem("employees_3", dispatch_url("lesson6.lesson6_employee3", "admin"), "lesson6"); 
      $this->menuitem("employees_4", dispatch_url("lesson6.lesson6_employee4", "admin"), "lesson6"); 
      $this->menuitem("department",  dispatch_url("lesson6.lesson6_department", "admin"), "lesson6"); 
      
      $this->menuitem("-", "", "lesson6");
      
      $this->menuitem("modulesource", moduleSourceUrl("lesson6"), "lesson6");
    }
  }

?>
