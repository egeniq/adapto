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
 * @copyright (c)2000-2007 Ibuildings.nl BV
 * @license http://www.achievo.org/atk/licensing ATK Open Source License
 *

 */

/**
 * Custom flags
 *
 * Do not hide attribute label when it is single on the tab
 */
define("AF_TABBEDPANE_NO_AUTO_HIDE_LABEL", AF_SPECIFIC_1);

/**
 * Adapto_Attribute_TabbedPane place regular attribute to the additional tabbed pane
 *
 * @author Yury Golovnya <ygolovnya@gmail.com>
 * @package adapto
 * @subpackage attributes
 *
 */
class Adapto_Attribute_TabbedPane extends Adapto_Attribute
{
    /**
     * The tabs list
     * @var array
     * @access private
     */
    public $m_tabsList = array(); // defaulted to public

    /**
     * The list of "attribute"=>"tab
     * @var array
     * @access private
     */
    public $m_attribsList = array(); // defaulted to public

    /**
     * Constructor
     * @param String $name The name of the attribute
     * @param Array  $tabs The arrays looks like array("tabname1"=>("attribname1,"attribname2),"tabname1"=>(..),..)
     * @param int $flags The flags for this attribute
     */

    public function __construct($name, $tabs = array(), $flags = 0)
    {
        foreach ($tabs as $tab => $attribs) {
            foreach ($attribs as $attrib) {
                $this->add($attrib, $tab);
            }
        }
        // A Adapto_Attribute_TabbedPane attribute should be display only in edit/view mode
        parent::__construct($name, $flags | AF_HIDE_SEARCH | AF_HIDE_LIST | AF_HIDE_SELECT); // base class constructor
    }

    /**
     * Add attribute to tabbedpane
     * @param String $attrib The name of the attribute
     * @param String  $tab The name of tab. If empty, attribute name used
     */
    function add($attrib, $tab = "")
    {
        if (empty($tab))
            $tab = $attrib;

        if (!in_array($tab, $this->m_tabsList)) {
            $this->m_tabsList[] = $tab;
        }
        $this->m_attribsList[$attrib] = $tab;
        if (is_object($this->m_ownerInstance)) {
            $p_attr = &$this->m_ownerInstance->getAttribute($attrib);
            if (is_object($p_attr)) {
                $p_attr->addDisabledMode(DISABLED_VIEW | DISABLED_EDIT);
                $p_attr->setTabs($this->getTabs());
                $p_attr->setSections($this->getSections());
            }
        }
    }

    /**
     * Return list of all tabs, having in this attribute
     * @param String $action An action name. Don't use now
     * @return Array  $tab The array name of tab.
     */
    function getPaneTabs($action)
    {
        return $this->m_tabsList;
    }

    /**
     * Return default tab, now simply first tab
     * @return String The default tab name.
     */
    function getDefaultTab()
    {
        //return first tab
        return $this->m_tabsList[0];
    }

    /**
     * Post init function
     *
     */
    function postInit()
    {
        foreach (array_keys($this->m_attribsList) as $attrib) {
            $p_attr = &$this->m_ownerInstance->getAttribute($attrib);
            $p_attr->addDisabledMode(DISABLED_VIEW | DISABLED_EDIT);
            $p_attr->setTabs($this->getTabs());
            $p_attr->setSections($this->getSections());
        }
    }

    /**
     * Check if attribute is single on the tab
     * @param String $name The name of attribute
     * @return Bool  True if single.
     * $todo Take into accout AF_HIDE_VIEW,AF_HIDE_EDIT flag of attribute -
     * attribute can be placed on tab, but only in edit action - 2 attribute when edit and 1  -if view
     */
    function isAttributeSingleOnTab($name)
    {
        $result = false;

        if (!$this->hasFlag(AF_TABBEDPANE_NO_AUTO_HIDE_LABEL)) {
            $tab = $this->m_attribsList[$name];
            $friquency = array_count_values(array_values($this->m_attribsList));
            $result = ($friquency[$tab] == 1);
        }
        return $result;
    }

