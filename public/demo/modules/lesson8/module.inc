<?php

  /**
   * The module definition class.
   *
   * This module introduces the Open Office document generator.
   * The module.inc file itself contains nothing new. The things to learn are
   * in the entity files.
   */
  class mod_lesson8 extends Adapto_Module
  {
    function getMenuItems()
    {
      $this->menuitem("lesson8");
      $this->menuitem("employees", dispatch_url("lesson8.employee", "admin"), "lesson8");
      $this->menuitem("-", "", "lesson8");
      $this->menuitem("modulesource", moduleSourceUrl("lesson8"), "lesson8");
    }
  }

?>