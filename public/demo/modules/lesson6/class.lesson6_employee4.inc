<?php

  /**
   * This entity is similar to the employee 2 entity that you saw before.
   *
   * The addition in this lesson is we are going to customize it more
   * with new policy functions.
   */

  

  class lesson6_employee4 extends Adapto_MetaEntity
  {

    function meta(&$policy)
    {
      // With the policy it's possible to get a attribute list
      // that the atkMetaEntity made of your table. You can retreive
      // the list with the getAttributes() function.
      $attrs = &$policy->getAttributes();

      // Now we have the attribute list, it's possible
      // to change an attribute. For example, defeault the
      // atkMetaEntity will detect an atkTextAttribute for
      // the notes field, but with the following 2 commands
      // you can change it into an atkAttribute and give
      // it the flag AF_BLANK_LABEL.
      $attrs["notes"]["type"] = "atkattribute";
      $attrs["notes"]["params"] = array(AF_BLANK_LABEL);

      // TODO: Write some docs
      $policy->hasOne("lesson6_department");

    }

    function adminFooter()
    {
      return entitySourceUrl("lesson6.lesson6_employee4");
    }
  }

?>