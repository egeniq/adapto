<?php

  /**
   * This file contains the definition of an atkMetaEntity. So what is an
   * atkMetaentity? well it's an entity that uses the meta data of the database
   * to automaticly add the attribute for the fields it find.
   *
   * First we have to import the atkMetaEntity so we can extend our new class.
   */
  

  /**
   * To create a module like we did in lesson 1 for the employees, we now
   * only need 1 line of code and that's the class definition. The atkMetaentity
   * will look for a table called the same as the classname and then it will
   * build the entity for you. In this example we have more than 1 line because
   * we also want a link to show the source of this entity.
   */
  class lesson6_employee1 extends Adapto_MetaEntity
  {
    function adminFooter()
    {
      return entitySourceUrl("lesson6.lesson6_employee1");
    }
  }

?>