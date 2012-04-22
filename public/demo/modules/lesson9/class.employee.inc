<?php

/**
 * This lesson introduces many-to-many relations.
 *
 * The basis of implementing a many-to-many relation is 'normalization'.
 * This means that, like in the database, the relation is represented
 * by an intermediary entity. This intermediary entity has 2 manytoone
 * relations with the entitys on both sides of the manytomany relation.
 * 
 * See the file class.employeeproject.inc for the entity that links 
 * many projects to many employees.
 * 
 * For simplicity, we're using the employee class from lesson 1 as 
 * the basis.
 */
class employee extends Adapto_MetaEntity
{
    protected $table      = "lesson9_employee";   
    protected $index      = "name";
    protected $order      = "name";
    protected $flags      = array(EF_ADD_LINK);
    protected $descriptor = "[name]";
    
    public static function meta($policy)
    {
        $policy->get("name")->addFlag(AF_UNIQUE|AF_SEARCHABLE);
        $policy->get("salary")->addFlag(AF_TOTAL); 
        
        /**
         * The following code is where we add the manytomany relation.
         * The 'through' parameter tells ATK what intermediary entity
         * to use (normalization). 
         * In this case, we use a shuttle relationship. In the project
         * entity we'll demonstrate a different type of relationship.
         * Available types are: shuttle, eshuttle, select, bool, list
         * If you don't specify a type, you'd get a shuttle by default
         * but we're specifying it anyway for the sake of example.
         */
        $policy->hasMany("projects", array("type"=>"shuttle",
                                           "through"=>"employeeproject" ));
    }

    public function adminFooter()
    {
      return entitySourceUrl("lesson9.employee");
    }
}


