<?php

/**
 * This file is part of the Adapto Toolkit.
 * Detailed copyright and licensing information can be found
 * in the doc/COPYRIGHT and doc/LICENSE files which should be
 * included in the distribution.
 *
 * @package adapto
 * @subpackage menu
 *
 * @copyright (c)2000-2004 Ibuildings.nl BV
 * @license http://www.achievo.org/atk/licensing ATK Open Source License
 *

 */

/**
 * Implementation of the dhtml menu.
 *
 * @author Ber Dohmen <ber@ibuildings.nl>
 * @author Sandy Pleyte <sandy@ibuildings.nl>
 * @package adapto
 * @subpackage menu
 */
class Adapto_Menu_Dhtml extends Adapto_menuinterface
{
    public $m_height; // defaulted to public

    /**
     * Constructor
     *
     * @return Adapto_Menu_Dhtml
     */

    public function __construct()
    {
        $this->m_height = "50";
    }

    /**
     * Render the menu
     *
     * @return string The rendered menu
     */
    function render()
    {

        global $g_menu, $Adapto_VARS;
        $atkmenutop = $Adapto_VARS["atkmenutop"];
        if ($atkmenutop == "")
            $atkmenutop = "main";

        $tabs = "";
        $divs = "";
        $tab = 1;

        while (list($name) = each($g_menu)) {
            $tabContent = "";
            $atkmenutop = $name;
            $tabName = addslashes(atktext($atkmenutop, "", "menu"));
            $items = 0;

            for ($i = 0; $i < count($g_menu[$atkmenutop]); $i++) {
                $menu = "";
                $name = $g_menu[$atkmenutop][$i]["name"];
                $url = session_url($g_menu[$atkmenutop][$i]["url"], SESSION_NEW);
                $enable = $g_menu[$atkmenutop][$i]["enable"];

                // Check wether we have the rights and the item is not a root item
                if (is_array($enable) && $atkmenutop != "main" && $name != "-") {
                    $enabled = false;

                    // include every entity and perform an allowed() action on it
                    // to see wether we have ther rights to perform the action
                    for ($j = 0; $j < (count($enable) / 2); $j++) {
                        $action = $enable[(2 * $j) + 1];

                        $instance = &getEntity($enable[(2 * $j)]);
                        $enabled |= $instance->allowed($action);
                    }
                    $enable = $enabled;
                }

                /* delimiter ? */
                if ($g_menu[$atkmenutop][$i]["name"] == "-")
                    $menu .= "";

                /* normal menu item */
                else if ($enable) {
                    if ($g_menu[$atkmenutop][$i]["url"] != "") {
                        $tabContent .= "<a target='main' class='tablink' href='$url'>" . atktext($name, "", "menu") . "</a>";

                        if ($i < count($g_menu[$atkmenutop]) - 1) {
                            $tabContent .= "&nbsp;|&nbsp;";
                        }

                        $items++;
                    }
                }
            }

            if ($items > 0) {
                $tabs .= '   rows[1][' . $tab . '] = "' . $tabName . '"' . "\n";
                $divs .= '<div id="T1' . $tab . '" class="tab-body">' . $tabContent . '</div>' . "\n";
                $tab++;
            }
        }

        // add options tab containing logout
        $tabs .= '   rows[1][' . $tab . '] = "Opties"' . "\n";
        $divs .= '<div id="T1' . $tab . '" class="tab-body"><a class="tablink" href="index.php?atklogout=1" target="_top">' . atktext("logout", "atk")
                . '</a></div>' . "\n";

        $page = &atknew("atk.ui.atkpage");
        $theme = &atkinstance("atk.ui.atktheme");
        $page->register_style($theme->stylePath("style.css"));
        $page->register_style($theme->stylePath("dhtmlmenu.css"));
        $page->register_script(Adapto_Config::getGlobal("atkroot") . "atk/javascript/Adapto_tabs.js");

        $code = 'var tabSelectMode = "' . Adapto_Config::getGlobal("tabselectMode") . '";' . "\n";

        $code .= 'var rows     = new Array();
               public num_rows = 1; // defaulted to public
               public top      = 0; // defaulted to public
               public left     = 10; // defaulted to public
               public width    = "100%"; // defaulted to public
               public tab_off  = "#198DE9"; // defaulted to public
               public tab_on   = "#EEEEE0"; // defaulted to public

               rows[1]      = new Array;';
        $code .= "\n" . $tabs . "\n";

        $code .= "\n" . 'generateTabs();' . "\n";

        $page->register_scriptcode($code);

        $page->addContent($divs);

        $page
                ->addContent(
                        '<script language="JavaScript"  type="text/javascript">
                            if (DOM) { currShow=document.getElementById(\'T11\');}
                            else if (IE) { currShow=document.all[\'T11\'];}
                            else if (NS4) { currShow=document.layers[\'T11\'];}' . "\n" . 'changeCont("11", "tab11");' . "\n</script>");

        $string = $page->render("Menu", true);
        return $string;
    }

    /**
     * Get the menu height
     *
     * @return int The height of the menu
     */
    function getHeight()
    {
        return $this->m_height;
    }

    /**
     * Get the menu position
     *
     * @return int Menu is positioned at the top
     */
    function getPosition()
    {
        return MENU_TOP;
    }

    /**
     * Is this menu scrollable?
     *
     * @return int Menu is not scrollable
     */
    function getScrollable()
    {
        return MENU_UNSCROLLABLE;
    }

    /**
     * Is this menu multilevel?
     *
     * @return int This menu is not multilevel
     */
    function getMultilevel()
    {
        return MENU_NOMULTILEVEL;
    }
}

?>