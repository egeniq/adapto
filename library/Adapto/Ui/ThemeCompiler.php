<?php
/**
 * This file is part of the Adapto Toolkit.
 * Detailed copyright and licensing information can be found
 * in the doc/COPYRIGHT and doc/LICENSE files which should be
 * included in the distribution.
 *
 * @package adapto
 * @subpackage ui
 *
 * @copyright (c)2004-2006 Ibuildings.nl BV
 * @copyright (c)2004-2006 Ivo Jansch
 * @license http://www.achievo.org/atk/licensing ATK Open Source License
 *

 */

/**
 * Compiles cache for current theme.
 *
 * The compiler scans the theme directory and file structure and builds a
 * compiled file that contains the exact location of every themeable
 * element.
 *
 * If a theme is derived from another theme, the compiled theme contains the
 * sum of the parts, so a single compiled theme file contains every
 * information that ATK needs about the theme.
 *
 * @author Boy Baukema <boy@ibuildings.nl>
 * @author ijansch
 * @package adapto
 * @subpackage ui
 *
 */
class Adapto_Ui_ThemeCompiler
{

    /**
     * Compile a theme file for a certain theme.
     *
     * @param String $name The name of the theme to compile.
     */
    function compile($name)
    {
        // Process theme directory structure into data array.
        $data = $this->readStructure($name);
        
        // Write it to the compiled theme file
        if (count($data)) {
           
            if (!file_exists(Adapto_Config::get("adapto", "system.tempDir") . "/themes/")) {
                mkdir(Adapto_Config::get("adapto", "system.tempDir") . "/themes/");
            }

            $tmpfile = new Adapto_Util_TmpFile("themes/$name.inc");
            $tmpfile->writeAsPhp("theme", $data);
            return true;
        }
        return false;
    }

    /**
     * Parse theme structure.
     *
     * This method parses the themes directory and file structure and
     * converts it to a dataset containing all theme attributes and the
     * exact location of all themable files.
     *
     * This method also takes inheritance into account. If the theme derives
     * from another theme, the info for said theme is included too. This is
     * done recursively so themes can derive from any number of base themes.
     *
     * All themes are implicitly derived from the 'default' theme unless they
     * specify otherwise in their Config.php file.
     *
     * @param String $name The name of the theme
     * @param String $location The location of the theme ("atk", "app" or "auto")
     * @return array Theme dData structure
     */
    function readStructure($name, $location = "auto")
    {
        $data = array();

        $path = $this->findTheme($name, $location);
        
        if ($path == "") {
            // theme not found.
            $defaulttheme = Adapto_Config::get("adapto", "theme.name");
            if ($name != $defaulttheme) {
                // this is not the default theme, let's load that instead.
                return $this->readStructure($defaulttheme, "auto");
            } else {
                // this is the default theme, set in the config. If this doesn't exist, fallback to default.
                return $this->readStructure("default", "adapto");
            }
        }

        // First parse the themedef file for attributes
        if ($path != "" && file_exists($path . "Config.php")) {
            
            $className = 'Adapto_Theme_'.ucfirst($name).'_Config';
            
            $theme = new $className();

            if (isset($theme->baseTheme)) { // If theme is derived from another theme, use that other theme as basis
 
                $basethemelocation = isset($theme->baseThemeLocation) ? $theme->baseThemeLocation : "auto";
                $data = $this->readStructure($theme->baseTheme, $basethemelocation);
            } else if ($name != "default") { // If basetheme is not explicitly defined, use default as base theme

                $data = $this->readStructure("default", "auto");
            } else if ($name == "default" && $location == "app") { // if this theme is the app's default theme, use atk default as base
 
                $data = $this->readStructure("default", "adapto");
            } else {
                // end of the pipeline
            }

            if (isset($theme->parameters)) {
                foreach ($theme->parameters as $key => $value)
                    $data["parameters"][$key] = $value;
            }

            // Second scan all files in the theme path
            $this->_scanThemePath($name, $path, $data);
            $this->scanModulePath($name, $data);

            $data["parameters"]["basepath"] = $path;
        }
        return $data;
    }

    /**
     * Find the location on disk of a theme with a certain name.
     *
     * @param String $name Name of the theme
     * @param String $location The location of the theme ("adapto", "app" or "auto")
     *                         If set to auto, the method changes the $location
     *                         value to the actual location.
     */
    function findTheme($name, &$location)
    {        
        $pathBase = ucfirst($name);
        
        if (strpos($name, ".") !== false) {
            list($module, $name) = explode(".", $name);
            $path = Adapto_Module::pathForModule($module) . "themes/" . $pathBase . "/";
            if (file_exists($path . "Config.php")) {
                $location = "module";
                return "module/$module/themes/$pathBase/";
            }
        } else if ($location != "app" && file_exists(APPLICATION_PATH . "/../library/Adapto/Theme/$pathBase/Config.php")) {
            $location = "adapto";
            return APPLICATION_PATH . "/../library/Adapto/Theme/$pathBase/";
        }
        throw new Adapto_Exception("Theme $name not found");
        $location = "";
        return "";
    }

