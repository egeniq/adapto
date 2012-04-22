<?php

/**
 * This entity is similar to the department entity of lesson 2.
 * We'll add various features to the department functionality.
 * One is the use of tabs to make the user interface cleaner.
 * 
 * Finally, we're doing something nifty to manipulate the
 * behaviour of attributes on the fly; in this case we have an
 * 'is hiring' field per department that toggles whether or not
 * new employees can be added.
 */
class department extends Adapto_MetaEntity
{
    protected $table      = "lesson3_department";
    protected $order      = "name";
    protected $flags      = array(EF_ADD_LINK);
    protected $descriptor = "[name]";
    
    /**
     * @param atkMetaPolicy $policy
     */
    public static function meta($policy)
    {
        $policy->get("name")->addFlag(AF_UNIQUE|AF_SEARCHABLE);

        $policy->hasMany("employees");

        /**
         * The next line tells ATK to put both the 'employees' and 
         * 'is_hiring' fields on the 'staff' tab.
         */
        $policy->setTab(array("is_hiring", "employees"), "staff");

    }

    public function adminFooter()
    {
      return entitySourceUrl("lesson3.department");
    }
   
    /**
     * By defining a method whose name is equal to that of an attribute, with
     * the _display postfix, we tell the system that we want to influence the
     * way names are displayed. In this example, we want to display the name
     * of departments that are hiring new employees, in bold.
     * 
     * To try out this feature, edit a department, change the checkbox of its
     * 'is hiring' field and look at how the name is bold/not bold in the
     * record list.
     */
    public function name_display($record)
    {
        /**
         * First we retrieve the original text that would be displayed if we
         * would have no override.
         */
        $nameAttribute = $this->getAttribute("name");
        $displayString = $nameAttribute->display($record);

        /**
         * Then, if is_hiring is true, we add bold tags around the
         * original text.
         */
        if ($record["is_hiring"]) {
            $displayString = '<b>'.$displayString.'</b>';
        }

        return $displayString;
    }

    /**
     * By defining a method whose name is equal to that of an attribute, we
     * can override the editing of the attribute. In this example, if
     * 'is_hiring' is not true, we want to disable the addition of new
     * employees in the list of employees for this department.
     */
    public function employees_edit($record, $mode)
    {
        /**
         * If is_hiring is not false, we disable the add functionality
         * on the target entity. This is done by retrieving the destination
         * entity (lesson3.employee), and adding the EF_NO_ADD flag to it.
         */
        $employees = $this->getAttribute("employees");

        if (!$record["is_hiring"]) {
            $entity = $employees->getDestination();
            $entity->addFlag(EF_NO_ADD);
        }
    

        /**
         * Finally, we call the original edit method, because we
         * don't want to override anything else.
         */
        return $employees->edit($record, $mode);
    }
    
    /**
     * Finally, we're adding a bit of ajax magic. Whenever the is_hiring field
     * is changed, we want the 'add employee' link to disappear right away. To 
     * accomplish this, we implement a 'postMeta' method. postMeta is what gets 
     * called once all the meta magic from the meta() function is completed, and
     * all our attributes are set up. 
     */
    public function postMeta()
    {
        /**
         * Now that everything is setup, we can retrieve the is_hiring attribute
         * and add the employees relationship as its dependee. You can try this
         * by toggling the is_hiring checkbox on the staff tab; you'll notice
         * that the 'add new employee' link disappears right away.
         */
        $this->getAttribute("is_hiring")->addDependee("employees");
    }
}