    /**
     * Adds the attribute's edit / hide HTML code to the edit array.
     *
     * @param String $mode     the edit mode ("add" or "edit")
     * @param array  $arr      pointer to the edit array
     * @param array  $defaults pointer to the default values array
     * @param array  $error    pointer to the error array
     * @param String $fieldprefix   the fieldprefix
     */
    function _addToEditArray($mode, &$arr, &$defaults, &$error, $fieldprefix)
    {
        $entity = &$this->m_ownerInstance;
        $fields = array();

        //collecting output from attributes
        foreach ($this->m_attribsList as $name => $tab) {
            $p_attrib = &$entity->getAttribute($name);
            if (is_object($p_attrib)) {
                /* hide - nothing to do with tabbedpane, must be render on higher level*/
                if (($mode == "edit" && $p_attrib->hasFlag(AF_HIDE_EDIT)) || ($mode == "add" && $p_attrib->hasFlag(AF_HIDE_ADD))) {
                    /* when adding, there's nothing to hide... */
                    if ($mode == "edit" || ($mode == "add" && !$p_attrib->isEmpty($defaults)))
                        $arr["hide"][] = $p_attrib->hide($defaults, $fieldprefix, $mode);
                }
                /* edit */
 else {
                    $entry = array("name" => $p_attrib->m_name, "obligatory" => $p_attrib->hasFlag(AF_OBLIGATORY), "attribute" => &$p_attrib);
                    $entry["id"] = $p_attrib->getHtmlId($fieldprefix);

                    /* label? */
                    $entry["label"] = $p_attrib->getLabel($defaults);
                    /* error? */
                    $entry["error"] = $p_attrib->getError($error);
                    // on which tab? - from tabbedpane properties
                    $entry["tabs"] = $tab;
                    /* the actual edit contents */
                    $entry["html"] = $p_attrib->getEdit($mode, $defaults, $fieldprefix);
                    $fields["fields"][] = $entry;
                }
            } else {
                atkerror("Attribute $name not found!");
            }
        }
        /* check for errors */
        $fields["error"] = $defaults['atkerror'];
        return $fields;
    }

