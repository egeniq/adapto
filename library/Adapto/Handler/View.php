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
 * Handler class for a readonly view action. Similar to the edit handler,
 * but all fields are displayed readonly.
 *
 * @author ijansch
 * @package adapto
 * @subpackage handlers
 *
 */
class Adapto_Handler_View extends Adapto_ViewEditBase
{

    public $m_buttonsource = null; // defaulted to public

    /**
     * The action handler method.
     *
     * @param Bool $renderbox Render this action in a renderbox or just output the HTML
     */
    function action_view($renderbox = true)
    {
        if (!empty($this->m_partial)) {
            $this->partial($this->m_partial);
            return;
        }

        $record = $this->getRecord();

        // allowed to view record?
        if (!$this->allowed($record)) {
            $this->renderAccessDeniedPage();
            return;
        }

        $page = &$this->getPage();
        $this->notify("view", $record);
        $page->addContent($this->m_entity->renderActionPage("admin", $this->invoke("viewPage", $record, $this->m_entity, $renderbox)));
    }

    /**
     * Returns the view record.
     */
    function getRecordFromDb()
    {
        list($record) = $this->m_entity
                ->selectDb($this->m_postvars['atkselector'], $this->getEntity()->getColumnConfig()->getOrderByStatement(), "", $this->m_entity->m_viewExcludes, "",
                        "view");
        return $record;
    }

    /**
     * Get the start of the form.
     *
     * @return String HTML The forms' start
     */

    public function getFormStart($record = null)
    {
        return '<form name="entryform" id="entryform" action="' . getDispatchFile() . '" method="get">' . session_form(SESSION_NESTED)
                . '<input type="hidden" name="atkselector" value="' . $this->getEntity()->primaryKey($record) . '">';
    }

    /**
     * Returns an htmlpage displaying all displayable attributes.
     * @param array $record The record to display.
     * @param atkEntity $entity The entity for which a viewPage is displayed.
     * @param Bool $renderbox Render this action in a renderbox or just output the HTML
     * @return String The html page with a reaonly view of relevant fields.
     */
    function viewPage($record, $entity, $renderbox = true)
    {
        $ui = &$this->getUi();
        $entity->addStyle("style.css");

        if (is_object($ui)) {
            $params = $entity->getDefaultActionParams();
            $tab = $entity->getActiveTab();
            $innerform = $this->viewForm($record, "view");

            $params["activeTab"] = $tab;
            $params["header"] = $this->invoke("viewHeader", $record);
            $params['title'] = $entity->actionTitle($this->m_action, $record);
            $params["content"] = $entity->tabulate("view", $innerform);

            $params["formstart"] = $this->getFormStart($record);
            $params["buttons"] = $this->getFormButtons($record);
            $params["formend"] = '</form>';

            $output = $ui->renderAction("view", $params);

            if (!$renderbox) {
                return $output;
            }

            $this->getPage()->setTitle(atktext('app_shorttitle') . " - " . $entity->actionTitle($this->m_action, $record));

            $vars = array("title" => $entity->actionTitle($this->m_action, $record), "content" => $output);

            if ($this->getRenderMode() == "dialog") {
                $total = $ui->renderDialog($vars);
            } else {
                $total = $ui->renderBox($vars, $this->m_boxTemplate);
            }

            return $total;
        } else {
            atkerror("ui object error");
        }
    }

    /**
     * Get the buttons for the current action form.
     *
     * @param array $record
     * @return array Array with buttons
     */

    public function getFormButtons($record = null)
    {
        // If no custom button source is given, get the default atkController.
        if ($this->m_buttonsource === null) {
            $this->m_buttonsource = &$this->m_entity;
        }

        return $this->m_buttonsource->getFormButtons("view", $record);
    }

    /**
     * Overrideable function to create a header for view mode.
     * Similar to the admin header functionality.
     */
    function viewHeader()
    {
        return "";
    }

