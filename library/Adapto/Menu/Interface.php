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
 * Abstract baseclass (interface) for all menu implementations. Any menu
 * implementation should extend this class and override the methods
 * defined by this interface.
 *
 * @author ijansch
 * @author Sandy Pleyte <sandy@ibuildings.nl>
 * @package adapto
 * @subpackage menu
 */
class Adapto_Menu_Interface
{
    public $m_height; // defaulted to public

    /**
     * Render the menu
     * @return String HTML fragment containing the menu.
     */
    function render()
    {
    }

    /**
     * Translates a menuitem with the menu_ prefix, or if not found without
     *
     * @param String $menuitem Menuitem to translate
     * @param String $modname Module to which the menuitem belongs
     * @return Translation of the given menuitem
     */
    function getMenuTranslation($menuitem, $modname = 'atk')
    {
        return atktext(array("menu_$menuitem", $menuitem), $modname);
    }

    /**
     * Return the menu header
     * 
     * @param string $atkmenutop
     * @return string The menu header
     */
    function getHeader($atkmenutop)
    {
    }

    /**
     * Return the menu footer
     * 
     * @param string $atkmenutop
     * @return string The menu footer
     */
    function getFooter($atkmenutop)
    {
    }

    /**
     * If the menu is displayed in the top frame of the application, this
     * method should return the height of the frame that the menu requires.
     *
     * The framework calls this method to determine the frameset dimensions.
     * @return int The required frame height.
     */
    function getHeight()
    {
    }

    /**
     * Retrieve the position in which the menu is displayed.
     *
     * The framework calls this method to determine the structure of the
     * frameset.
     * @return int The MENU_* frameposition.
     */
    function getPosition()
    {
    }

    /**
     * Retrieve the scrolling possibilities of the menu.
     * @return int the MENU_* scroll definition
     */
    function getScrollable()
    {
    }

    /**
     * Determine if the menu can handle multiple levels
     * of submenu.
     * @return boolean True if multiple levels are supported, false if each
     *                 menu can only have one level of submenuitems.
     */
    function getMultilevel()
    {
    }

    /**
     * Recursively checks if a menuitem should be enabled or not.
     *
     * @param array $menuitem menuitem array
     * @return enabled?
     */
    function isEnabled($menuitem)
    {
        global $g_menu;

        $enable = $menuitem['enable'];
        if ((is_string($enable) || (is_array($enable) && count($enable) == 2 && is_object(@$enable[0]))) && is_callable($enable)) {
            $enable = call_user_func($enable);
        } else if (is_array($enable)) {
            $enabled = false;
            for ($j = 0; $j < (count($enable) / 2); $j++) {
                $enabled = $enabled || is_allowed($enable[(2 * $j)], $enable[(2 * $j) + 1]);
            }
            $enable = $enabled;
        } else if (array_key_exists($menuitem['name'], $g_menu) && is_array($g_menu[$menuitem['name']])) {
            $enabled = false;
            foreach ($g_menu[$menuitem['name']] as $item)
                $enabled = $enabled || $this->isEnabled($item);
            $enable = $enabled;
        }

        return $enable;
    }
}

?>