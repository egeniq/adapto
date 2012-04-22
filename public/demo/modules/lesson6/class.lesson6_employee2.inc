<?php

  /**
   * This entity is similar to the employee 1 entity that you saw before.
   *
   * The addition in this lesson is we are again going to customize
   * it like in employee 3, but now like the 'old' fashion way.
   */
  
  userelation("atkManyToOneRelation");

  class lesson6_employee2 extends Adapto_MetaEntity
  {
    // Instead of creating a meta function, we now add
    // a normal constructor.
    function Lesson6_Employee2()
    {
      // In the constructor we call the atkMetaEntity which will
      // generate the complete entity.
      $this->atkMetaEntity();

      // To override a field, it's now a matter of adding
      // an attribute to the entity, like in lesson 1.
      $this->add(new Adapto_Attribute("notes", AF_BLANK_LABEL));

      $this->add(new Adapto_ManyToOneRelation("lesson6_department_id","lesson6.lesson6_department", AF_SEARCHABLE));
    }

    function adminFooter()
    {
      return entitySourceUrl("lesson6.lesson6_employee2");
    }
  }

?>