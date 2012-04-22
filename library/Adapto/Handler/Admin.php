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
 * @copyright (c)2000-2004 Ibuildings.nl BV
 * @license http://www.achievo.org/atk/licensing ATK Open Source License
 *

 */

/**
 * Handler for the 'admin' action of an entity. It displays a recordlist with
 * existing records, and links to view/edit/delete them (or custom actions
 * if present), and an embedded addform or a link to an addpage (depending
 * on the presence of the EF_ADD_LINK or EF_ADD_DIALOG flag).
 *
 * @author ijansch
 * @package adapto
 * @subpackage handlers
 *
 */

class Adapto_Handler_Admin extends Adapto_ActionHandler
{
    public $m_actionSessionStatus = SESSION_NESTED; // defaulted to public

    /**
     * The action method
     */
    function action_admin()
    {
        if (!empty($this->m_partial)) {
            $this->partial($this->m_partial);
            return;
        }

        $page = &$this->getPage();
        $res = $this->renderAdminPage();
        $page->addContent($this->m_entity->renderActionPage("admin", $res));
    }

    /**
     * Sets the action session status for actions in the recordlist.
     * (Defaults to SESSION_NESTED).
     *
     * @param Integer $sessionStatus The sessionstatus (for example SESSION_REPLACE)
     */
    function setActionSessionStatus($sessionStatus)
    {
        $this->m_actionSessionStatus = $sessionStatus;
    }

    /**
     * Render the adminpage, including addpage if necessary
     *
     * @return array with result of adminPage and addPage
     */
    function renderAdminPage()
    {
        $res = array();
        if ($this->m_entity->hasFlag(EF_NO_ADD) == false && $this->m_entity->allowed("add")) {
            if (!$this->m_entity->hasFlag(EF_ADD_LINK) && !$this->m_entity->hasFlag(EF_ADD_DIALOG)) // otherwise, in adminPage, an add link will be added.
 {
                // we could get here because of a reject.
                $record = $this->getRejectInfo();

                $res[] = $this->invoke("addPage", $record);
            }
        }
        $res[] = $this->invoke("adminPage");
        return $res;
    }

    /**
     * Draws the form for adding new records.
     *
     * The implementation delegates drawing of the form to the atkAddHandler.
     *
     * @param array $record The record
     * @return String A box containing the add page.
     */
    function addPage($record = NULL)
    {
        // Reuse the atkAddHandler for the addPage.
        $entity = atkGetEntity($this->invoke('getAddEntityType'));

        $handler = $entity->getHandler("add");
        $handler->setEntity($entity);
        $handler->setReturnBehaviour(Adapto_ACTION_STAY); // have the save action stay on the admin page
        return $handler->invoke("addPage", $record);
    }

    /**
     * Admin page displays records and the actions that can be performed on
     * them (edit, delete)
     *
     * @param array $actions The list of actions displayed next to each
     *                       record. Entitys can implement a
     *                       recordActions($record, &$actions, &$mraactions)
     *                       method to add/remove record-specific actions.
     * @return String A box containing the admin page (without the add form,
     *                which is added later.
     */
    function adminPage($actions = "")
    {
        $ui = &$this->getUi();

        $vars = array("title" => $this->m_entity->actionTitle($this->getEntity()->m_action), "content" => $this->renderAdminList());

        if ($this->getRenderMode() == 'dialog') {
            $output = $ui->renderDialog($vars);
        } else {
            $output = $ui->renderBox($vars);
        }

        return $output;
    }

    /**
     * Renders the recordlist for the admin mode
     *
     * @param Array $actions An array with the actions for the admin mode
     * @return String The HTML for the admin recordlist
     */
    function renderAdminList($actions = "")
    {
        $this->getEntity()->addStyle("style.css");

        $grid = atkDataGrid::create($this->getEntity(), 'admin');

        if (is_array($actions)) {
            $grid->setDefaultActions($actions);
        }

        $this->modifyDataGrid($grid, atkDataGrid::CREATE);

        if ($this->redirectToSearchAction($grid)) {
            return '';
        }

        $params = array();
        $params["header"] = $this->invoke("adminHeader") . $this->getHeaderLinks();
        $params["list"] = $grid->render();
        $params["footer"] = $this->invoke("adminFooter");
        $output = $this->getUi()->renderList("admin", $params);
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

            $this->modifyDataGrid($grid, atkDataGrid::CREATE);
        }

        if ($this->redirectToSearchAction($grid)) {
            return '';
        }

