<?php

  
  useattrib("atkboolattribute");
  userelation("atkonetomanyrelation");

  class lesson6_department extends Adapto_MetaEntity
  {
    function lesson6_department()
    {
      $this->atkMetaEntity();
      $this->add(new Adapto_BoolAttribute("is_hiring"));
    }

    function name_display($record)
    {
      $nameattr = &$this->getAttribute("name");
      $org = $nameattr->display($record);

      if ($record["is_hiring"])
      {
        $org = '<b>'.$org.'</b>';
      }

      return $org;
    }

    function employees_edit($record, $mode)
    {

      $employees = &$this->getAttribute("employees");

      if (!$record["is_hiring"])
      {
        $entity = &$employees->getDestination();
        {
          $entity->addFlag(EF_NO_ADD);
        }
      }

      return $employees->edit($record, $mode);
    }

    function descriptor_def()
    {
      return "[name]";
    }

    function adminFooter()
    {
      return entitySourceUrl("lesson6.lesson6_department");
    }
  }
?>