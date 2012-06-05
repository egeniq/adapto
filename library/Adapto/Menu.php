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
 * Some defines
 */
define("MENU_TOP", 1);
define("MENU_LEFT", 2);
define("MENU_BOTTOM", 3);
define("MENU_RIGHT", 4);
define("MENU_SCROLLABLE", 1);
define("MENU_UNSCROLLABLE", 2);
define("MENU_MULTILEVEL", 1); //More then 2 levels supported
define("MENU_NOMULTILEVEL", 2);

include_once(Adapto_Config::getGlobal("atkroot") . "atk/atkmenutools.inc");

/**
 * Menu utility class.
 *
 * This class is used to retrieve the instance of an atkMenuInterface-based
 * class, as defined in the configuration file.
 *
 * @author Ber Dohmen <ber@ibuildings.nl>
 * @author Sandy Pleyte <sandy@ibuildings.nl>
 * @package adapto
 * @subpackage menu
 */
class Adapto_Menu
{

    /**
     * Convert the layout name to a classname
     *
     * @param string $layout The layout name
     * @return string The classname
     */
    function layoutToClass($layout)
    {
        $classname = $layout;
        
        return $classname;
    }

    /**
     * Get the menu class
     *
     * @return string The menu classname
     */
    function getMenuClass()
    {
        // Get the configured layout class
        $classname = Adapto_Menu::layoutToClass(Adapto_Config::getGlobal("menu_layout"));
        Adapto_Util_Debugger::debug("Configured menu layout class: $classname");

        // Check if the class is compatible with the current theme, if not use a compatible menu.
        $theme = Adapto_ClassLoader::getInstance("Adapto_Ui_Theme");
        $compatiblemenus = $theme->getAttribute('compatible_menus');
        // If this attribute exists then retreive them
        if (is_array($compatiblemenus)) {
            for ($i = 0, $_i = count($compatiblemenus); $i < $_i; $i++)
                $compatiblemenus[$i] = Adapto_Menu::layoutToClass($compatiblemenus[$i]);
        }

        if (!empty($compatiblemenus) && is_array($compatiblemenus) && !in_array($classname, $compatiblemenus)) {
            $classname = $compatiblemenus[0];
            Adapto_Util_Debugger::debug("Falling back to menu layout class: $classname");
        }

        // Return the layout class name
        return $classname;
    }

    /**
     * Get new menu object
     *
     * @return object Menu class object
     */
    function &getMenu()
    {
        static $s_instance = NULL;
        if ($s_instance == NULL) {
            Adapto_Util_Debugger::debug("Creating a new menu instance");
            $classname = Adapto_Menu::getMenuClass();

            $filename = getClassPath($classname);
            if (file_exists($filename))
                $s_instance = Adapto_ClassLoader::create($classname);
            else {
                throw new Adapto_Exception('Failed to get menu object (' . $filename . ' / ' . $classname . ')!');
                atkwarning('Please check your compatible_menus in themedef.inc and config_menu_layout in config.inc.php.');
                $s_instance = Adapto_ClassLoader::create('atk.menu.atkplainmenu');
            }

            // Set the dispatchfile for this menu based on the theme setting, or to the default if not set.
            // This makes sure that all calls to dispatch_url will generate a url for the main frame and not
            // within the menu itself.
            $theme = Adapto_ClassLoader::getInstance("Adapto_Ui_Theme");
            $dispatcher = $theme->getAttribute('dispatcher', Adapto_Config::getGlobal("dispatcher", "dispatch.php")); // do not use atkSelf here!
            $c = Adapto_ClassLoader::getInstance("atk.atkcontroller");
            $c->setPhpFile($dispatcher);

            atkHarvestModules("getMenuItems");
        }

        return $s_instance;
    }

}

?>
