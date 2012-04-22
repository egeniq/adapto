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
 * @copyright (c)2007 Ibuildings.nl BV
 * @license http://www.achievo.org/atk/licensing ATK Open Source License
 *

 */

/**
 * Imports
 * @access private
 */

/**
 * The atkBoolAttribute class represents an attribute of an entity
 * that can either be true or false.
 *
 * @author Sandy Pleyte <sandy@achievo.org>
 * @package adapto
 * @subpackage menu
 *
 */
class Adapto_Menu_cookmenu extends Adapto_ModernMenu
{
    /**
     * Constructor
     *
     * @return Adapto_Menu_cookmenu
     */

    public function __construct()
    {
        $this->m_height = "22";
    }

    /**
     * Get the menu
     *
     * @return string The menu
     */
    function getMenu()
    {
        global $g_menu;
        $atkmenutop = "main";

        $menuitems = $this->getMenuItems($g_menu, $atkmenutop);
        $theme = &atkinstance("atk.ui.atktheme");
        $page = &atkinstance("atk.ui.atkpage");
        $page->register_style($theme->stylePath("style.css"));
        $page->register_style($theme->stylePath("cookmenu.css"));
        $page->register_scriptcode("var myThemePanelBase='" . str_replace('arrow.gif', '', $theme->imgPath("arrow.gif")) . "';", true);
        $page->register_script(Adapto_Config::getGlobal("atkroot") . "atk/javascript/jscookmenu/JSCookMenu.js");
        $page->register_script(Adapto_Config::getGlobal("atkroot") . "atk/javascript/jscookmenu/effect.js");
        $page->register_script(Adapto_Config::getGlobal("atkroot") . "atk/javascript/jscookmenu/theme.js");
        $ui = &atkinstance("atk.ui.atkui");

        $menu_javascript = '<script language="JavaScript" type="text/javascript">
                          public atkCookMenu = // defaulted to public
                          [' . "\n";
        $this->getJavascriptMenuItems($menuitems, $menu_javascript);
        $menu_javascript .= '];';

        $menu = '</script>

      <div id="Adapto_Menu_cookmenu"></div>
      <script language="JavaScript" type="text/javascript"><!--
      cmDraw (\'Adapto_Menu_cookmenu\', atkCookMenu, \'vbr\', cmThemePanel, \'ThemePanel\');
      --></script>
      ';

        $box = $ui->renderBox(array("menu_javascript" => $menu_javascript, "menu" => $menu), "menu");
        return $box;

    }

    /**
     * Get the javascript menu items
     *
     * @param array $menuitems
     * @param string $menu_javascript
     */
    function getJavascriptMenuItems($menuitems, &$menu_javascript)
    {
        foreach ($menuitems as $item) {
            if ($item["name"] == "-") {
                $menu_javascript .= "_cmSplit,\n";
            } else {
                if ($item["url"] != "") {
                    $url = "'" . $item["url"] . "'";
                    $target = "'_self'";
                } else {
                    $url = "null";
                    $target = "null";
                }
                $menu_javascript .= "[null, '" . addslashes($item["name"]) . "', " . $url . ", " . $target . ", null";
                if (isset($item["submenu"]) && count($item["submenu"]) > 0) {
                    $menu_javascript .= ',';
                    if ($item["header"] != "") {
                        $menu_javascript .= "[_cmNoAction, '<td colspan=\"3\"" . $item["header"] . "</td>', null, null, null],\n";
                        $menu_javascript .= "_cmSplit,\n";

                    }
                    $this->getJavascriptMenuItems($item["submenu"], $menu_javascript);
                }
                $menu_javascript .= "],\n";
            }
        }
    }

}
