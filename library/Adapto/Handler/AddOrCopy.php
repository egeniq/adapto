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
 * @author Dennis Luitwieler <dennis@ibuildings.nl>
 * @package adapto
 * @subpackage handlers
 *
 */
class Adapto_Handler_AddOrCopy extends Adapto_ActionHandler
{
    public $m_processUrl = null; // defaulted to public

    /**
     * The action method.
     */
    function action_addorcopy()
    {
        $this->setRenderMode('dialog');

        if ($this->m_partial == 'process') {
            $this->handleProcess();
        } else {
            $this->handleDialog();
        }
    }

    /**
     * Handle dialog partial.
     */
    function handleDialog()
    {
        $page = &$this->getPage();
        $result = $this->invoke('addOrCopyPage');
        $page->addContent($result);
    }

    /**
     * Handle process partial.
     */
    function handleProcess()
    {
        $addOrCopy = $this->m_postvars['addorcopy'];

        if ($addOrCopy == 'copy') {
            $this->handleCopy();
        } else {
            $this->handleAdd();
        }
    }

    /**
     * Remove one-to-many relations which the user didn't explicitly select.
     *
     * @param array $record   record
     * @param atkEntity $entity   entity reference
     * @param array $includes include list
     * @param string $prefix  current prefix
     */
    function preCopy(&$record, &$entity, $includes, $prefix = "")
    {
        foreach (array_keys($entity->getAttributes()) as $name) {
            $attr = &$entity->getAttribute($name);

            if (!is_a($attr, 'atkonetomanyrelation'))
                continue;

            $path = $prefix . $name;
            if (!in_array($path, $includes)) {
                unset($record[$name]);
            } else {
                $attr->createDestination();

                for ($i = 0, $_i = count($record[$name]); $i < $_i; $i++) {
                    $this->preCopy($record[$name][$i], $attr->m_destInstance, $includes, $path . ".");
                }
            }
        }
    }

    /**
     * Handle copy.
     *
     * @param string $attrRefreshUrl  the attribute refresh url if not specified
     *                                the entire page is refreshed
     */
    function handleCopy($attrRefreshUrl = null)
    {
        $selector = $this->m_postvars['selector'];
        $includes = $this->m_postvars['includes'];
        if ($includes == null)
            $includes = array();

        list($record) = $this->m_entity->selectDb($selector, "", 1, "", "", "copy");
        if ($record != null) {
            if (!$this->m_entity->allowed('copy', $record)) {
                $this->updateDialog($this->renderAccessDeniedDialog());
                return;
            }

            $this->preCopy($record, $this->m_entity, $includes);
        }

        $db = &$this->m_entity->getDb();
        $page = &$this->getPage();

        if ($this->m_entity->copyDb($record)) {
            $db->commit();
            $this->notify("copy", $record);
            $this->clearCache();

            $script = atkDialog::getCloseCall();

            if ($attrRefreshUrl == null) {
                $script .= "document.location.href = document.location.href;";
            } else {
                $page->register_script(Adapto_Config::getGlobal('atkroot') . 'atk/javascript/class.atkattribute.js');
                $script .= "ATK.Attribute.refresh('" . $attrRefreshUrl . "');";
            }
        } else {
            $db->rollback();

            $ui = &$this->m_entity->getUi();

            $params = array();
            $params["content"] = "<br />" . $this->m_entity->text('error_copy_record') . "<br />";
            $params["buttons"][] = '<input type="button" class="btn_cancel" value="' . $this->m_entity->text('close') . '" onClick="' . atkDialog::getCloseCall()
                    . '" />';
            $content = $ui->renderAction("addorcopy", $params);

            $params = array();
            $params["title"] = $this->m_entity->actionTitle('addorcopy');
            $params["content"] = $content;
            $content = $ui->renderDialog($params);

            $script = atkDialog::getUpdateCall($content, false);
        }

        $page->register_loadscript($script);
    }

    /**
     * Handle add.
     */
    function handleAdd()
    {

        $script = atkDialog::getCloseCall();

        if ($this->m_entity->hasFlag(EF_ADD_DIALOG)) {
            $dialog = new Adapto_Dialog($this->m_entity->atkEntityType(), 'add', 'dialog');
            $dialog->setSessionStatus(SESSION_PARTIAL);
            $script .= $dialog->getCall(true, false);
        } else {
            $script .= sprintf("document.location.href = %s;", atkJSON::encode(session_url(dispatch_url($this->m_entity->atkEntityType(), 'add'), SESSION_NESTED)));
        }

        $page = &$this->getPage();
        $page->register_loadscript($script);
    }

    /**
     * Add or copy page.
     */
    function addOrCopyPage()
    {
        $content = $this->getAddOrCopyPage();
        $page = &$this->getPage();
        $page->addContent($content);
    }

    /**
     * Returns the add or copy page contents.
     *
     * @return string add or copy page contents
     */
    function getAddOrCopyPage()
    {
        $url = $this->getProcessUrl();
        $controller = &atkController::getInstance();

        $params = array();
        $params["formstart"] = $this->getFormStart();
        $params["formend"] = '</form>';
        $params["content"] = $this->getContent();
        $params["buttons"][] = $controller->getDialogButton('save', $this->m_entity->text('create'), $url);
        $params["buttons"][] = $controller->getDialogButton('cancel');

        return $this->renderAddOrCopyPage($params);
    }

    /**
     * Render the add or copy page using the given parameters.
     *
     * @param array $params parameters
     * @return string rendered page
     */
    function renderAddOrCopyPage($params)
    {
        $entity = &$this->m_entity;
        $ui = &$entity->getUi();

        $output = $ui->renderAction("add", $params);
        $this->addRenderBoxVar("title", $entity->actionTitle('addorcopy'));
        $this->addRenderBoxVar("content", $output);
        $total = $ui->renderDialog($this->m_renderBoxVars);

        return $total;
    }

