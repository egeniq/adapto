<?php

/**
 * This file is part of the Adapto Toolkit.
 * Detailed copyright and licensing information can be found
 * in the doc/COPYRIGHT and doc/LICENSE files which should be
 * included in the distribution.
 *
 * @package adapto
 * @subpackage modules
 *
 * @copyright (c)2000-2004 Ibuildings.nl BV
 * @license http://www.achievo.org/atk/licensing ATK Open Source License
 *


 */

/**
 * The Adapto_Module abstract base class.
 *
 * All modules in an ATK application should derive from this class, and
 * override the methods of this abstract class as they see fit.
 *
 * @author petercv
 * @package adapto
 * @subpackage modules
 * @abstract
 */
class Adapto_Module
{
    /**
     * We keep track if the entity currently being instantiated is an
     * overloader or not.
     *
     * @var boolean
     */
    static $s_isOverloader = false;

    public $m_name; // defaulted to public

    /**
     * Constructor. The module needs to register it's entitys
     * overhere, create its menuitems etc.
     * @param String $name The name of the module.
     */

    public function __construct($name = "")
    {
        $this->m_name = $name;
    }

    /**
     * Register entitys with their supported actions. Can be used
     * for security etc.
     */
    function getEntitys()
    {
    }

    /**
     * Returns an array with filenames of attributes that need to be included
     * in order to let this module work properly.
     * @return array with attribute filenames
     */
    function getAttributes()
    {
    }

    /**
     * This method returns an array with menu items that need to be available
     * in the main ATK menu. This function returns the array created with
     * the menuitem() method, and does not have to be extended!
     * @return array with menu items for this module
     */
    function getMenuItems()
    {
    }

    /**
     * Create a new menu item, optionally configuring access control.  This 
     * function can also be used to create separators, submenus and submenu items.
     *
     * @param String $name The menuitem name. The name that is displayed in the
     *                     userinterface can be influenced by putting
     *                     "menu_something" in the language files, where 'something'
     *                     is equal to the $name parameter.
     *                     If "-" is specified as name, the item is a separator.
     *                     In this case, the $url parameter should be empty.
     * @param String $url The url to load in the main application area when the
     *                    menuitem is clicked. If set to "", the menu is treated
     *                    as a submenu (or a separator if $name equals "-").
     *                    The dispatch_url() method is a useful function to
     *                    pass as this parameter.
     * @param String $parent The parent menu. If omitted or set to "main", the
     *                       item is added to the main menu.
     * @param mixed $enable This parameter supports the following options:
     *                      1: menuitem is always enabled
     *                      0: menuitem is always disabled
     *                         (this is useful when you want to use a function
     *                         call to determine when a menuitem should be
     *                         enabled. If the function returns 1 or 0, it can
     *                         directly be passed to this method in the $enable
     *                         parameter.
     *                      array: when an array is passed, it should have the
     *                             following format:
     *                             array("entity","action","entity","action",...)
     *                             When an array is passed, the menu checks user
     *                             privileges. If the user has any of the
     *                             entity/action privileges, the menuitem is
     *                             enabled. Otherwise, it's disabled.
     * @param int $order The order in which the menuitem appears. If omitted,
     *                   the items appear in the order in which they are added
     *                   to the menu, with steps of 100. So, if you have a menu
     *                   with default ordering and you want to place a new
     *                   menuitem at the third position, pass 250 for $order.
     */
    function menuitem($name = "", $url = "", $parent = "main", $enable = 1, $order = 0)
    {
        /* call basic menuitem */
        if (empty($parent))
            $parent = 'main';
        menuitem($name, $url, $parent, $enable, $order, $this->m_name);
    }

    /**
     * This method can be used to return an array similar to the menu array
     * but with links to (a) preference(s) page(s) for this module. The items
     * that will be returned have to be added with the preferencelink() method.
     * @return array with preference links for this module
     */
    function getPreferenceLinks()
    {
    }

    /**
     * This method is similar to the getPreferenceLinks() method but instead
     * will return links to (an) admin page(s) for this module. The array which
     * will be returned have to be created with the adminlink() method.
     * @return array with admin links for this module
     */
    function getAdminLinks()
    {
    }

    /**
     * Returns the entity overloader if it exists. Else it
     * will just return the module/entity name of the given entity.
     * @param string $entity module/entity string
     * @return string (overloader) module/entity string
     */
    function entityOverloader($entity)
    {
        global $g_overloaders;

        /* overloader check */
        if (!empty($g_overloaders[$entity])) {
            atkdebug("Using overloader '" . $g_overloaders[$entity] . "' for class '" . $entity . "'");
            self::$s_isOverloader = true;
            $entity = newEntity($g_overloaders[$entity], FALSE);
            self::$s_isOverloader = false;
            return $entity;
        }
        /* no overloader */
        else {
            return null;
        }
    }

