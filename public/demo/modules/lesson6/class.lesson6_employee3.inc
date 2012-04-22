<?php

  /**
   * This entity is similar to the employee 1 entity that you saw before.
   *
   * The addition in this lesson is we are going to customize it with
   * a new function called meta().
   */
  

  class lesson6_employee3 extends Adapto_MetaEntity
  {

    // To override the meta data you can use the meta() function.
    function meta(&$policy)
    {
      // With the setIncludes() function we can specify what fields we
      // want to use in the entity.
      // Ofcourse is it's also possible to do it the other way
      // around, by calling the function setExcludes(). You can only use
      // one of them at the same time.
      $policy->setIncludes("id", "name", "hiredate", "notes","salary");

      // With the addFlag function we can change the flags of a field.
      // As you can see in the code below, this can be done for one or
      // more fields.
      $policy->addFlags("id", AF_AUTOKEY);
      $policy->addFlags("name", "hiredate","salary", AF_OBLIGATORY);
      $policy->addFlags("salary",AF_TOTAL);

      // Besides adding flags, it's also possible to remove flags with
      // the removeFlags method and setting flags with the setFlags
      // method.

      // Placing an attribute or multiple attributes on tabs can be
      // done with the setTabs method. The last param is the tab name.
      $policy->setTabs("salary","hiredate","new_tab");

      // Ofcourse it's also possible to set the attribute order. All
      // attributes not mentioned will be put at the bottom using
      // their current order.
      $policy->setOrder("name","notes");
    }

    function adminFooter()
    {
      return entitySourceUrl("lesson6.lesson6_employee3");
    }
  }


?>