    /**
     * Returns a piece of html code that can be used in a form to edit this
     * attribute's value.
     *
     * @param array $defaults The record that holds the value for this attribute.
     * @param String $fieldprefix The fieldprefix to put in front of the name
     *                            of any html form element for this attribute.
     * @param String $mode The mode we're in ('add' or 'edit')
     * @return String A piece of htmlcode for editing this attribute
     */
    function edit($defaults = "", $fieldprefix = "", $mode = "")
    {
        $entity = &$this->m_ownerInstance;
        $arr = array("hide" => array());
        //get data
        $data = $this->_addToEditArray($mode, $arr, $defaults, $defaults['atkerror'], $fieldprefix);

        // Handle errors - need more testing for move to right tab
        /*$errors = array();
        if (count($data['error']) > 0)
        {
          $error_title = '<b>'.atktext('error_formdataerror').'</b>';
        
          foreach ($data["error"] as $error)
          {
            if(in_array($error['attribname'],array_keys($this->m_attribsList)))
            {
              $error['tab'] = $this->m_attribsList[$error['attribname']];
            }
        
            if ($error['err'] == "error_primarykey_exists")
            {
              $pk_err_attrib[] = $error['attrib_name'];
            }
            else
            {
              $type = (empty($error["entity"]) ? $entity->m_type : $error["entity"]);
        
              if (count($entity->getTabs($entity->m_action)) > 1 && $error["tab"])
                $error_tab = ' ('.atktext("error_tab").' '.'<a href="javascript:showTab(\''.$error["tab"].'\');">'.atktext(array("tab_".$error["tab"], $error["tab"]),$entity->m_module, $entity->m_type).'</a> )';
              else $error_tab = "";
        
              if(!is_array($error['attrib_name']))
              {
                $label = atktext($error['attrib_name'], $entity->m_module, $type);
              }
              else
              {
                $label = array();
                foreach($error['attrib_name'] as $attrib)
                  $label[] = atktext($attrib, $entity->m_module, $type);
        
                $label= implode(", ", $label);
              }
        
              $errors[] = array("msg"=>$error['msg'].$error_tab, "label"=>$label);
            }
          }
          if (count($pk_err_attrib)>0) // Make primary key error message
          {
            for($i=0;$i<count($pk_err_attrib); $i++)
            {
              $pk_err_msg .= atktext($pk_err_attrib[$i], $entity->m_module);
              if (($i+1) < count($pk_err_attrib)) $pk_err_msg .= ", ";
            }
            $errors[] = array("label"=>atktext("error_primarykey_exists"),
                              "msg"=>$pk_err_msg);
          }
        }*/

        // Handle fields
        // load images
        $theme = &atkinstance("atk.ui.atktheme");
        $tipimg = $theme->imgPath("help.gif");
        $reqimg = '<img align="top" src="' . $theme->imgPath("required_field.gif") . '" border="0"
                  alt="' . atktext("field_obligatory") . '" title="' . atktext("field_obligatory") . '">';

        /* display the edit fields */
        $fields = array();
        $tab = $this->getDefaultTab();

        for ($i = 0, $_i = count($data["fields"]); $i < $_i; $i++) {
            $field = &$data["fields"][$i];
            $tplfield = array();

            $tplfield["tab"] = $field["tabs"];

            $tplfield["initial_on_tab"] = $tplfield["tab"] == $tab;

            $tplfield["class"] = "tabbedPaneAttr tabbedPaneTab{$field['tabs']}";

            // Check if there are attributes initially hidden on this tabbedpane
            if ($field["attribute"]->isInitialHidden($defaults))
                $tplfield["class"] .= " atkAttrRowHidden";

            $tplfield["rowid"] = "tabbedPaneAttr_" . ($field['id'] != '' ? $field['id'] : getUniqueID("anonymousattribrows")); // The id of the containing row

            /* check for separator */
            if ($field["html"] == "-" && $i > 0 && $data["fields"][$i - 1]["html"] != "-") {
                $tplfield["line"] = "<hr>";
            } /* double separator, ignore */
 elseif ($field["html"] == "-") {
            } /* only full HTML */
 elseif (isset($field["line"])) {
                $tplfield["line"] = $field["line"];
            } /* edit field */
 else {
                if ($field["attribute"]->m_ownerInstance->getNumbering()) {
                    atkViewEditBase::_addNumbering($field, $tplfield, $i);
                }

                /* does the field have a label? */
                if ((isset($field["label"]) && $field["label"] !== "AF_NO_LABEL") && !$this->isAttributeSingleOnTab($field['name']) || !isset($field["label"])) {
                    if ($field["label"] == "") {
                        $tplfield["label"] = "";
                    } else {
                        $tplfield["label"] = $field["label"];
                        if ($field["error"]) // TODO KEES
 {
                            $tplfield["error"] = $field["error"];
                        }
                    }
                } else {
                    $tplfield["label"] = "AF_NO_LABEL";
                }

                /* obligatory indicator */
                if ($field["obligatory"]) {
                    $tplfield["label"];
                    $tplfield["obligatory"] = $reqimg;
                }

                /* html source */
                $tplfield["widget"] = $field["html"];
                $editsrc = $field["html"];

                /* tooltip */
                if (is_object($entity->m_attribList[$field['name']])) {
                    $module = $entity->m_attribList[$field['name']]->getModule();
                }
                if (!$module)
                    $module = "atk";
                $ttip = atktext($entity->m_type . "_" . $field["name"] . "_tooltip", $module, "", "", "", true);

                if ($ttip) {
                    $onelinetip = preg_replace('/([\r\n])/e', "", $ttip);
                    $tooltip = '<img align="top" src="' . $tipimg . '" border="0" alt="' . $onelinetip . '" onClick="javascript:alert(\''
                            . str_replace("\n", '\n', addslashes($ttip)) . '\')" onMouseOver="javascript:window.status=\'' . addslashes($onelinetip) . '\';">';
                    $tplfield["tooltip"] = $tooltip;
                    $editsrc .= $tooltip . "&nbsp;";
                }

                $tplfield['id'] = str_replace('.', '_', $entity->atkentitytype() . '_' . $field["id"]);

                $tplfield["full"] = $editsrc;
            }
            $fields[] = $tplfield; // make field available in numeric array
            $params[$field["name"]] = $tplfield; // make field available in associative array
        }

        $ui = &$entity->getUi();
        $page = &$entity->getPage();

        $result = "";

        foreach ($arr["hide"] as $hidden) {
            $result .= $hidden;
        }

        $params["activeTab"] = $tab;
        $params["panename"] = $this->m_name;
        $params["fields"] = $fields; // add all fields as an numeric array.
        $params["errortitle"] = $error_title;
        $params["errors"] = $errors; // Add the list of errors.
        if (!$template)
            $template = $entity->getTemplate($mode, $record, $tab);
        $result .= $ui->render("tabbededitform.tpl", $params);

        $content = $this->tabulate($mode, $result, $fieldprefix);

        return $content;
    }