    /**
     * Returns the add or copy page contents.
     *
     * @return string add or copy page contents
     */
    function getContent()
    {
        $content = atktext("intro_addorcopy")
                . '
        <br />
        <br />
        <table border="0" style="text-align: left">
          <tr>
            <td>
              ' . $this->getNewOption() . '
            </td>
          </tr>
          <tr>
            <td>
              ' . $this->getCopyOption() . '
            </td>
          </tr>
        </table>';

        return $content;
    }

    /**
     * Returns the form start.
     *
     * @return string form start
     */
    function getFormStart()
    {
        $controller = &atkController::getInstance();
        $formstart = '<form id="dialogform" name="dialogform" action="' . $controller->getPhpFile() . '?' . SID . '" method="post">';
        return $formstart;
    }

    /**
     * Returns the option label for the given action.
     *
     * @param string $action action (copy or add)
     * @return string option label for action
     */
    function getOptionLabel($action)
    {
        $entity = $this->m_entity->m_type;
        $module = $this->m_entity->m_module;

        $label = $this->m_entity->text("{$action}_{$module}_{$entity}", null, '', '', true);

        if ($label == "")
            $label = $this->m_entity->text("{$action}_{$entity}", null, '', '', true);

        if ($label == "")
            $label = $this->m_entity->text($action) . " " . strtolower($this->m_entity->text($this->m_entity->m_type));

        return $label;
    }

    /**
     * Returns the new option.
     *
     * @return string option html
     */
    function getNewOption()
    {
        $label = $this->getOptionLabel('new');
        return '
        <input type="radio" id="addorcopy_new" name="addorcopy" value="new" checked="checked" />
        <label for="addorcopy_new">' . $label . '</label>';
    }

    /**
     * Returns true if there is something to copy. This is used
     * to determine if we can skip the whole dialog and continue to the add page
     * directly.
     *
     * @param atkEntity $entity The entity to check
     * @param string $selector an extra selector to add to the count query
     * @return bool true when there are records to copy, false if there are none
     * @static
     */
    function hasCopyableRecords(&$entity, $selector = '')
    {
        $count = $entity->countDb($selector);
        return ($count > 0);
    }

    /**
     * Returns the copy option
     *
     * @return string option html;
     */
    function getCopyOption()
    {
        $records = $this->m_entity->selectDb("", "", "", "", array_merge(array($this->m_entity->primaryKeyField()), $this->m_entity->descriptorFields()));

        if (count($records) == 0) {
            $label = $this->getOptionLabel('copy') . ' (' . $this->m_entity->text('no_copyable_records') . ')';

            return '
          <input type="radio" id="addorcopy_copy" name="addorcopy" value="copy" disabled="disabled" />
          <label for="addorcopy_copy">' . $label . '</label>';
        } else {
            $label = $this->getOptionLabel('copy');

            return '
          <input type="radio" id="addorcopy_copy" name="addorcopy" value="copy" />
          <label for="addorcopy_copy">' . $label . '</label>&nbsp;&nbsp;' . $this->getCopyDropDown($records) . '<br />' . $this->getCopyIncludes($this->m_entity);
        }
    }

    /**
     * Creates a drop-down with copyable records.
     *
     * @param Array $records Array with records
     * @return string copyable records drop-down
     */
    function getCopyDropDown($records)
    {
        $html = '<select name="selector" class="atkmanytoonerelation">';
        foreach ($records as $record) {
            $html .= '<option value="' . $this->m_entity->primaryKey($record) . '">' . $this->m_entity->descriptor($record) . '</option>';
        }

        $html .= '</select>';

        return $html;
    }

    /**
     * Returns a HTML fragment which allows the user to select nested
     * one-to-many relations he/she wants to include in the copy.
     * 
     * @param atkEntity $entity The entity to get the onetomany relations from
     * @param String $prefix The field prefix
     * @param Integer $level The level of the includes
     */
    function getCopyIncludes($entity, $prefix = '', $level = 0)
    {
        $result = '';

        foreach (array_keys($entity->getAttributes()) as $name) {
            $attr = &$entity->getAttribute($name);

            if (!is_a($attr, 'atkonetomanyrelation'))
                continue;
            if ($attr->hasFlag(AF_HIDE_EDIT))
                continue;
            if ($attr->hasFlag(AF_READONLY_EDIT))
                continue;
            if ($attr->createDestination() && $attr->m_destInstance->hasFlag(EF_READONLY))
                continue;

            $path = $prefix . $name;
            $id = str_replace('.', '_', $path);
            $result .= str_repeat('&nbsp;', ($level + 1) * 5) . '<input type="checkbox" name="includes[]" value="' . $path . '" id="' . $id
                    . '" />
          <label for="' . $id . '">' . $this->m_entity->text('include') . ' ' . strtolower($attr->label()) . '</label><br />';

            $attr->createDestination();
            $result .= $this->getCopyIncludes($attr->m_destInstance, $path . '.', $level + 1);
        }

        return $result;
    }

    /**
     * Returns the process URL.
     *
     * @return string process URL
     */
    function getProcessUrl()
    {
        if ($this->m_processUrl != null) {
            return $this->m_processUrl;
        } else {
            return partial_url($this->m_entity->atkEntityType(), 'addorcopy', 'process');
        }
    }

    /**
     * Override the default process URL.
     *
     * @param string $url process URL
     */
    function setProcessUrl($url)
    {
        $this->m_processUrl = $url;
    }
}
?>
