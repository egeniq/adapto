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
 * Handler class for the feedback action of an entity. The handler draws a
 * screen with a message, giving the user feedback on some action that
 * occurred.
 *
 * @author ijansch
 * @package adapto
 * @subpackage handlers
 *
 */
class Adapto_Handler_Feedback extends Adapto_ActionHandler
{

    /**
     * The action handler method.
     */
    function action_feedback()
    {
        $page = &$this->getPage();
        $output = $this->invoke("feedbackPage", $this->m_postvars["atkfbaction"], $this->m_postvars["atkactionstatus"], $this->m_postvars["atkfbmessage"]);
        $page->addContent($this->m_entity->renderActionPage("feedback", $output));
    }

    /**
     * The method returns a complete html page containing the feedback info.
     * @param String $action The action for which feedback is provided
     * @param int $actionstatus The status of the action for which feedback is
     *                          provided
     * @param String $message An optional message to display in addition to the
     *                        default feedback information message.
     *
     * @return String The feedback page as an html String.
     */
    function feedbackPage($action, $actionstatus, $message = "")
    {
        $entity = &$this->m_entity;
        $ui = &$this->getUi();

        $entity->addStyle("style.css");

        $params["content"] = atktext('feedback_' . $action . '_' . atkActionStatus($actionstatus), $entity->m_module, $entity->m_type) . ' <br>' . $message;

        if (atkLevel() > 0) {
            $params["formstart"] = '<form method="get">' . session_form(SESSION_BACK);
            $params["buttons"][] = '<input type="submit" class="btn_cancel" value="&lt;&lt; ' . atktext('back') . '">';
            $params["formend"] = '</form>';
        }

        $output = $ui->renderAction($action, $params);

        return $ui->renderBox(array("title" => $entity->actionTitle($action), "content" => $output));

    }
}

?>