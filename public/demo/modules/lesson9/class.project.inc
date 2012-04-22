<?php

/**
 */

class project extends Adapto_MetaEntity
{
    protected $table = "lesson9_project";
    protected $flags = array(EF_ADD_LINK);
    protected $index = "name";
    protected $order = "name";
    protected $descriptor = "[name]";
    
    public static function meta($policy)
    {
        $policy->get("name")->addFlag(AF_SEARCHABLE|AF_UNIQUE);
        
        /**
         * The select relation is a manytomany type relation represented
         * by a set of selected records and a selection field. Like in 
         * the class.employee.inc example in this module, the parameters 
         * represent the intermediary entity and the relationship type.
         */
        $policy->hasMany("employees", array("through"=>"employeeproject",
                                            "type"=>"select"));
    }

    function adminFooter()
    {
      return entitySourceUrl("lesson9.project");
    }
  }
