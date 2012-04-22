<?php

  /**
   * The module definition class.
   *
   * This module introduces data internationalisation and the atkTreeEntity.
   * The module.inc file itself contains nothing new. The things to learn are
   * in the entity files.
   *
   * There are two flavours of data internationalisation. Column based and
   * record based. the 'translation' entity demonstrates column based data
   * internationalisation, the 'translation_mr' entity demonstrates record
   * based internationalisation. See both entitys for a more elaborate
   * explanation.
   */
  class mod_lesson7 extends Adapto_Module
  {
    function getMenuItems()
    {
      $this->menuitem("lesson7");

      $this->menuitem("category", dispatch_url("lesson7.category", "admin"), "lesson7");
      $this->menuitem("translations", dispatch_url("lesson7.translation", "admin"), "lesson7");
      $this->menuitem("translations_mr", dispatch_url("lesson7.translation_mr", "admin"), "lesson7");
      $this->menuitem("translations_mr2", dispatch_url("lesson7.translation_mr2", "admin"), "lesson7");

      $this->menuitem("-", "", "lesson7");

      $this->menuitem("modulesource", moduleSourceUrl("lesson7"), "lesson7");
    }
  }

?>