        return $grid->render();
    }

    /**
     * If a search action has been defined and a search only returns one result
     * the user will be automatically redirected to the search action.
     *
     * @param atkDataGrid $grid data grid
     * @return boolean redirect active?
     */

    protected function redirectToSearchAction($grid)
    {
        $entity = $this->getEntity();
        $search = $grid->getPostvar('atksearch');

        // check if we are searching and a search action has been defined
        if (!is_array($search) || count($search) == 0 || !is_array($entity->m_search_action) || count($entity->m_search_action) == 0) {
            return false;
        }

        // check if there is only a single record in the result
        $grid->loadRecords();
        if ($grid->getCount() != 1) {
            return false;
        }

        $records = $grid->getRecords();

        foreach ($entity->m_search_action as $action) {
            if (!$entity->allowed($action, $records[0])) {
                continue;
            }

            // reset search so we can back to the normal admin screen if we want
            $grid->setPostvar('atksearch', array());

            $url = session_url(dispatch_url($entity->atkEntityType(), $action, array('atkselector' => $entity->primaryKey($records[0]))), SESSION_NESTED);

            if ($grid->isUpdate()) {

                $script = 'document.location.href = ' . atkJSON::encode($url) . ';';
                $entity->getPage()->register_loadscript($script);
            } else {
                $entity->redirect($url);
            }

            return true;
        }

        return false;
    }

    /**
     * Function that is called when creating an adminPage.
     *
     * The default implementation returns an empty string, but developers can
     * override this function in their custom handlers or directly in the
     * entity class.
     *
     * @return String A string that is displayed above the recordlist.
     */
    function adminHeader()
    {
        return "";
    }

    /**
     * Function that is called when creating an adminPage.
     *
     * The default implementation returns an empty string, but developers can
     * override this function in their custom handlers or directly in the
     * entity class.
     *
     * @return String A string that is displayed below the recordlist.
     */
    function adminFooter()
    {
        return "";
    }

    /**
     * Get the importlink to add to the admin header
     *
     * @return String HTML code with link to the import action of the entity (if allowed)
     */
    function getImportLink()
    {
        $link = "";
        if ($this->m_entity->allowed("add") && !$this->m_entity->hasFlag(EF_READONLY) && $this->m_entity->hasFlag(EF_IMPORT)) {
            $link .= href(dispatch_url($this->m_entity->atkEntityType(), "import"), atktext("import", "atk", $this->m_entity->m_type), SESSION_NESTED);
        }
        return $link;
    }

    /**
     * Get the exportlink to add to the admin header
     *
     * @return String HTML code with link to the export action of the entity (if allowed)
     */
    function getExportLink()
    {
        $link = "";
        if ($this->m_entity->allowed("view") && $this->m_entity->allowed("export") && $this->m_entity->hasFlag(EF_EXPORT)) {
            $filter = '';
            if (count($this->m_entity->m_fuzzyFilters) > 0) {
                $filter = implode(' AND ', $this->m_entity->m_fuzzyFilters);
            }

            $link .= href(dispatch_url($this->m_entity->atkEntityType(), "export", array('atkfilter' => $filter)), atktext("export", "atk", $this->m_entity->m_type),
                    SESSION_NESTED);
        }
        return $link;
    }

    /**
     *
     * This function returns the entitytype that should be used for creating
     * the add form or add link above the admin grid. This defaults to the
     * entity for this handler. Override this method in your handler or directly
     * in your entity to set a custom entitytype.
     */

    public function getAddEntityType()
    {
        return $this->m_entity->atkEntityType();
    }

    /**
     * Get the add link to add to the admin header
     *
     * @return String HTML code with link to the add action of the entity (if allowed)
     */
    function getAddLink()
    {
        $entity = atkGetEntity($this->invoke('getAddEntityType'));

        if (!$entity->hasFlag(EF_NO_ADD) && $entity->allowed("add")) {
            $label = $entity->text("link_" . $entity->m_type . "_add", null, "", "", true);
            if (empty($label)) {
                // generic text
                $label = $entity->text($entity->m_type) . " " . atktext("add", "atk", "");
            }

            $add = $entity->hasFlag(EF_ADD_DIALOG);
            $addorcopy = $entity->hasFlag(EF_ADDORCOPY_DIALOG) && atkAddOrCopyHandler::hasCopyableRecords($entity);

            if ($add || $addorcopy) {
                $action = $entity->hasFlag(EF_ADDORCOPY_DIALOG) ? 'addorcopy' : 'add';

                $dialog = new Adapto_Dialog($entity->atkEntityType(), $action, 'dialog');
                $dialog->setModifierObject($entity);
                $dialog->setSessionStatus(SESSION_PARTIAL);
                $onClick = $dialog->getCall();

                return '
			      <a href="javascript:void(0)" onclick="' . $onClick . '; return false;" class="valignMiddle">' . $label . '</a>
			    ';
            } elseif ($entity->hasFlag(EF_ADD_LINK)) {
                $addurl = $this->invoke('getAddUrl', $entity);
                return atkHref($addurl, $label, SESSION_NESTED);
            }

        }

        return "";
    }

    /**
     * This function renders the url that is used by
     * Adapto_Handler_Admin::getAddLink().
     *
     * @return string The url for the add link for the admin page
     */

    public function getAddUrl()
    {
        $entity = atkGetEntity($this->invoke('getAddEntityType'));
        return atkSelf() . '?atkentitytype=' . $entity->atkEntityType() . '&atkaction=add';
    }

    /**
     * Get all links to add to the admin header
     *
     * @return String String with the HTML code of the links (each link separated with |)
     */
    function getHeaderLinks()
    {
        $links = array();
        $addlink = $this->getAddLink();
        if ($addlink != "")
            $links[] = $addlink;
        $importlink = $this->getImportLink();
        if ($importlink != "")
            $links[] = $importlink;
        $exportlink = $this->getExportLink();
        if ($exportlink != "")
            $links[] = $exportlink;
        $result = implode(" | ", $links);

        if (strlen(trim($result)) > 0) {
            $result .= '<br/>';
        }

        return $result;
    }

    /**
     * Dialog handler.
     */
    function partial_dialog()
    {
        $this->setRenderMode('dialog');
        $result = $this->renderAdminPage();
        return $this->m_entity->renderActionPage("admin", $result);
    }

    /**
     * Attribute handler.
     *
     * @param string $partial full partial
     */
    function partial_attribute($partial)
    {
        list($type, $attribute, $partial) = explode('.', $partial);

        $attr = &$this->m_entity->getAttribute($attribute);
        if ($attr == NULL) {
            atkerror("Unknown / invalid attribute '$attribute' for entity '" . $this->m_entity->atkEntityType() . "'");
            return '';
        }

        return $attr->partial($partial, 'admin');
    }
}
?>
