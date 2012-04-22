<?php
/**
 * This file is part of the Adapto Toolkit.
 * Detailed copyright and licensing information can be found
 * in the doc/COPYRIGHT and doc/LICENSE files which should be 
 * included in the distribution.
 *
 * @package adapto
 * @subpackage attributes
 *
 * @copyright (c)2000-2004 Ibuildings.nl BV
 * @license http://www.achievo.org/atk/licensing ATK Open Source License
 *

 */

/**
 * @internal include baseclass.
 */
useattrib('atkDateTimeAttribute');

/**
 * Attribute for keeping track of last-modification times.
 *
 * The Adapto_Attribute_UpdateStamp class can be used to automatically store the
 * date and time of the last modification of a record.
 * To use this attribute, add a DATETIME field to your table and add this
 * attribute to your entity. No params are necessary, no initial_values need 
 * to be set. The timestamps are generated automatically.
 * This attribute is automatically set to readonly, and to af_hide_add 
 * (because we only have the first timestamp AFTER a record is added).
 *
 * @author ijansch
 * @package adapto
 * @subpackage attributes
 *
 */
class Adapto_Attribute_UpdateStamp extends Adapto_DateTimeAttribute
{
    /**
     * Constructor
     *
     * @param String $name Name of the attribute (unique within an entity, and
     *                     corresponds to the name of the datetime field 
     *                     in the database where the stamp is stored.
     * @param int $flags Flags for the attribute.
     */

    public function __construct($name, $flags = 0)
    {
        parent::__construct($name, date("Y-m-d"), date("H:i:s"), $flags | AF_READONLY | AF_HIDE_ADD);
        $this->setForceInsert(true);
        $this->setForceUpdate(true);
        $this->setInitialValue(atkDateTimeAttribute::datetimeArray());
    }

    /**
     * Value to DB.
     * 
     * @param array $record The record
     * @return The value to store in the database
     */
    function value2db($record)
    {
        // if record not created using a form this situation can occur, so set the value here
        // Every time we must overwrite the value of this attribute, because this is UPDATE stamp
        $record[$this->fieldName()] = $this->initialValue();
        return parent::value2db($record);
    }

    /**
     * Override the initial value
     *
     * @return Array
     */
    function initialValue()
    {
        return atkDateTimeAttribute::datetimeArray();
    }

    /**
     * Returns a piece of html code for hiding this attribute in an HTML form,
     * while still posting its value. (<input type="hidden">)
     *
     * @param array $record The record that holds the value for this attribute
     * @param String $fieldprefix The fieldprefix to put in front of the name
     *                            of any html form element for this attribute.
     * @return String A piece of htmlcode with hidden form elements that post
     *                This attribute's value without showing it.
     */
    function hide($record = "", $fieldprefix)
    {
        $field = $record[$this->fieldName()];
        $result = "";
        if (is_array($field))
            foreach ($field as $key => $value)
                $result .= '<input type="hidden" name="' . $fieldprefix . $this->formName() . '[' . $key . ']" ' . 'value="' . $value . '">';

        else
            $result = '<input type="hidden" name="' . $fieldprefix . $this->formName() . '" value="' . $field . '">';

        return $result;
    }

    /**
     * Retrieve the list of searchmodes supported by the attribute. 
     *
     * @return array Array containing "exact" as the only supported 
     *               searchmode.
     */
    function getSearchModes()
    {
        // exact match and substring search should be supported by any database.
        // (the LIKE function is ANSI standard SQL, and both substring and wildcard
        // searches can be implemented using LIKE)
        // Possible values
        //"regexp","exact","substring", "wildcard","greaterthan","greaterthanequal","lessthan","lessthanequal"
        return array("exact");
    }

    /**
     * We always have a value, even if we're not even in the record
     * @return boolean false
     * */
    function isEmpty($record)
    {
        return false;
    }
}

?>