    /**
     * Returns the entity file for the given entity.
     *
     * @see entityFile()
     * @param string $entity the entity type
     * @return string entity filename
     */
    function getEntityFile($entity)
    {
        return entityFile($entity);
    }

    /**
     * Returns the fixture path for the given fixture.
     *
     * @param string $fixture <module.fixture> string
     * @return string path to fixture without extension
     */
    function getFixturePath($fixture)
    {
        $module = getEntityModule($fixture);
        $fixture = getEntityType($fixture);
        $path = moduleDir($module) . 'testcases/fixtures/' . $fixture;
        return $path;
    }

    /**
     * Construct a new entity. A module can override this method for it's own entitys.
     * @param atkEntity $entity the entity type
     * @return new entity object
     */
    function &newEntity($entity)
    {
        global $config_atkroot;

        /* include the base file */

        $corporate_base = Adapto_Config::getGlobal("corporate_entity_base");
        if ($corporate_base != "") {

        }

        /* check for file */
        $file = $this->getEntityFile($entity);
        if (!file_exists($file)) {

            $res = atkClassLoader::invokeFromString(Adapto_Config::getGlobal("missing_class_handler"), array(array("entity" => $entity, "module" => $this->m_name)));
            if ($res !== false) {
                return $res;
            } else {
                atkerror("Cannot create entity, because a required file ($file) does not exist!", "critical");
                return NULL;
            }
        }

        /* include file */
        include_once($file);

        /* module */
        $module = getEntityModule($entity);

        // set the current module scope, this will be retrieved in atkEntity
        // to set it's $this->m_module instance variable in an early stage
        if (!self::$s_isOverloader) {
            Adapto_Module::setModuleScope($module);
        }

        /* now that we have included the entity source file, we check
         * for overloaders (because overloaders might need the included file!)
         */
        $overloader = &$this->entityOverloader($entity);
        if ($overloader != NULL) {
            $overloader->m_module = $module;

            if (!self::$s_isOverloader) {
                Adapto_Module::resetModuleScope();
            }

            return $overloader;
        }

        /* initialize entity and return */
        $type = getEntityType($entity);
        $entity = new $type();
        $entity->m_module = $module;

        if (!self::$s_isOverloader) {
            Adapto_Module::resetModuleScope();
        }

        return $entity;
    }

    /**
     * Set current module scope.
     *
     * @param string $module current module
     * @static
     */
    function setModuleScope($module)
    {
        global $g_atkModuleScope;
        $g_atkModuleScope = $module;
    }

    /**
     * Returns the current module scope.
     *
     * @return string current module
     * @static
     */
    function getModuleScope()
    {
        global $g_atkModuleScope;
        return $g_atkModuleScope;
    }

    /**
     * Resets the current module scope.
     *
     * @static
     */
    function resetModuleScope()
    {
        Adapto_Module::setModuleScope(null);
    }

    /**
     * Checks if a certain entity exists for this module.
     * @param string $entity the entity type
     * @return entity exists?
     */
    function entityExists($entity)
    {
        // check for file
        $file = $this->getEntityFile($entity);
        return file_exists($file);
    }

    /**
     * Get the modifier functions for this entity
     *
     * @param atkEntity $entity
     * @return array Array with modifier function names
     */
    function getModifierFunctions(&$entity)
    {
        return array($entity->m_type . "_modifier", str_replace(".", "_", $entity->atkentitytype()) . "_modifier");
    }

    /**
     * Modifies the given entity
     *
     * @param atkEntity $entity Entity to be modified
     */
    function modifier(&$entity)
    {
        // Determine the modifier name and existance for modifiers that modify any entity having the this entity's type in any module
        $specificmodifiers = $this->getModifierFunctions($entity);

        // Set the number of applied modifiers to zero
        $appliedmodifiers = 0;

        // Loop through the possible modifiers and apply them if found
        foreach ($specificmodifiers as $modifiername) {
            // If the modifiers is found
            if (method_exists($this, $modifiername)) {
                // Add a debug line so we know, the modifier is applied
                atkdebug(sprintf("Applying modifier %s from module %s to entity %s", $modifiername, $this->m_name, $entity->m_type));

                // Apply the modifier
                $entity->m_modifier = $this->m_name;
                $this->$modifiername($entity);
                $entity->m_modifier = "";

                // Increase the number of applied modifiers
                $appliedmodifiers++;
            }
        }

        // If none of the modifiers was found, add a warning to the debug log
        if ($appliedmodifiers == 0)
            atkdebug(
                    sprintf("Failed to apply modifier function %s from module %s to entity %s; modifier function not found", implode(" or ", $specificmodifiers),
                            $this->m_name, $entity->m_type), DEBUG_WARNING);
    }
}
