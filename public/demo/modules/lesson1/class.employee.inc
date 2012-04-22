<?php

/**
 * This file contains the definition of an entity. It describes our basic 
 * business entity. It contains hints for the userinterface of the admin 
 * screens and other feature definitions.
 *
 * Note how very little code (approx. 10 lines, the rest is comments) can
 * generate a complete administration screen for employee records.
 */

/**
 * A entity usually represents one table in the database. In this case, we
 * define the entity for employees, to generate the CRUD app for managing
 * employees. 
 */
class employee extends Adapto_MetaEntity
{
    /**
     * The next line tells ATK which table this entity represents.
     * (It is good practice to prefix the tablename with the name of the
     * module, since entitys from different modules may have the same name,
     * but table names must be unique). If we leave out the $table 
     * property, ATK assumes that the tablename is the same as the
     * classname, 'employee' in this case. Since we're using prefixed
     * tables, we're adding the $table property.
     */
    protected $table = "lesson1_employee";
    
    /**
     * The next line indicates that the "name" field can be used to generate
     * an alphabetical index in the admin screen.
     */
    protected $index = "name";
    
    /**
     * The following line sets the default order for this entity. The order
     * can be adjusted by the user by clicking the column heading.
    */
    protected $order = "name";
    
    /**
     * Some behaviour can be influenced by adding so-called 'entity flags' to our
     * entity. In this case, we add the EF_ADD_LINK. This means that in admin screens
     * we don't want to add records in the same screen, we want a link to a separate
     * add screen instead. (Remove the flag and browse the app to see the difference
     */
    protected $flags = array(EF_ADD_LINK);
    
    /**
     * With the descriptor, we can tell ATK how generally a record from this entity is
     * displayed. Employees are displayed by their name, so we use the name field as
     * $descriptor. To see this in action, edit an employee and look at the title
     * of the box. It will say Employee [Bill] indicating that we're editing bill.
     * If you would remove the $descriptor, it would simply say Employee [3] because
     * it falls back to using the database id if we don't tell it what field to use.
     * The brackets are used because this is a template, containing fieldnames between
     * square brackets. An example of a more complex $descriptor could be:
     * protected $descriptor = "[name] ([city])";
     * This results in the display of 'Jack (New York)' when editing employee
     * Jack from New York.
     */
    protected $descriptor = "[name]";
    
    /**
     * ATK builds the entire application automatically. Howeer, we can change the 'policy'
     * ATK is using to determine the app's behaviour. We do this by implementing the meta
     * method, that gives us the $policy so we can change things. This method should 
     * always be static.
     *  
     * @param atkMetaPolicy $policy
     */
    public static function meta($policy)
    {
        /**
         * With the next line, we retrieve the 'name' field from the default policy, and add a 
         * flag to it, to make this field searchable. In the app, you'll see a search box 
         * on top of the 'name' column. This flag is what causes that.
         * We also add the AF_UNIQUE flag to make the app force the user to enter unique
         * employees only. Note how we can concatenate multiple flags (they are bitwise).
         * 
         * How would you know by the way that there is a 'name' field? Simply look at 
         * your database structure, this is where ATK gets its fields from.
         */
        $policy->get("name")->addFlag(AF_SEARCHABLE|AF_UNIQUE);
        
        /**
         * Here's another example of adding flags to attributes; we're telling ATK that the 
         * salary field should be totalled.
         */
        $policy->get("salary")->addFlag(AF_TOTAL);
    }

    /**
     * The adminFooter method is called by ATK to check if you have custom
     * code that you want to display below the list of records. There's also
     * an adminHeader method to put things above the list.
     *
     * In this case, the footer is used to display a link to this sourcefile,
     * nicely highlighted. The method used here is provided in the module.inc
     * file of the lesson_utils module.
     */
    public function adminFooter()
    {
        return entitySourceUrl("lesson1.employee");
    }
}

