<?php

  /**
   * The department is a simple entity with an id as primary key, and a name
   * field.
   */
  userelation("atkonetomanyrelation");

  class department extends Adapto_Entity
  {
    function department()
    {
      $this->atkEntity("department", EF_ADD_LINK);

      $this->add(new Adapto_Attribute("id", AF_AUTOKEY));
      $this->add(new Adapto_Attribute("name", AF_OBLIGATORY|AF_UNIQUE|AF_SEARCHABLE));

      $this->add(new Adapto_OneToManyRelation("employees","lesson10.employee","department_id", AF_HIDE_LIST));

      $this->setOrder("name");
      $this->setTable("lesson3_department"); // reuse tables from lesson 3
    }

    function descriptor_def()
    {
      return "[name]";
    }

    function adminFooter()
    {
      return entitySourceUrl("lesson10.department");
    }
  }
?>
