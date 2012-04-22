<?php

  useattrib("atktextattribute");
  useattrib("atkdateattribute");
  useattrib("atknumberattribute");
  userelation("atkmanytoonerelation");

  class employee extends Adapto_Entity
  {
    function employee()
    {
      $this->atkEntity("employee", EF_ADD_LINK);

      $this->add(new Adapto_Attribute("id", AF_AUTOKEY));
      $this->add(new Adapto_Attribute("name", AF_OBLIGATORY|AF_UNIQUE|AF_SEARCHABLE));

      $dpt = &$this->add(new Adapto_ManyToOneRelation("department_id","lesson10.department", AF_SEARCHABLE));

      $mgr = &$this->add(new Adapto_ManyToOneRelation("manager_id","lesson10.employee", AF_SEARCHABLE));
      $mgr->addDestinationFilter("department_id = '[department_id.id]' AND id<>'[id]'");
     
      $dpt->addDependee("manager_id");


      $this->add(new Adapto_DateAttribute("hiredate"));
      $this->add(new Adapto_NumberAttribute("salary", AF_TOTAL));
      $this->add(new Adapto_TextAttribute("notes", 0, AF_HIDE_LIST));

      $this->setOrder("name");
      $this->setIndex("name");
      $this->setTable("lesson3_employee"); // reuse tables from lesson3
    }

    function descriptor_def()
    {
      return "[name]";
    }

    function adminFooter()
    {
      return entitySourceUrl("lesson10.employee");
    }

  }


?>