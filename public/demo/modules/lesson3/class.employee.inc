<?php

/**
 * This entity is similar to the employee entity of lesson 2.
 *
 * The addition in this lesson is a little bit of code that checks the
 * salary of someone's manager. If someone earns more than his manager, the
 * row is colored red as a warning. We'll also add some other bits and 
 * pieces to improve this application.
 */
class employee extends Adapto_MetaEntity
{
    protected $table      = "lesson3_employee";   
    protected $index      = "name";
    protected $order      = "name";
    protected $flags      = array(EF_ADD_LINK);
    protected $descriptor = "[name]";
    
    /**
     * @param atkMetaPolicy $policy
     */
    public static function meta($policy)
    {
        $policy->get("name")->addFlag(AF_UNIQUE|AF_SEARCHABLE);
        
        /**
         * Remember how we had a salary attribute with an AF_TOTAL flag?
         * As you can see in lesson 1 and 2, the field automatically only
         * accepts numbers because it's a numeric field in the database.
         * 
         * We can customize this a bit though. ATK features many different
         * attributes in its atk/attributes/ directory, and one of them is 
         * the atkCurrencyAttribute which is more suitable for monetary 
         * values.
         * 
         * Using setTypeAndParams we tell ATK to use the atkcurrencyattribute
         * for this field. The params can be derived by looking at the 
         * atkCurrencyAttribute constructor documentation. We can see the params
         * $name, $flags, $size, $currency. Using setTypeAndParams we can leave
         * out the name because we already have a name; we only need to specify 
         * the other three. We pass AF_TOTAL for the flags, 10 for the size, 
         * and EUR for the currency. The addFlag we originaly had in lesson2
         * is no longer necessary if we pass the flag like this.  
         */
        $policy->get("salary")
            ->setTypeAndParams("atk.attributes.atkcurrencyattribute", 
                               array(AF_TOTAL, 10, "EUR"));

        $policy->hasOne("department")->addFlag(AF_SEARCHABLE);

        /**
         * Another cool feature we're introducing is autocompletion. By adding
         * the AF_RELATION_AUTOCOMPLETE flag to the relationship for the manager,
         * the dropdown is replaced by an autocomplete field where the user
         * can enter a couple of characters and the system automatically 
         * looks them up.
         */
        $policy->hasOne("employee", array("source"=>"manager_id"))->addFlag(AF_SEARCHABLE|AF_RELATION_AUTOCOMPLETE);

        /**
         * With the next line, we demonstrate how you can rearrange fields, if 
         * you don't want to rely on the database order. 
         * Note that we use 'department_id' and manager_id here. Even though 
         * we say hasOne("department"), the actual field is still called 
         * department_id.
         * Hint: get() supports multiple fields, so you could also say
         * $policy->get("field1", "field2")->insertAfter("field4"); to 
         * reshuffle multiple fields at the same time.
         */
        $policy->get("department_id")->insertBefore("manager_id"); 
        
        /**
         * In the department entity of lesson 3, we saw the use of tabs to 
         * distribute fields. In this entity we'll introduce sections.
         * In this case, we add the salary and hiredate fields to a 
         * section called 'contractinfo'. You'll notice that the user
         * can now collapse and expand these fields.
         * Note the dot: setSection is a multipurpose function that can
         * be used to indicate tabs as well:
         * - "address"; places the attributes on the address tab
         * - ".address"; places the attributes in an address section
         * - "address.billing"; places the fields in the billing section
         *                      of the address tab.
         */
        $policy->setSection(array("salary", "hiredate"), ".contractinfo");
    }


    public function adminFooter()
    {
      return entitySourceUrl("lesson3.employee");
    }
    
    /**
     * The final feature we're demonstraing in lesson 3 is adding 
     * business logic, in this case giving a row a different color based on
     * some condition. In this case, we'll flag an employee if his salary is
     * higher than that of his manager.
     * We can do this by implementing the rowcolor method, which is one of the
     * hooks ATK supports. The current record is passed as a parameter.
     * 
     * With this function implemented, look at the recordlist when editing
     * employees. Also note, that in the department screen, on the staff tab,
     * you'll see the effects of this as well; this is reuse of business logic! 
     */
    public function rowColor($record)
    {
        /**
         * First we get the manager id from the current record. Since manager is
         * a relation, the manager_id is an associative array of fields from the
         * manager table. Usually, only the primary key fields are loaded.
         * Note that while so far we have called the relationship hasOne("employee"),
         * for features such as this we're going to have to use the actual field 
         * names.
         */
        $manager_id = $record["manager_id"]["id"];

        /**
         * If this employee has a manager..
         */
        if ($manager_id!="") {

            /**
             * First get the salary of the current record.
             */
            $salary = $record["salary"];

            /**
             * Now we use ATK's database abstraction layer to load the salary 
             * of the manager. This is a way of retrieving data without having 
             * to write actual SQL queries. This way, the application remains
             * database vendor independent. We do the select on the entity
             * that the manager relation points to.
             */
            $managerEntity = $this->getAttribute("manager_id")->getDestination();

            /**
             * The 'where clause' is passed in the select method, which returns
             * an atkSelector. The includes method of atkSelector can be used
             * to tell ATK that we only want to retrieve a specific set of
             * fields (only one in this case). getFirstRow then retrieves the 
             * first row of the resultset. We could use getAllRows but we know
             * there will be only one manager record.
             */
            $managerRecord = $managerEntity->select("id=$manager_id")->includes("salary")->getFirstRow();
        
            $managerSalary = $managerRecord["salary"];
        

            /**
             * Finally, we make the comparison. If the current employee earns more
             * than his manager, the rowcolor is set to red by returning the html
             * color value for red. If we return nothing (all other cases), the
             * default row color will be used.
             */
            if ($salary > $managerSalary) return "#ff0000";
        }
    }   
    
}


