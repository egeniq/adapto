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
 * @copyright (c)2000-2004 Ivo Jansch
 * @license http://www.achievo.org/atk/licensing ATK Open Source License
 *

 */

/**
 * Handler class for the select action of an entity. The handler draws a
 * generic select form for searching through the records and selecting
 * one of the records.
 *
 * @author ijansch
 * @author petercv
 * @package adapto
 * @subpackage handlers
 */
class Adapto_Handler_Select extends Adapto_ActionHandler
{
    /**
     * The action handler method.
     */

    public function action_select()
    {
        if (!empty($this->m_partial)) {
            $this->partial($this->m_partial);
            return;
        }

        $output = $this->invoke("selectPage");

        if ($output != "") {
            $this->getPage()->addContent($this->getEntity()->renderActionPage("select", $output));
        }
    }

    /**
     * This method returns an html page containing a recordlist to select
     * records from. The recordlist can be searched, sorted etc. like an
     * admin screen.
     *
     * @return String The html select page.
     */

    public function selectPage()
    {
        $entity = $this->getEntity();
        $entity->addStyle("style.css");

        $grid = atkDataGrid::create($entity, 'select');
        $actions = array('select' => atkurldecode($grid->getPostvar('atktarget')));
        $grid->removeFlag(atkDataGrid::MULTI_RECORD_ACTIONS);
        $grid->removeFlag(atkDataGrid::MULTI_RECORD_PRIORITY_ACTIONS);
        $grid->setDefaultActions($actions);

        $this->modifyDataGrid($grid, atkDataGrid::CREATE);

        if ($this->autoSelectRecord($grid)) {
            return '';
        }

        $params = array();
        $params["header"] = $entity->text("title_select");
        $params["list"] = $grid->render();
        $params["footer"] = "";

        if (atkLevel() > 0) {
            $backUrl = session_url(atkSelf() . '?atklevel=' . session_level(SESSION_BACK));
            $params["footer"] = '<br><div style="text-align: center"><input type="button" onclick="window.location=\'' . $backUrl . '\';" value="&lt;&lt; '
                    . $this->getEntity()->text('back') . '"></div>';
        }

        $output = $this->getUi()->renderList("select", $params);

        $vars = array("title" => $this->m_entity->actionTitle('select'), "content" => $output);
        $output = $this->getUi()->renderBox($vars);

        return $output;
    }

    /**
     * Update the admin datagrid.
     *
     * @return string new grid html
     */

    public function partial_datagrid()
    {

        try {
            $grid = atkDataGrid::resume($this->getEntity());

            $this->modifyDataGrid($grid, atkDataGrid::RESUME);
        } catch (Exception $e) {
            $grid = atkDataGrid::create($this->getEntity());

            $this->modifyDataGrid($grid, atkDataGrid::RESUME);
        }
        return $grid->render();
    }

    /**
     * If the auto-select flag is set and only one record exists we immediately
     * return with the selected record.
     *
     * @param atkDataGrid $grid data grid
     * 
     * @return boolean auto-select active?
     */

    protected function autoSelectRecord($grid)
    {
        $entity = $this->getEntity();
        if (!$entity->hasFlag(EF_AUTOSELECT)) {
            return false;
        }

        $grid->loadRecords();
        if ($grid->getCount() != 1) {
            return false;
        }

        if (atkLevel() > 0 && $grid->getPostvar('atkprevlevel', 0) > atkLevel()) {
            $backUrl = session_url(atkSelf() . '?atklevel=' . session_level(SESSION_BACK));
            $entity->redirect($backUrl);
        } else {
            $records = $grid->getRecords();

            // There's only one record and the autoselect flag is set, so we
            // automatically go to the target.

            $parser = new Adapto_StringParser(rawurldecode(atkurldecode($grid->getPostvar('atktarget'))));

            // For backwardscompatibility reasons, we also support the '[pk]' var.
            $records[0]['pk'] = $entity->primaryKey($records[0]);
            $target = $parser->parse($records[0], true);

            $entity->redirect(session_url($target, SESSION_NESTED));
        }

        return true;
    }
}
