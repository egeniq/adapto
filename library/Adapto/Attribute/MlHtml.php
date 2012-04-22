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
 * The Adapto_Attribute_MlHtml class is the same as a normal
 * atkMlTextAttribute. It only has a different display
 * function. For this attribute, the value is rendered as-is,
 * which means you can use html codes in the text.
 *
 * Based on atkHtmlAttribute.
 *
 * @author Peter Verhage <peter@ibuildings.nl>
 * @package adapto
 * @subpackage attributes
 *
 */
class Adapto_Attribute_MlHtml extends Adapto_MlTextAttribute
{
    /**
     * New line to BR boolean
     */
    public $nl2br = false; // defaulted to public

    /**
     * Constructor
     * @param string $name name of the attribute
     * @param int $flags flags of the attribute
     * @param bool $nl2br nl2br boolean
     */

    public function __construct($name, $flags = 0, $nl2br = false)
    {
        parent::__construct($name, $flags); // base class constructor
        $this->nl2br = $nl2br;
    }

    /**
     * Returns a displayable string for this value.
     * @param array $record Array wit fields
     * @return Formatted string
     */
    function display($record)
    {
        global $config_language;
        if ($this->nl2br)
            return nl2br($record[$this->fieldName()][$config_language[0]]);
        else
            return $record[$this->fieldName()][$config_language[0]];
    }
}
?>
