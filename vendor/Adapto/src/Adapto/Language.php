<?php
/**
 * This file is part of the Adapto Toolkit.
 * Detailed copyright and licensing information can be found
 * in the doc/COPYRIGHT and doc/LICENSE files which should be
 * included in the distribution.
 *
 * @package adapto
 *
 */

namespace Adapto;

/**
 * Class that handles userinterface internationalization.
 *
 * This class is used to retrieve the proper translations for any string
 * displayed in the userinterface. It includes only those language files
 * that are actually used, and has several fallback systems to find
 * translations if they can be find in the correct module.
 * 
 * TODO: move Adapto specific key/string handling to Adapto/I18n/Translator and
 * use Zend/I18n/Translator for all the hard work. Also find a way
 * to have Adapto strings and application level strings happily co-exist.
 *
 * @author Boy Baukema <boy@ibuildings.nl>
 * @package adapto
 *
 */
class Language
{
    private $_zendTranslate = NULL;
    
    /**
     * Default Constructor
     * @access private
     */

    public function __construct()
    {
        Util\Debugger::debug("New instance made of Adapto\Language");
    
    }
    
    public function _($string, $module="adapto", $entity = NULL, $lng = NULL)
    {
        return $this->text($string, $module, $entity, $lng);
    }

    /**
     * Text function, retrieves a translation for a certain string.
     * 
     * @param mixed $string           string or array of strings containing the name(s) of the string to return
     *                                when an array of strings is passed, the second will be the fallback if
     *                                the first one isn't found, and so forth
     * @param String $module          module in which the language file should be looked for,
     *                                defaults to core module with fallback to Adapto
     * @param String $entity            the entity to which the string belongs
     * @param String $lng             ISO 639-1 language code, defaults to config variable
     * @param String $firstfallback   the first module to check as part of the fallback
     * @param bool   $entityfaulttext   if true, then it doesn't returns false when it can't find a translation
     * @param bool   $modulefallback  Wether or not to use all the modules of the application in the fallback,
     *                                when looking for strings
     * @return String the string from the languagefile
     */

    public function text($string, $module="adapto", $entity = NULL, $lng = NULL)
    {
        // We don't translate nothing
        if ($string == '') {
            return '';
        }
            
        if ($lng == "") {
            $lng = $this->getLanguage();
        }
        
        $lng = strtolower($lng);
        
        // If only one string given, process it immediately
        if (!is_array($string))
            return $this->_getString($string, $module, $lng, $entity);

        // If multiple strings given, iterate through all strings and return the translation if found
        for ($i = 0, $_i = count($string); $i < $_i; $i++) {
            // Try to get the translation
            $translation = $this->_getString($string[$i], $module, $lng, $entity, ($i < ($_i - 1)));

            // Return the translation if found
            if ($translation != "")
                return $translation;
        }
        return "";

    }

    public function getSupportedLanguages()
    {
        return \Adapto\Config::get('adapto', 'language.supported_languages', array('en'));
    }

    /**
     * Get the current language, either from url, or if that's not present, from what the user has set.
     * @static
     * @return String current language.
     */

    public function getLanguage()
    {
        $session = new \stdClass(); // todo, inject proper session into the language handler. Zend_Registry::get('Session_Adapto');
        
        if (isset($session->language)
                && in_array($session->language, $this->getSupportedLanguages())) {
            $lng = $session->language;
        } else {
            $lng = $this->getUserLanguage();
            
            // Remember it
            $session->language = $lng;
        }
        return strtolower($lng);
    }



    /**
     * Get the selected language of the current user if he/she set one,
     * otherwise we try to get it from the browser settings and if even THAT
     * fails, we return the default language.
     *
     * @static
     * @return unknown
     */

    public function getUserLanguage()
    {
        $supported = $this->getSupportedLanguages();
            
        /** @todo this is what it did in ATK; must be rewritten to Zend_Auth etc.
        $sessionmanager = null;
        if (function_exists('atkGetSessionManager'))
            $sessionmanager = &atkGetSessionManager();
        if (!empty($sessionmanager)) {
            if (function_exists("getUser")) {
                $userinfo = getUser();
                $fieldname = Adapto\Config::get('adapto', 'auth_languagefield');
                if (isset($userinfo[$fieldname]) && in_array($userinfo[$fieldname], $supported))
                    return $userinfo[$fieldname];
            }
        } */

        // Otherwise we check the headers
        if (\Adapto\Config::get('adapto', 'language.use_browser_language', false)) {
            $headerlng = $this->getLanguageFromHeaders();
            if ($headerlng && in_array($headerlng, $supported))
                return $headerlng;
        }

        // We give up and just return the default language
        return \Adapto\Config::get('adapto', 'language', 'en'); 
    }

    /**
     * Get the primary languagecode that the user has set in his/her browser
     *
     * @static
     * @return String The languagecode
     */

    public static function getLanguageFromHeaders()
    {
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $langs = split('[,;]', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
            if ($langs[0] != "") {
                $elems = explode("-", $langs[0]); // lng might contain a subset after the dash.
                $autolng = $elems[0];
            }
        }
        return $autolng;
    }
    
    protected function _loadString($key, $module, $lng)
    {
        $strings = $this->_loadLanguage($module, $lng);
           
        return (isset($strings[$key])?$strings[$key]:$key);        
    }
    
    protected function _loadLanguage($module, $lng)
    {
        $strings = array();
        
        if ($module == "adapto") {
            $path = 'vendor/Adapto/language/';
        } else {
            $path = 'module/' . ucfirst($module) . '/language/';
        }
        
        $path .= $lng . '.php';
        
        if (!file_exists($path)) {
            throw new \Adapto\Exception("Language '$lng' not available in module '$module'");
        } else {
            
            include($path);
            return $$lng;
           
        }
        
    }
    
    /**
     * This function takes care of the fallbacks when retrieving a string ids.
     * It is as following:
     * First we check for a string specific to both the module and the entity
     * (module_entity_key).
     * If that isn't found we check for an entity specific string (entity_key).
     * And if all that fails we look for a general string in the module.
     *
     * @access protected
     *
     * @param string $key             the name of the string to return
     * @param string $module          module in which the language file should be looked for,
     *                                defaults to core module with fallback to Adapto
     * @param string $lng             ISO 639-1 language code, defaults to config variable
     * @param string $entity            the entity to which the string belongs
     * @param bool   $entityfaulttext   wether or not to pass a default text back
     * @param string $firstfallback   the first module to check as part of the fallback
     * @param bool   $modulefallback  Wether or not to use all the modules of the application in the fallback,
     *                                when looking for strings
     * @return string the name with which to call the string we want from the languagefile
     */

    protected function _getString($key, $module, $lng, $entity = "", $failSilently = false)
    {

        if ($entity != "") {
            $text = $this->_loadString($module . "_" . $entity . "_" . $key, $module, $lng);
            if ($text != "") {
                return $text;
            } else {
                $text = $this->_loadString($entity . "_" . $key, $module, $lng);
                if ($text != "") {
                    return $text;
                }
            }
        }

        $text = $this->_loadString($key, $module, $lng);

        if ($text != "") {
            return $text;
        }
        
        if (!$failSilently) {
			throw new \Adapto\Exception("Adapto\Language: translation for '$key' with module: '$module' and entity: '$entity' and language: '$lng' not found");
        }
        
        return "";

    }

}


