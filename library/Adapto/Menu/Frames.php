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
 * Implementation of the framestext menu.
 *
 * @author Ber Dohmen <ber@ibuildings.nl>
 * @package adapto
 * @subpackage menu
 */

class Adapto_Menu_Frames extends Adapto_PlainMenu
{

    /**
     * Render the menu
     *
     * @return string The rendered menu
     */
    function render()
    {
        global $Adapto_VARS, $g_menu, $g_menu_parent;
        $atkmenutop = atkArrayNvl($Adapto_VARS, "atkmenutop", "main");
        $menu = "<div align='" . Adapto_Config::getGlobal("menu_align", "center") . "'>";
        $menu .= $this->getHeader($atkmenutop);
        if (is_array($g_menu[$atkmenutop])) {
            usort($g_menu[$atkmenutop], array("atkplainmenu", "menu_cmp"));
            $menuitems = array();
            for ($i = 0; $i < count($g_menu[$atkmenutop]); $i++) {
                if ($i == count($g_menu[$atkmenutop]) - 1) {
                    $delimiter = "";
                } else {
                    $delimiter = Adapto_Config::getGlobal("menu_delimiter");
                }
                $name = $g_menu[$atkmenutop][$i]["name"];
                $menuitems[$i]["name"] = $name;
                $url = $g_menu[$atkmenutop][$i]["url"];
                $enable = $g_menu[$atkmenutop][$i]["enable"];
                $modname = $g_menu[$atkmenutop][$i]["module"];

                if (is_array($enable)) {
                    $enabled = false;
                    for ($j = 0; $j < (count($enable) / 2); $j++) {
                        $enabled |= is_allowed($enable[(2 * $j)], $enable[(2 * $j) + 1]);
                    }
                    $enable = $enabled;
                }

                $menuitems[$i]["enable"] = $enable;
                $menuitems[$i]["url"] = $url;
                $menuitems[$i]['module'] = $modname;

                /* delimiter ? */
                if ($name == "-")
                    $menu .= $delimiter;

                else if ($enable) // don't show menu items we don't have access to.
 {

                    $hassub = isset($g_menu[$g_menu[$atkmenutop][$i]["name"]]);

                    /* submenu ? */
                    if ($hassub) {
                        if (empty($url)) // normal submenu
 {
                            $menu .= href('menu.php?atkmenutop=' . $name, $this->getMenuTranslation($name, $modname)) . $delimiter;
                        } else // submenu AND a default url.
 {
                            $menuurl = session_url('menu.php?atkmenutop=' . $name);
                            $mainurl = session_url($url, SESSION_NEW);
                            $menu .= '<a href="javascript:menuload(\'' . $menuurl . '\', \'' . $mainurl . '\');">' . $this->getMenuTranslation($name, $modname)
                                    . '</a>' . $delimiter;
                        }
                    } else // normal menuitem
 {
                        $menu .= href($url, $this->getMenuTranslation($name, $modname), SESSION_NEW, false, 'target="main"') . $delimiter;
                    }
                }
            }
        }
        /* previous */
        if ($atkmenutop != "main") {
            $parent = $g_menu_parent[$atkmenutop];
            $menu .= Adapto_Config::getGlobal("menu_delimiter");
            $menu .= href('menu.php?atkmenutop=' . $parent, atktext("back_to", "atk") . ' ' . $this->getMenuTranslation($parent, $modname), SESSION_DEFAULT)
                    . $delimiter;
        }
        $menu .= $this->getFooter($atkmenutop);
        $page = &atknew("atk.ui.atkpage");
        $theme = &atkinstance("atk.ui.atktheme");
        $page->register_style($theme->stylePath("style.css"));
        $menustylepath = $theme->stylePath("menu.css");
        if (!empty($menustylepath))
            $page->register_style($menustylepath);
        $page->register_script(Adapto_Config::getGlobal("atkroot") . "atk/javascript/menuload.js");

        $ui = &atkinstance("atk.ui.atkui");

        $box = $ui
                ->renderBox(
                        array("title" => $this->getMenuTranslation($atkmenutop, $modname), "content" => $menu, "menuitems" => $menuitems,), "menu");

        $page->addContent($box);

        return $page->render("Menu", true);
    }
}
?>
