<?php

/**
 * This file is part of the Adapto Toolkit.
 * Detailed copyright and licensing information can be found
 * in the doc/COPYRIGHT and doc/LICENSE files which should be
 * included in the distribution.
 *
 * @package adapto
 *
 * @copyright (c)2000-2004 Ivo Jansch
 * @license http://www.achievo.org/atk/licensing ATK Open Source License
 *

 */

/**
 * Config class for loading config files and retrieving config options.
 * Also contains misc. methods for use in config files.
 *
 * @author ijansch
 * @package adapto
 */
class Adapto_Config
{ 
    /**
     * Get a configuration value for a section (typically a module)
     *
     * Can be overridden with a global function config_$section_$tag.
     * Relies on your configurations being in configs/ (or wherever $config_configdir says).
     * Also gets $section.*.inc.php.
     * If the section is a module and has a skel/configs/ it will get those configs too
     * and use them as defaults.
     *
     * <b>Example:</b>
     *        Adapto_Config::get('color','mymodule','FF0000');
     *
     * @param string $section Section to check (typically a module)
     * @param string $tag     Name of configuration to get
     * @param mixed  $default Default to use if configuration value does not exist
     * @return mixed Configuration value
     */

    public static function get($section, $tag, $default = NULL)
    {
        static $s_configs = array();

        if (!isset($s_configs[$section])) {
            $config = self::getConfigForSection($section);
            
            $s_configs[$section] = $config;
        }   

        $elems = explode(".", $tag); 
       
        $root = $s_configs[$section];
        if (!is_object($root)) {
            throw new Adapto_Exception("Config section $section not found.");
        }
                
        while (count($elems)>1 && $root != NULL) {
            $item = array_shift($elems);
            $root = $root->get($item);
        }
        
        $value = NULL;
        if ($root != NULL) {
            $value = $root->get($elems[0]);
        }
        
        if ($value != NULL) {
            return $value;
        } else {
            if ($default === NULL) {
                throw new Adapto_Exception("Config value '$tag' not found in section '$section' and no default provided.");
            } else {
                return $default;
            }
        }
    }

    /**
     * Get the configuration values for a section and if the section
     * turns out to be a module, try to get the module configs
     * and merge them as fallbacks.
     *
     * @param string $section Name of the section to get configs for
     * @return Zend_Config Configuration values
     */

    public static function getConfigForSection($section)
    {
        $config = Zend_Registry::get("Config_".ucfirst($section));
        return $config;
    }


    /**
     * Is debugging enabled for client IP?
     *
     * @param array $params
     * @static
     */
    function ipDebugEnabled($params)
    {
        $ip = atkGetClientIp();
        return in_array($ip, $params["list"]);
    }

    /**
     * Is debugging enabled by special request variable?
     *
     * @param array $params
     * @static
     */
    function requestDebugEnabled($params)
    {
        $session = &atkSessionManager::getSession();

        if (isset($_REQUEST["Adapto_Util_Debugger::debug"]["key"])) {
            $session["debug"]["key"] = $_REQUEST["Adapto_Util_Debugger::debug"]["key"];
        } else if (isset($_COOKIE['Adapto_Util_Debugger::debug_KEY']) && !empty($_COOKIE['Adapto_Util_Debugger::debug_KEY'])) {
            $session["debug"]["key"] = $_COOKIE['Adapto_Util_Debugger::debug_KEY'];
        }

        return (isset($session["debug"]["key"]) && $session["debug"]["key"] == $params["key"]);
    }

    /**
     * Returns a debug level based on the given options for
     * dynamically checking/setting the debug level. If nothing
     * found returns the default level.
     *
     * @param int $default The default debug level
     * @param array $options  
     * @static
     */
    function smartDebugLevel($default, $options = array())
    {
        $session = &atkSessionManager::getSession();

        $enabled = $default > 0;

        foreach ($options as $option) {
            $method = $option["type"] . "DebugEnabled";
            if (is_callable(array("atkconfig", $method)))
                $enabled = $enabled || atkconfig::$method($option);
        }

        global $config_debug_enabled;
        $config_debug_enabled = $enabled;

        if ($enabled) {
            if (isset($_REQUEST["Adapto_Util_Debugger::debug"]["level"])) {
                $session["debug"]["level"] = $_REQUEST["Adapto_Util_Debugger::debug"]["level"];
            } else if (isset($_COOKIE['Adapto_Util_Debugger::debug_LEVEL'])) {
                $session["debug"]["level"] = $_COOKIE['Adapto_Util_Debugger::debug_LEVEL'];
            }

            if (isset($session["debug"]["level"]))
                return $session["debug"]["level"];
            else
                return max($default, 0);
        }

        return $default;
    }


}

/**
 * @todo module() and the MF_ flags should be moved to moduletools, but these are
 * not present yet at configfile load time.
 */

/**
 * Module flags
 */

/**
 * Don't use the menuitems from this module
 */
define("MF_NOMENU", 1);

/**
 * Don't use the rights of this module
 */
define("MF_NORIGHTS", 2);

/**
 * Use this module only as a reference
 */
define("MF_REFERENCE", MF_NOMENU | MF_NORIGHTS);

define("MF_SPECIFIC_1", 4);
define("MF_SPECIFIC_2", 8);
define("MF_SPECIFIC_3", 16);

/**
 * Don't preload this module (module_preload.inc)
 */
define("MF_NO_PRELOAD", 32);


