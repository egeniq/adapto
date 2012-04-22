<?php

  /**
   */
  class mod_lesson9 extends Adapto_Module
  {
    function getMenuItems()
    {
      $this->menuitem("lesson9");

      $this->menuitem("employees", dispatch_url("lesson9.employee", "admin"), "lesson9");
      $this->menuitem("projects", dispatch_url("lesson9.project", "admin"), "lesson9");
      $this->menuitem("-", "", "lesson9");

      $this->menuitem("modulesource", moduleSourceUrl("lesson9"), "lesson9");
    }
  }

?>
