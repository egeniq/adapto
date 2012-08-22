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

// Load used attribute class
useattrib("atktextattribute");

/**
 * The Adapto_Attribute_Html class is the same as a normal atkAttribute. It only
 * (has a different display function. For this attribute, the value is
 * rendered as-is, which means you can use html codes in the text.
 *
 * There might me times where you want the user to be able to use html tags,
 * but you don't want to have the inconvenience of using br's for each line.
 * For this reason the constructor accepts a parameter which tells it to do
 * a newline-to-br conversion.
 *
 * @author ijansch
 * @package adapto
 * @subpackage attributes
 *
 */
class Adapto_Attribute_Html extends Adapto_TextAttribute
{
    /**
     * New line to BR boolean
     */
    public $nl2br = false; // defaulted to public

    /**
     * Constructor
     * @param string $name Name of the attribute
     * @param int $flags Flags of the attribute
     * @param bool $nl2br nl2br boolean
     */

    public function __construct($name, $flags = 0, $nl2br = false)
    {
        $this->atkAttribute($name, $flags); // base class constructor
        $this->nl2br = $nl2br;
    }

    /**
     * Returns a displayable string for this value.
     * @param array $record Array wit fields
     * @return Formatted string
     */
    function display($record)
    {
        if ($this->nl2br)
            return nl2br($record[$this->fieldName()]);
        else
            return $record[$this->fieldName()];
    }
}
?>