    /**
     * Display a tabbed pane with attributes
     * @param array $record  Array with fields
     * @param string $mode The mode
     * @return html code
     */
    function display($record, $mode = "")
    {
        // get active tab
        $active_tab = $this->getDefaultTab();
        $fields = array();

        $entity = &$this->m_ownerInstance;
        $ui = &$entity->getUi();

        // For all attributes we use the display() function to display the
        // attributes current value. This may be overridden by supplying
        // an <attributename>_display function in the derived classes.
        foreach ($this->m_attribsList as $name => $tab) {
            $p_attrib = &$entity->getAttribute($name);
            if (is_object($p_attrib)) {
                $tplfield = array();
                if (!$p_attrib->hasFlag(AF_HIDE_VIEW)) {
                    $fieldtab = $this->m_attribsList[$name];

                    $tplfield["class"] = "tabbedPaneAttr tabbedPaneTab{$fieldtab}";
                    $tplfield["rowid"] = "tabbedPaneAttr_" . getUniqueID("anonymousattribrows"); // The id of the containing row
                    $tplfield["tab"] = $tplfield["class"]; // for backwards compatibility

                    $tplfield["initial_on_tab"] = ($fieldtab == $active_tab);

                    // An <attributename>_display function may be provided in a derived
                    // class to display an attribute. If it exists we will use that method
                    // else we will just use the attribute's display method.
                    $funcname = $p_attrib->m_name . "_display";
                    if (method_exists($entity, $funcname))
                        $editsrc = $entity->$funcname($record, "view");
                    else
                        $editsrc = $p_attrib->display($record, "view");

                    /* tooltip */
                    $module = $p_attrib->getModule();
                    if (!$module)
                        $module = "atk";
                    $ttip = atktext($entity->m_type . "_" . $name . "_tooltip", $module, "", "", "", true);

                    if ($ttip) {
                        $theme = &atkinstance("atk.ui.atktheme");
                        $tipimg = $theme->imgPath("help.gif");

                        $onelinetip = preg_replace('/([\r\n])/e', "", $ttip);
                        $tooltip = '<img align="top" src="' . $tipimg . '" border="0" alt="' . $onelinetip . '" onClick="javascript:alert(\''
                                . str_replace("\n", '\n', addslashes($ttip)) . '\')" onMouseOver="javascript:window.status=\'' . addslashes($onelinetip)
                                . '\';">';
                        $tplfield["tooltip"] = $tooltip;
                        $editsrc .= $tooltip . "&nbsp;";
                    }

                    $tplfield["full"] = $editsrc;
                    $tplfield["widget"] = $editsrc; // in view mode, widget and full are equal

                    // The Label of the attribute (can be suppressed with AF_NOLABEL or AF_BLANKLABEL)
                    // For each attribute, a txt_<attributename> must be provided in the language files.
                    if (!$p_attrib->hasFlag(AF_NOLABEL) && !$this->isAttributeSingleOnTab($name)) {
                        if ($p_attrib->hasFlag(AF_BLANKLABEL)) {
                            $tplfield["label"] = "";
                        } else {
                            $tplfield["label"] = $p_attrib->label($record);
                        }
                    } else {
                        // Make the rest fill up the entire line
                        $tplfield["label"] = "";
                        $tplfield["line"] = $tplfield["full"];
                    }
                    $fields[] = $tplfield;
                }
            } else {
                atkerror("Attribute $name not found!");
            }
        }
        $innerform = $ui->render($entity->getTemplate("view", $record, $tab), array("fields" => $fields));

        return $this->tabulate("view", $innerform);
    }

