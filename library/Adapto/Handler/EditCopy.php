<?php

/**
 * This file is part of the Adapto Toolkit.
 * Detailed copyright and licensing information can be found
 * in the doc/COPYRIGHT and doc/LICENSE files which should be 
 * included in the distribution.
 *
 * @package adapto
 * @subpackage handlers
 *
 * @copyright (c)2000-2004 Ibuildings.nl BV
 * @license http://www.achievo.org/atk/licensing ATK Open Source License
 *

 */

/**
 * Handler for the 'editcopy' action of an entity. It copies the selected
 * record, and then redirects to the edit action for the copied record.
 *
 * @author petercv
 * @package adapto
 * @subpackage handlers
 *
 */
class Adapto_Handler_EditCopy extends Adapto_ActionHandler
{

    /**
     * The action method. 
     */
    function action_editcopy()
    {
        atkdebug("atkentity::action_editcopy()");

        $record = $this->getCopyRecord();
        // allowed to editcopy record?
        if (!$this->allowed($recordset)) {
            $this->renderAccessDeniedPage();
            return;
        }

        $db = &$this->m_entity->getDb();
        if (!$this->m_entity->copyDb($record)) {
            $db->rollback();
            $location = $this->m_entity->feedbackUrl("editcopy", ACTION_FAILED, $record, $db->getErrorMsg());
            $this->m_entity->redirect($location);
        } else {
            $db->commit();
            $this->clearCache();
            $location = session_url(dispatch_url($this->m_entity->atkentitytype(), "edit", array("atkselector" => $this->m_entity->primaryKey($record))),
                    SESSION_REPLACE);
            $this->m_entity->redirect($location);
        }
    }

    /**
     * Get the selected record from 
     *
     * @return the record to be copied
     */

    protected function getCopyRecord()
    {
        $selector = $this->m_postvars['atkselector'];
        $recordset = $this->m_entity->selectDb($selector, "", "", "", "", "copy");
        if (count($recordset) > 0) {
            return $recordset[0];
        }
 else {
            atkdebug("Geen records gevonden met selector: $selector");
            $this->m_entity->redirect();
        }
    }

}

?>