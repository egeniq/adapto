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
 * @copyright (c)2006 Ibuildings.nl BV
 * @license http://www.achievo.org/atk/licensing ATK Open Source License
 *
 */

/** @internal includes **/

useattrib("atklistattribute");

/**
 * The atkTimeZone class represents an attribute to handle timezones in a listbox.
 *
 * @author Dennis Luitwieler <dennis@ibuildings.nl>
 * @package adapto
 * @subpackage attributes
 */
class Adapto_Attribute_TimeZone extends Adapto_ListAttribute
{
    public $m_defaulttocurrent = true; // defaulted to public

    /**
     * Constructor
     *
     * <b>Example:</b>
     *        $this->add(new Adapto_Attribute_TimeZone("timezone",AF_OBLIGATORY));
     * @param string $name Name of the attribute
     * @param int $flags Flags for the attribute
     */

    public function __construct($name, $flags = 0)
    {
        $optionsArray = array('timezone_utc_-1200', 'timezone_utc_-1100', 'timezone_utc_-1000', 'timezone_utc_-0900', 'timezone_utc_-0800',
                'timezone_utc_-0700', 'timezone_utc_-0600', 'timezone_utc_-0500', 'timezone_utc_-0400', 'timezone_utc_-0300', 'timezone_utc_-0200',
                'timezone_utc_-0100', 'timezone_utc_+0000', 'timezone_utc_+0100', 'timezone_utc_+0200', 'timezone_utc_+0300', 'timezone_utc_+0400',
                'timezone_utc_+0500', 'timezone_utc_+0600', 'timezone_utc_+0700', 'timezone_utc_+0800', 'timezone_utc_+0900', 'timezone_utc_+1000',
                'timezone_utc_+1100', 'timezone_utc_+1200', 'timezone_utc_+1300',);

        $valuesArray = array('-1200', '-1100', '-1000', '-0900', '-0800', '-0700', '-0600', '-0500', '-0400', '-0300', '-0200', '-0100', '+0000', '+0100',
                '+0200', '+0300', '+0400', '+0500', '+0600', '+0700', '+0800', '+0900', '+1000', '+1100', '+1200', '+1300',);

        parent::__construct($name, $optionsArray, $valuesArray, $flags, 0);
    }

    /**
     * Returns the UTC offset in seconds for a value of the timezone attribute.
     *
     * @param string $value
     * @param timestamp $timestamp
     * @return int UTC offset in seconds
     *
     * @static
     */
    function getUTCOffset($value, $timestamp = null)
    {
        if ($value === null) {
            return 0;
        } else {
            list($sign, $hours, $minutes) = sscanf($value, '%1s%2d%2d');
            return ($sign == '+' ? 1 : -1) * ($hours * 60 * 60) + ($minutes * 60);
        }
    }
}
?>