    /**
     * Tabulate
     *
     * @param string $action
     * @param string $content
     * @param string $fieldprefix
     * @return String The HTML content
     */
    function tabulate($action, $content, $fieldprefix = "")
    {
        $activeTabName = "tabbedPaneTab" . $this->getDefaultTab();
        $list = $this->getPaneTabs($action);
        if (count($list) > 0) {
            $entity = &$this->m_ownerInstance;
            $entity->addStyle("tabs.css");

            $page = &$entity->getPage();
            $page->register_script(Adapto_Config::getGlobal("atkroot") . "atk/javascript/class.atktabbedpane.js");
            $page->register_loadscript("ATK.TabbedPane.showTab('tabbedPane{$fieldprefix}{$this->m_name}', '$activeTabName');");

            $ui = &$entity->getUi();
            if (is_object($ui)) {
                $content = $ui
                        ->renderBox(
                                array("tabs" => $this->buildTabs($action, $fieldprefix), "paneName" => "tabbedPane{$fieldprefix}{$this->m_name}",
                                        "activeTabName" => $activeTabName, "content" => $content), "panetabs");
            }
        }

        return $content;
    }

    /**
     * Builds a list of tabs.
     *
     * This doesn't generate the actual HTML code, but returns the data for
     * the tabs (title, selected, urls that should be loaded upon click of the
     * tab etc).
     * @param String $action The action for which the tabs should be generated.
     * @param string $fieldprefix The fieldprefix
     * @return array List of tabs
     * @todo Make translation of tabs module aware
     */
    function buildTabs($action = "", $fieldprefix = "")
    {
        $entity = &$this->m_ownerInstance;
        $result = array();

        // which tab is currently selected
        $active_tab = $this->getDefaultTab();

        foreach ($this->m_attribsList as $attrib => $tab) {
            $newtab = array();
            $newtab["title"] = atktext(array("tab_$tab", $tab), $entity->m_module, $entity->m_type);
            $newtab["attribute"] = $attrib;
            $newtab["selected"] = ($active_tab == $tab);
            $result["tabbedPaneTab{$tab}"] = $newtab;
        }

        return $result;
    }

    /**
     * No function, but is neccesary
     * @param array $record
     * @return NULL
     */
    function db2value($record)
    {
        return NULL;
    }

    /**
     * No function, but is necessary
     *
     * @param array $record
     * @param string $fieldprefix
     * @return empty string
     */
    function hide($record = "", $fieldprefix = "")
    {
        return '';
    }

    /**
     * Return the database field type of the attribute.
     * @return string empty string
     */
    function dbFieldType()
    {
        return "";
    }

    /**
     * Determine the load type of this attribute.
     *
     * @param String $mode The type of load (view,admin,edit etc)
     * @param boolean $searching 
     *
     * @return int NOLOAD     - nor load(), nor addtoquery() should be
     *                          called (attribute can not be loaded from the
     *                          database)
     *
     */
    function loadType($mode, $searching = false)
    {
        return NOLOAD;
    }

    /**
     * Determine the storage type of this attribute.
     *
     * @param String $mode The type of storage ("add" or "update")
     *
     * @return int NOSTORE    - nor store(), nor addtoquery() should be
     *                          called (attribute can not be stored in the
     *                          database)
     */
    function storageType($mode)
    {
        return NOSTORE;
    }
}
?>
