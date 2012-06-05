<?php
/**
 * This file is part of the Adapto Toolkit.
 * Detailed copyright and licensing information can be found
 * in the doc/COPYRIGHT and doc/LICENSE files which should be
 * included in the distribution.
 *
 * @package adapto
 * @subpackage utils
 *
 * @copyright (c)2005 Ibuildings.nl BV
 * @license http://www.achievo.org/atk/licensing ATK Open Source License
 *

 */

/**
 * The Adapto_Util_TriggerListener base class for handling trigger events on records.
 *
 * The most useful purpose of the Adapto_Util_TriggerListener is to serve as a base
 * class for custom trigger listeners. Extend this class and implement
 * postUpdate, preDelete etc. functions that will automatically be called
 * when such a trigger occurs. For more flexibility, override only
 * the notify($trigger, $record) method which catches every trigger. 
 * Using atkEntity::addListener you can add listeners that catch evens such as 
 * records updates and additions.
 * This is much like the classic atk postUpdate/postAdd triggers, only much
 * more flexible.
 *
 * @author Martin Roest <martin@ibuildings.nl>
 * @author petercv
 * @package adapto
 * @subpackage utils
 */
class Adapto_Util_TriggerListener
{
    /**
     * The owning entity of the listener.
     * @access private
     * @var atkEntity
     */
    public $m_entity = NULL; // defaulted to public

    /**
     * Base constructor.
     * 
     * @return Adapto_Util_TriggerListener
     */

    public function __construct()
    {
    }

    /**
     * Set the owning entity of the listener.
     *
     * When using atkEntity::addListener to add a listener to an entity it is not
     * necessary to call this method as addListener will do that for you.
     *
     * @param atkEntity $entity The entity to set as owner
     */
    function setEntity(&$entity)
    {
        $this->m_entity = &$entity;
    }

    /**
     * Notify the listener of any action on a record.
     *
     * This method is called by the framework for each action called on a
     * entity. Depending on the actionfilter passed in the constructor, the
     * call is forwarded to the actionPerformed($action, $record) method.
     *
     * @param String $trigger The trigger being performed
     * @param array $record The record on which the trigger is performed
     * @param string $mode The mode (add/update)
     * @return boolean Result of operation.
     */
    function notify($trigger, &$record, $mode = NULL)
    {
        if (method_exists($this, $trigger)) {
            Adapto_Util_Debugger::debug("Call listener " . get_class($this) . " for trigger $trigger on " . $this->m_entity->atkEntityType() . " ("
                    . $this->m_entity->primaryKey($record) . ")");
            return $this->$trigger($record, $mode);
        } else {
            return true;
        }
    }
}
?>