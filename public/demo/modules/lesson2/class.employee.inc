<?php

/**
 * This entity is a copy of the employee entity from lesson 1. (ofcourse, in ATK
 * it would actually not be necessary to copy, inheritance could be used
 * instead. For the clarity of the example though, a fresh entity is used
 * here.)
 *
 * New are the many-to-one relations.
 */

class employee extends Adapto_MetaEntity
{
    protected $table      = "lesson2_employee";   
    protected $index      = "name";
    protected $order      = "name";
    protected $flags      = array(EF_ADD_LINK);
    protected $descriptor = "[name]";
    
    public static function meta($policy)
    {
        $policy->get("name")->addFlag(AF_UNIQUE|AF_SEARCHABLE);
        $policy->get("salary")->addFlag(AF_TOTAL);

        /**
         * The first manytoone relation is a N:1 association to department. Many
         * employees can be in one department. One department can contain many
         * employees.
         * By adding a single line of code, we enable N:1 functionality in this
         * entity. The effect is a dropdown from which the user can choose a
         * department.
         *
         * We don't need any parameters, ATK figures out everything on its own.
         * 
         * Finally notice the chaining of the addFlag here; using this we can
         * add flags to the relation, in this case we make sure that the user
         * can search departments.
         */
        $policy->hasOne("department")->addFlag(AF_SEARCHABLE);

        /**
         * The second manytoone we add is a recursive parent/child relationship.
         * Employees have a manager. Ofcourse, a manager is an employee too.
         * 
         * In this case, we have to help ATK a bit. The department relationship 
         * automagically works, because ATK finds a department_id field in the 
         * database it can work with. There is however no employee_id field. 
         * The database has a manager_id, so we have to tell ATK to use that
         * for the relationship, using the source parameter.
         * 
         */
        $policy->hasOne("employee", array("source"=>"manager_id"))->addFlag(AF_SEARCHABLE);
 
    }


    public function adminFooter()
    {
      return entitySourceUrl("lesson2.employee");
    }
}

