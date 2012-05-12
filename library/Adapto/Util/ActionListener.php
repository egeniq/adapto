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
 * The Adapto_Util_ActionListener baseclass for handling ATK events.
 *
 * The most useful purpose of the Adapto_Util_ActionListener is to serve as a base
 * class for custom action listeners. Extend this class and override only
 * the notify($action, $record) method. Using atkEntity::addListener you can
 * add listeners that catch evens such as records updates and additions.
 * This is much like the classic atk postUpdate/postAdd triggers, only much
 * more flexible.
 *
 * @author ijansch
 * @package adapto
 * @subpackage utils
 */
class Adapto_Util_ActionListener
{
    /**
     * The list of actions the action listener should listen to.
     * @access private
     * @var Array
     */
    public $m_actionfilter = array(); // defaulted to public

    /**
     * The owning entity of the listener.
     * @access private
     * @var atkEntity
     */
    public $m_entity = NULL; // defaulted to public

    /**
     * Base constructor
     *
     * @param array $actionfilter The list of actions to listen to
     * @return Adapto_Util_ActionListener
     */

    public function __construct($actionfilter = array())
    {
        $this->m_actionfilter = $actionfilter;
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
     * @param String $action The action being performed
     * @param array $record The record on which the action is performed
     */
    function notify($action, $record)
    {
        if (count($this->m_actionfilter) == 0 || Adapto_in_array($action, $this->m_actionfilter)) {
            Adapto_Util_Debugger::debug("Action $action performed on " . $this->m_entity->atkEntityType() . " (" . $this->m_entity->primaryKey($record) . ")");
            $this->actionPerformed($action, $record);
        }
    }

    /**
     * Notify the listener of an action on a record.
     *
     * This method should be overriden in custom action listeners, to catch
     * the action event.
     * @abstract
     * @param String $action The action being performed
     * @param array $record The record on which the action is performed
     */
    function actionPerformed($action, $record)
    {
    }

    /**
     * Notify the listener of any action about to be performed on a record.
     *
     * This method is called by the framework for each action called on a
     * entity. Depending on the actionfilter passed in the constructor, the
     * call is forwarded to the preActionPerformed($action, $record) method.
     *
     * @param String $action The action about to be performed
     * @param array $record The record on which the action is about to be performed
     */
    function preNotify($action, &$record)
    {
        if (count($this->m_actionfilter) == 0 || Adapto_in_array($action, $this->m_actionfilter)) {
            Adapto_Util_Debugger::debug("Action $action to be performed on " . $this->m_entity->atkEntityType() . " (" . $this->m_entity->primaryKey($record) . ")");
            $this->preActionPerformed($action, $record);
        }
    }

    /**
     * Notify the listener of an action about to be performed on a record.
     *
     * This method should be overriden in custom action listeners, to catch
     * the action event.
     * @abstract
     * @param String $action The action about to be performed
     * @param array $record The record on which the action is about to be performed
     */
    function preActionPerformed($action, &$record)
    {
    }

}

?>
