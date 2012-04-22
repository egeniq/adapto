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
 * Handler for the 'tcopy' action of an entity. It copies the selected
 * record, and then redirects back to the calling page.
 *
 * @author ijansch
 * @package adapto
 * @subpackage handlers
 *
 */
class Adapto_Handler_Copy extends Adapto_ActionHandler
{

    /**
     * The action handler.
     * 
     * @param Bool $redirect
     */
    function action_copy($redirect = true)
    {
        $this->invoke("entityCopy");
    }

    /**
     * Copies a record, based on parameters passed in the url.
     */
    function entityCopy()
    {
        atkdebug("Adapto_Handler_Copy::entityCopy()");
        $recordset = $this->m_entity->selectDb($this->m_postvars['atkselector'], "", "", "", "", "copy");
        $db = &$this->m_entity->getDb();
        if (count($recordset) > 0) {
            // allowed to copy record?
            if (!$this->allowed($recordset[0])) {
                $this->renderAccessDeniedPage();
                return;
            }

            if (!$this->m_entity->copyDb($recordset[0])) {
                atkdebug("atkentity::action_copy() -> Error");
                $db->rollback();
                $location = $this->m_entity->feedbackUrl("save", ACTION_FAILED, $recordset[0], $db->getErrorMsg());
                atkdebug("atkentity::action_copy() -> Redirect");
                $this->m_entity->redirect($location);
            } else {
                $db->commit();
                $this->notify("copy", $recordset[0]);
                $this->clearCache();
            }
        }
        $this->m_entity->redirect();
    }
}

?>