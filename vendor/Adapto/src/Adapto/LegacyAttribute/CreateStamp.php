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

/**
 * @internal include baseclass.
 */
useattrib('atkUpdateStampAttribute');

/**
 * Attribute for keeping track of record creation times.
 *
 * The Adapto_Attribute_CreateStamp class can be used to automatically store the
 * date and time of the creation of a record.
 * To use this attribute, add a DATETIME field to your table and add this
 * attribute to your entity. No params are necessary, no initial_values need 
 * to be set. The timestamps are generated automatically.
 * This attribute is automatically set to readonly, and to af_hide_add 
 * (because we only have the timestamp AFTER a record is added).
 *
 * (the attribute was posted at www.achievo.org/forum/viewtopic.php?p=8608)
 *
 * @author Rich Kucera <kucerar@hhmi.org>
 * @package adapto
 * @subpackage attributes
 *
 */
class Adapto_Attribute_CreateStamp extends Adapto_UpdateStampAttribute
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
        parent::__construct($name, $flags);
    }

    /**
     * This function is called by the framework to determine if the attribute
     * needs to be saved to the database in an updateDb call.
     * This attribute should never be updated
     *
     * @param array $rec The record that is going to be saved.
     * @return boolean True if this attribute should participate in the update
     *                 query; false if not.
     */
    function needsUpdate($rec)
    {
        // no matter what, we NEVER save a new value.
        return false;
    }

}

?>