    /**
     * Get the view page
     *
     * @param Array $record The record
     * @param String $mode The mode we're in (defaults to "view")
     * @param String $template The template to use for the view form
     * @return String HTML code of the page
     */
    function viewForm($record, $mode = "view", $template = "")
    {
        $entity = &$this->m_entity;

        // get data, transform into form, return
        $data = $entity->viewArray($mode, $record);

        // get active tab
        $tab = $entity->getActiveTab();
        // get all tabs of current mode
        $tabs = $entity->getTabs($mode);

        $fields = array();
        $attributes = array();

        // For all attributes we use the display() function to display the
        // attributes current value. This may be overridden by supplying
        // an <attributename>_display function in the derived classes.
        for ($i = 0, $_i = count($data["fields"]); $i < $_i; $i++) {
            $field = &$data["fields"][$i];
            $tplfield = array();

            $classes = array();
            if ($field["sections"] == "*") {
                $classes[] = "alltabs";
            } else if ($field["html"] == "section") {
                // section should only have the tab section classes
                foreach ($field["tabs"] as $section)
                    $classes[] = "section_" . str_replace('.', '_', $section);
            } else if (is_array($field["sections"])) {
                foreach ($field["sections"] as $section)
                    $classes[] = "section_" . str_replace('.', '_', $section);
            }

            $tplfield["class"] = implode(" ", $classes);
            $tplfield["tab"] = $tplfield["class"]; // for backwards compatibility

            // visible sections, both the active sections and the tab names (attribute that are
            // part of the anonymous section of the tab)
            $visibleSections = array_merge($this->m_entity->getActiveSections($tab, $mode), $tabs);

            // Todo fixme: initial_on_tab kan er uit, als er gewoon bij het opstarten al 1 keer showTab aangeroepen wordt (is netter dan aparte initial_on_tab check)
            // maar, let op, die showTab kan pas worden aangeroepen aan het begin.
            $tplfield["initial_on_tab"] = ($field["tabs"] == "*" || in_array($tab, $field["tabs"]))
                    && (!is_array($field["sections"]) || count(array_intersect($field['sections'], $visibleSections)) > 0);

            // Give the row an id if it doesn't have one yet
            if (!isset($field["id"]) || empty($field["id"]))
                $field['id'] = getUniqueID("anonymousattribrows");

            // ar_ stands voor 'attribrow'.
            $tplfield["rowid"] = "ar_" . $field['id']; // The id of the containing row

            /* check for separator */
            if ($field["html"] == "-" && $i > 0 && $data["fields"][$i - 1]["html"] != "-") {
                $tplfield["line"] = "<hr>";
            } /* double separator, ignore */
 elseif ($field["html"] == "-") {
            } /* sections */
 elseif ($field["html"] == "section") {
                $tplfield["line"] = $this->getSectionControl($field, $mode);
            } /* only full HTML */
 elseif (isset($field["line"])) {
                $tplfield["line"] = $field["line"];
            } /* edit field */
 else {
                if ($field["attribute"]->m_ownerInstance->getNumbering()) {
                    $this->_addNumbering($field, $tplfield, $i);
                }

                /* does the field have a label? */
                if ((isset($field["label"]) && $field["label"] !== "AF_NO_LABEL") || !isset($field["label"])) {
                    if ($field["label"] == "") {
                        $tplfield["label"] = "";
                    } else {
                        $tplfield["label"] = $field["label"];
                    }
                } else {
                    $tplfield["label"] = "AF_NO_LABEL";
                }

                // Make the attribute and entity names available in the template.
                $tplfield['attribute'] = $field["attribute"]->fieldName();
                $tplfield['entity'] = $field["attribute"]->m_ownerInstance->atkEntityType();

                /* html source */
                $tplfield["widget"] = $field["html"];
                $editsrc = $field["html"];

                /* tooltip */
                $tooltip = $field["attribute"]->getToolTip();
                if ($tooltip) {
                    $tplfield["tooltip"] = $tooltip;
                    $editsrc .= $tooltip . "&nbsp;";
                }

                $tplfield['id'] = str_replace('.', '_', $entity->atkentitytype() . '_' . $field["id"]);

                $tplfield["full"] = $editsrc;

                $column = $field['attribute']->getColumn();
                $tplfield["column"] = $column;
            }
            $fields[] = $tplfield; // make field available in numeric array
            $params[$field["name"]] = $tplfield; // make field available in associative array
            $attributes[$field["name"]] = $tplfield; // make field available in associative array

        }
        $ui = &$this->getUi();

        $tabTpl = $this->_getTabTpl($entity, $tabs, $mode, $record);

        if ($template) {
            $innerform = $ui->render($template, array("fields" => $fields, 'attributes' => $attributes));
        } else {
            if (count(array_unique($tabTpl)) > 1) {
                $tabForm = $this->_renderTabs($fields, $tabTpl);
                $innerform = implode(null, $tabForm);
            } else {
                $innerform = $ui->render($entity->getTemplate("view", $record, $tab), array("fields" => $fields, 'attributes' => $attributes));
            }
        }
        return $innerform;
    }

    /**
     * The dialog partial
     *
     * @return String HTML code for the view dialog
     */
    function partial_dialog()
    {
        return $this->renderViewDialog();
    }

    /**
     * Render view dialog.
     *
     * @param array $record
     * @return string html
     */
    function renderViewDialog($record = null)
    {
        if ($record == null) {
            $record = $this->getRecord();
        }

        $this->setRenderMode('dialog');
        $result = $this->m_entity->renderActionPage("view", $this->invoke("viewPage", $record, $this->m_entity));
        return $result;
    }
}
?>