    /**
     * Traverse theme path.
     *
     * Traverses the theme path and remembers the physical location of all theme files.
     *
     * @param String $path The path of the theme, relative to atkroot.
     * @param String $abspath The absolute path of the theme
     * @param String $data Reference to the data array in which to report the file locations
     */
    private function _scanThemePath($themeName, $abspath, &$data)
    {
        $traverser = &Adapto_ClassLoader::create("Adapto_Util_DirectoryTraverser");
        $subitems = $traverser->getDirContents($abspath);
        foreach ($subitems as $name) {
            // images, styles and templates are compiled the same
            if (in_array($name, array("images", "img", "styles", "templates", "js"))) {
                $files = $this->_dirContents($abspath . $name);
                foreach ($files as $file) {
                    $key = $file;
                    
                    if (in_array($name, array("images", "img", "styles", "js"))) {
                        
                        if (!file_exists("adapto_static/".$themeName."/".$name)) {
                            if (!Adapto_Util_Files::mkdirRecursive("adapto_static/".$themeName.'/'.$name)) {
                                throw new Adapto_Exception("public/adapto_static is not writable.");
                            }
                        }           
                        
                        // Todo: move the installation of public files to a later stage, 'scan' must not have
                        // side effects.
                        copy($abspath."/".$name."/".$file, "adapto_static/".$themeName."/".$name."/".$file);
                    }
                    $data["files"][$name][$key] = $themeName."/".$name . "/" . $file;
                }
            } else if ($name == "icons") {
                $subs = $this->_dirContents($abspath . $name);
                foreach ($subs as $type) {
                    $files = $this->_dirContents($abspath . $name . "/" . $type);
                    foreach ($files as $file) {
                        
                          if (!file_exists("adapto_static/".$themeName."/".$name."/".$type)) {
                            if (!Adapto_Util_Files::mkdirRecursive("adapto_static/".$themeName.'/'.$name."/".$type)) {
                                throw new Adapto_Exception("public/adapto_static is not writable.");
                            }
                        }           
                        
                        // Todo: move the installation of public files to a later stage, 'scan' must not have
                        // side effects.
                        copy($abspath."/".$name."/".$type."/".$file, "adapto_static/".$themeName."/".$name."/".$type."/".$file);
                        $data["files"]["icons"][$type][$file] = $themeName . "/" . $name . "/" . $type . "/" . $file;
                    }
                }
            } 
        }
    }

    /**
     * Traverse module path.
     *
     * Traverses the module path and remembers the physical location of all theme files.
     *
     * @param String $theme The name of the theme
     * @param String $data Reference to the data array in which to report the file locations
     */
    function scanModulePath($theme, &$data)
    {
        $modules = array(); // todo, how do we ask ZF for a list of modules?

        $traverser = &Adapto_ClassLoader::create("Adapto_Util_DirectoryTraverser");
        foreach ($modules as $module => $modpath) {
            $abspath = $modpath . "themes/" . $theme . "/";

            if (is_dir($abspath)) {
                $subitems = $traverser->getDirContents($abspath);
                foreach ($subitems as $name) {
                    if (in_array($name, array("images", "styles", "templates"))) // images, styles and templates are compiled the same
 {
                        $files = $this->_dirContents($abspath . $name);
                        foreach ($files as $file) {
                            $data["modulefiles"][$module][$name][$file] = $theme . "/" . $name . "/" . $file;
                        }
                    } else if ($name == "icons") // new Adapto_5 style icon theme dirs
 {
                        $subs = $this->_dirContents($abspath . $name);
                        foreach ($subs as $type) {
                            $files = $this->_dirContents($abspath . $name . "/" . $type);
                            foreach ($files as $file) {
                                $data["modulefiles"][$module]["icons"][$type][$file] = $theme . "/" . $name . "/" . $type . "/" . $file;
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Get files for a directory
     *
     * @param string $path The directory to traverse
     * @return Array with files from the traversed directory
     */
    function _dirContents($path)
    {
        $files = array();
        $traverser = &Adapto_ClassLoader::create("Adapto_Util_DirectoryTraverser");
        $traverser->addExclude('/^\.(.*)/'); // ignore everything starting with a '.'
        $traverser->addExclude('/^CVS$/'); // ignore CVS directories
        $files = $traverser->getDirContents($path);
        return $files;
    }

}
