<?php

/**
 * This file is part of the Adapto Toolkit.
 * Detailed copyright and licensing information can be found
 * in the doc/COPYRIGHT and doc/LICENSE files which should be
 * included in the distribution.
 *
 * The file contains the default values for most configuration
 * settings.
 *
 * @package adapto
 * @subpackage attributes
 *
 * @copyright (c)2000-2010 Ibuildings.nl BV
 * @license http://www.achievo.org/atk/licensing ATK Open Source License
 *

 */

/**
 * Attribute for editing email fields.
 *
 * @author ijansch
 * @author Maurice Maas 
 * @package adapto
 * @subpackage attributes
 *
 */
class Adapto_Attribute_Email extends Adapto_Attribute
{

    /**
     * @var boolean Bool to set DNS search in validate function
     * @access private
     */
    public $m_dnsSearch = false; // defaulted to public

    /**
     * Constructor
     *
     * <b>Example:</b>
     * <code>
     *   $this->add(new Adapto_Attribute_Email("email", false, AF_OBLIGATORY));
     * </code>
     *
     * @param String $name Name of the attribute
     * @param boolean $search Search DNS for MX records in validate function
     * @param int $flags Flags for the attribute
     * @param int $size The size of the field in characters
     */

    public function __construct($name, $search = false, $flags = 0, $size = 0)
    {
        $this->m_dnsSearch = $search;
        parent::__construct($name, $flags, $size);
    }

    /**
     * Returns a displayable string for this value.
     * @param array $record The record to display
     * @param String $mode The display mode ("view" for viewpages, or "list"
     *                     for displaying in recordlists, "edit" for
     *                     displaying in editscreens, "add" for displaying in
     *                     add screens. "csv" for csv files. Applications can
     *                     use additional modes.
     * @return String
     */
    function display($record, $mode = "")
    {
        if ($mode == "csv")
            return parent::display($record, $mode);

        if (isset($record[$this->fieldName()]) && $record[$this->fieldName()] != "") {
            return '<a href="mailto:' . $record[$this->fieldName()] . '">' . $record[$this->fieldName()] . '</a>';
        }
        return '';
    }

    /**
     * Validates email address through regular expression and dns check
     * @param array $record Record that contains value to be validated.
     *                      Errors are saved in this record, in the 'atkerror'
     *                      field.
     * @param String $mode Validation mode. Can be either "add" or "update"
     */
    function validate(&$record, $mode)
    {
        $email = $record[$this->fieldName()];
        //first check complete string
        if (!Adapto_Attribute_Email::validateAddressSyntax($email)) {
            atkTriggerError($record, $this, 'error_invalid_email');
        } else {
            if ($this->m_dnsSearch) {
                //now check if domain exists, searches DNS for MX records
                list($username, $domain) = explode('@', $email, 2);
                if (!(Adapto_Attribute_Email::validateAddressDomain($domain, false))) {
                    triggerError($record, $this->fieldName(), 'error_unkown_domain', text('error_unkown_domain') . " " . $domain);
                }
            }
        }
    }

    /**
     * Checks e-mail address syntax against a regular expression.
     *
     * @param  string $email e-mail address.
     * @return boolean e-mailaddress syntactically valid or not.
     * @static
     */
    function validateAddressSyntax($email)
    {
        if (preg_match("/^[-_a-zA-Z0-9+]+(\.[-_a-zA-Z0-9+]+)*@([0-9a-z-]+\.)*([0-9a-z][0-9a-z-]*[0-9a-z]\.)+[a-z]{2,}$/", $email)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check if the domain is valid and exists.
     *
     * The strict parameter decides if the MX record gets checked.
     *
     * @param string $domain
     * @param boolean $strict
     * @return boolean $result
     * @static
     */
    function validateAddressDomain($domain, $strict = false)
    {
        if ($strict) {
            $rr = 'MX';
        } else {
            $rr = 'ANY';
        }
        //Check if this domain has an MX host.
        if (checkdnsrr($domain, $rr)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Called by the framework to determine the database field datatype.
     * @return String The databasefield datatype.
     */
    function dbFieldType()
    {
        return "string";
    }

}

if (!function_exists("checkdnsrr")) {

    /**
     * Check an e-mail do main in DNS using nslookup.
     *
     * This is only used on Windows as on Linux environments this function
     * is native in PHP.
     * @access private
     */
    function checkdnsrr($hostName, $recType = 'MX')
    {
        if (!empty($hostName)) {
            $recType = escapeshellarg($recType);
            $hostNameArg = escapeshellarg($hostName);
            exec("nslookup -type=$recType $hostNameArg", $result);
            // check each line to find the one that starts with the host
            // name. If it exists then the function succeeded.
            foreach ($result as $line) {
                if (eregi("^$hostName", $line)) {
                    return true;
                }
            }
            // otherwise there was no mail handler for the domain
            return false;
        }
        return false;
    }
}

?>
