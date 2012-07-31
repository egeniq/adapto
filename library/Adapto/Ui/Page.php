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
 * @copyright (c)2000-2004 Ivo Jansch
 * @license http://www.achievo.org/atk/licensing ATK Open Source License
 *

 */

/**
 * Rendering flags.
 */
define("HTML_BODY", 1); // Add body tags to page
define("HTML_HEADER", 2); // Add header to page
define("HTML_DOCTYPE", 4); // Add doctype to page
define("HTML_ALL", HTML_BODY | HTML_HEADER); // Shortcut
define("HTML_STRICT", HTML_ALL | HTML_DOCTYPE); // Shortcut
define("HTML_PARTIAL", 8); // Partial (only content, this flag can't be ANDed!)

/**
 * Page renderer.
 *
 * This class is used to render output as an html page. It takes care of
 * creating a header, loading javascripts and loading stylesheets.
 * Since any script will output exactly one page to the browser, this is
 * a singleton. Use getInstance() to retrieve the one-and-only instance.
 *
 * @todo This should actually not be a singleton. HTML file generation
 *       scripts may need an instance per page generated.
 *
 * @author ijansch
 * @package adapto
 * @subpackage ui
 *
 */
class Adapto_Ui_Page
{
    /**
     * The list of javascript files to load.
     * @access private
     * @var array
     */
    public $m_metacode = array(); // defaulted to public

    /**
     * The list of javascript files to load.
     * @access private
     * @var array
     */
    public $m_scriptfiles = array(); // defaulted to public

    /**
     * List of javascript code statements to include in the header.
     * @access private
     * @var array
     */
    public $m_scriptcode = array("before" => array(), "after" => array()); // defaulted to public

    /**
     * List of javascript code statements to execute when a form on
     * the page is submitted.
     * @access private
     * @var array
     */
    public $m_submitscripts = array(); // defaulted to public

    /**
     * List of javascript code statements to execute when the page
     * is loaded.
     * @access private
     * @var array
     */
    public $m_loadscripts = array(); // defaulted to public

    /**
     * List of stylesheet files to load.
     * @access private
     * @var array
     */
    public $m_stylesheets = array(); // defaulted to public

    /**
     * List of style statements to include in the header.
     * @access private
     * @var array
     */
    public $m_stylecode = array(); // defaulted to public

    /**
     * The content to put on the page.
     * @access private
     * @var String
     */
    public $m_content = ""; // defaulted to public

    /**
     * The hidden variables for the page
     * @access private
     * @var Array
     */
    public $m_hiddenvars = array(); // defaulted to public

    /**
     * Page title.
     * 
     * @var string
     */
    protected $m_title = '';

    /**
     * The controller we're rendering this page into.
     * @var Adapto_Controller_Action
     */
    private $_controller = NULL;

    /**
     * Retrieve the one-and-only Adapto_Ui_Page instance.
     * @return Adapto_Ui_Page
     */

    static public function &getInstance()
    {
        static $s_page = NULL;
        if ($s_page == NULL) {
            $s_page = new Adapto_Ui_Page();
            Adapto_Util_Debugger::debug("Created a new Adapto_Ui_Page instance");
        }
        return $s_page;
    }

    /**
     * Constructor.
     */

    public function __construct(Adapto_Controller_Action $controller)
    {
        $this->_controller = $controller;
    }

    /**
     * Register a javascript file to be included.
     *
     * If called twice for the same filename, the file is loaded only once.
     * @param String $file The (relative path and) filename of the javascript
     *                     file.
     * @param String $before The (partial) name of a script that this script
     *                       should be loaded in front of. This can be used
     *                       to inject a script before another script, or to
     *                       avoid conflicts. Usually, this parameter is not
     *                       needed.
     */
    public function registerScript($file, $before = "")
    {
        if (!in_array($file, $this->m_scriptfiles)) {
            if ($before == "") {
                $this->m_scriptfiles[] = $file;
            } else {
                // lookup the dependency and inject script right before it.
                $result = array();
                $injected = false;
                for ($i = 0, $_i = count($this->m_scriptfiles); $i < $_i; $i++) {
                    if (stristr($this->m_scriptfiles[$i], $before) !== false) {
                        // inject the new one here.
                        $result[] = $file;
                        $injected = true;
                    }
                    $result[] = $this->m_scriptfiles[$i];
                }
                if (!$injected)
                    $result[] = $file;
                // inject at the end if dependency not found.

                $this->m_scriptfiles = $result;
            }
        }
        
    }

    /**
     * Unregister all registered javascripts
     *
     */
    function unregister_all_scripts()
    {
        $this->m_scriptfiles = array();
    }

    /**
     * Return all javascript files
     *
     * @return array contain file paths
     */
    function getScripts()
    {
        return $this->m_scriptfiles;
    }

    /**
     * Register a javascript code statement which will be rendered in the
     * header.
     *
     * The method has a duplicate check. Registering the exact same statement
     * twice, will result in the statement only being rendered and executed
     * once.
     * @param String $code The javascript code to place in the header.
     * @param Boolean $before Include the script before the javascript files
     */
    function register_scriptcode($code, $before = false)
    {
        $element = ($before ? 'before' : 'after');
        if (!in_array($code, $this->m_scriptcode[$element]))
            $this->m_scriptcode[$element][] = $code;
    }

    /**
     * Register a javascript code statement that is executed when a form on
     * the page is submitted.
     * @todo This is inconsequent, if multiple forms are present, each should
     *       have its own submitscripts. Should be moved to an atkForm class.
     * @param String $code The javascript code fragment to execute on submit.
     */
    function register_submitscript($code)
    {
        if (!in_array($code, $this->m_submitscripts))
            $this->m_submitscripts[] = $code;
    }

    /**
     * Returns a copy of the load scripts.
     */

    public function getLoadScripts()
    {
        return $this->m_loadscripts;
    }

    /**
     * Register a javascript code statement that is executed on pageload.
     * @param String $code The javascript code fragment to execute on load.
     */
    function register_loadscript($code, $offset = null)
    {
        if (!in_array($code, $this->m_loadscripts) && $offset === null) {
            $this->m_loadscripts[] = $code;
        } else if (!in_array($code, $this->m_loadscripts)) {
            array_splice($this->m_loadscripts, $offset, 0, $code);
        }
    }

    /**
     * Return all javascript codes in an array
     *
     * @return array
     */
    function getScriptCodes()
    {
        $scriptCodes = array_merge($this->m_scriptcode['before'], $this->m_scriptcode['after']);
        $scriptCodes[] = $this->_getGlobalSubmitScriptCode();
        $scriptCodes[] = $this->_getGlobalLoadScriptCode();

        return $scriptCodes;
    }

    /**
     * Register a Cascading Style Sheet.
     *
     * This method has a duplicate check. Calling it with the same stylesheet
     * more than once, will still result in only one single include of the
     * stylesheet.
     * @param String $file  The (relative path and) filename of the stylesheet.
     * @param String $media The stylesheet media (defaults to 'all').
     */
    function registerStyle($file, $media = 'all')
    {
        if (empty($media)) {
            $media = 'all';
        }

        if (!array_key_exists($file, $this->m_stylesheets)) {
            $this->m_stylesheets[$file] = $media;
        }
    }

    /**
     * Unregister a Cascading Style Sheet.
     *
     * @param String $file  The (relative path and) filename of the stylesheet.
     */
    public function unregisterStyle($file)
    {
        if (array_key_exists($file, $this->m_stylesheets)) {
            unset($this->m_stylesheets[$file]);
        }
    }

    /**
     * Return all stylesheet files
     *
     * @return array contain file paths
     */
    function getStyles()
    {
        return $this->m_stylesheets;
    }

    /**
     * Register Cascading Style Sheet fragment that will be included in the
     * page header.
     * @param String $code The Cascading Style Sheet code fragment to place in
     *                     the header.
     */
    function register_stylecode($code)
    {
        if (!in_array($code, $this->m_stylecode))
            $this->m_stylecode[] = $code;
    }

    /**
     * Return all style codes
     *
     * @return array
     */
    function getStyleCodes()
    {
        return $this->m_stylecode;
    }

    /**
     * Register hidden variables. These will be accessible to javascript and DHTML functions/scripts
     * but will not be shown to the user unless he/she has a very, very old browser
     * that is not capable of rendering CSS
     * @param array $hiddenvars the hiddenvariables we want to register
     */
    function register_hiddenvars($hiddenvars)
    {
        foreach ($hiddenvars as $hiddenvarname => $hiddenvarvalue) {
            $this->m_hiddenvars[$hiddenvarname] = $hiddenvarvalue;
        }
    }

    /**
     * Generate the HTML header (<head></head>) statement for the page,
     * including all scripts and styles.
     * @param String $title Title of the html page.
     * @param String $extra_header HTML code of extra headers to add to the head section
     * @return String The HTML pageheader, including <head> and </head> tags.
     */
    function head($title, $extra_header = "")
    {
        $this->_controller->view->headTitle($title);

        $version = Adapto_About::getVersion();
       
        $this->_controller->view->headMeta()->appendName('adapto_version', $version);

        if ($extra_header != "")
            $res .= $extra_header . "\n";

        $this->_applyMeta($res);
        $this->_applyScripts();
        $this->_applyStyles();

        $favico = Adapto_Config::get('adapto', 'ui.favico', '');
        if ($favico != '') {
            $res .= '  <link rel="icon" href="' . $favico . '" type="image/x-icon" />' . "\n";
            $res .= '  <link rel="shortcut icon" href="' . $favico . '" type="image/x-icon" />' . "\n";
        }

    }

    /**
     * Adds javascripts from the member variables to HTML output
     * @param String $res Reference to the HTML output
     * @param Bool $partial Is this a partial request or a complete request
     */
    protected function _applyScripts($partial = false)
    {
        // Todo: re-support injecting code here
        // $res .= $this->renderScriptCode("before");

        if (!$partial) {
            for ($i = 0; $i < count($this->m_scriptfiles); $i++) {
                $this->_controller->view->headScript()->appendFile($this->m_scriptfiles[$i]);
            }     
        } else {
            $files = '';
            for ($i = 0; $i < count($this->m_scriptfiles); $i++) {
                $files .= "ATK.Tools.loadScript('" . $this->m_scriptfiles[$i] . "');\n";
            }

            if (!empty($files)) {
                // prepend script files
                $this->_controller->view->headScript()->prependScript($files);
            }
        }

        // Todo: re-support injecting code here 
        // $res .= $this->renderScriptCode("after");

        // Todo: decide what to do with these global submit thingees.
        // $res .= $this->_getGlobalSubmitScriptCode($partial);
        // $res .= $this->_getGlobalLoadScriptCode($partial);
    }

    /**
     * Renders the registered javascripts, if $position is set to "before" the scripts will be
     * placed before the scripts that are already present. Otherwise they will be appended
     * at the end.
     *
     * @param string $position ("before" or "after")
     * @return string 
     */
    function renderScriptCode($position)
    {
        $res = "";
        for ($i = 0, $_i = count($this->m_scriptcode[$position]); $i < $_i; $i++) {
            $res .= $this->m_scriptcode[$position][$i] . "\n";
        }
        return $res;
    }

    /**
     * Get the globalSubmit javascript code
     *
     * @param bool $partial Is this a partial request or a complete request
     * @return String with javascript code
     */
    function _getGlobalSubmitScriptCode($partial = false)
    {
        // global submit script can only be registered in the original request
        if ($partial) {
            return "";
        }

        $res = "\n    function globalSubmit(form)\n";
        $res .= "    {\n";
        $res .= "      public retval = true;\n"; // defaulted to public

        for ($i = 0, $_i = count($this->m_submitscripts); $i < $_i; $i++) {
            $res .= "      retval = " . $this->m_submitscripts[$i] . "\n";
            $res .= "      if (retval!=true) return false;\n";
        }

        $res .= "      return retval;\n";
        $res .= "    }\n";
        return $res;
    }

    /**
     * Get the globalLoad javascript code
     *
     * @param bool $partial Is this a partial request or a complete request
     * @return String with javascript code
     */
    function _getGlobalLoadScriptCode($partial = false)
    {
        $res = '';
        if (count($this->m_loadscripts)) {
            $res = "";
            if (!$partial) {
                $res .= "function globalLoad()\n";
                $res .= "{\n";
            }

            for ($i = 0, $_i = count($this->m_loadscripts); $i < $_i; $i++) {
                $res .= "{$this->m_loadscripts[$i]}\n";
            }

            if (!$partial) {
                $res .= "}\n";
                $res .= "document.observe('dom:loaded', globalLoad);\n";
            }
        }
        return $res;
    }

    /**
     * Add stylesheets and stylecodes to the HMTL output
     *
     * @param String $res Reference to the HTML output
     * @param Bool $partial Is this a partial request or a complete request
     */
    protected function _applyStyles($partial = false)
    {
        if (!$partial) {
            foreach ($this->m_stylesheets as $file => $media) {
                $this->_controller->view->headLink()->appendStylesheet("adapto_static/".$file, $media);
            }

            for ($i = 0; $i < count($this->m_stylecode); $i++) {
                $this->_controller->view->headStyle()->appendStyle($this->m_stylecode[$i]);
            }
        } else {
            $files = '';
            foreach ($this->m_stylesheets as $file => $media) {
                $files .= "ATK.Tools.loadStyle('{$file}', '{$media}');\n";
            }

            if (!empty($files)) {
                // prepend stylesheets
                $res = '<script type="text/javascript">' . $files . '</script>' . $res;
            }
        }
    }

    /**
     * Add content to the page.
     * @param String $content The content to add to the page.
     */
    public function addContent($content)
    {
        $this->m_content .= $content;
    }

    /**
     * Returns the current page content.
     *
     * @return string current page content
     */
    function getContent()
    {
        return $this->m_content;
    }

    /**
     * Sets the page content (overwriting current content).
     *
     * @param string $content new page content
     */
    function setContent($content)
    {
        $this->m_content = $content;
    }

    /**
     * Generate the HTML body (<body></body>) statement for the page.
     * @param String $extraprops Extra attributes to add to the <body> tag.
     * @return String The HTML body, including <body> and </body> tags.
     */
    function body($extraprops = "")
    {
        global $Adapto_VARS;

        $res = '<body ';
        $res .= $extraprops . ">\n";
        return $res;
    }

    /**
     * Sets the page title.
     * 
     * @param string $title page title
     */

    public function setTitle($title)
    {
        $this->m_title = $title;
    }
    
    protected function _applyTheme()
    {
        $theme = Adapto_ClassLoader::getInstance('Adapto_Ui_Theme');
        foreach ($theme->pageStyles() as $style) {
            $this->registerStyle($style);
        }
    }
    
    protected function _applyBaseJs()
    {
        $this->registerScript('adapto_static/default/js/jquery-1.7.2.min.js');
    }

    /**
     * Render the complete page, including head and body.
     * @param String $title Title of the HTML page.
     * @param bool|int $flags (bool) Set to true to generate <body> tags. It is useful
     *                      	to set this to false only when rendering content
     *                      	that either already had its own <body></body>
     *                      	statement, or content that needs no body
     *                      	statements, like a frameset. (DEPRICATED !!)
     * 											  (int) Flags for the render function
     * @param string $extrabodyprops  Extra attributes to add to the <body> tag.
     * @param string $extra_header HTML code of extra headers to add to the head section
     * @return String The HTML page, including <html> and </html> tags.
     */
    public function render($title = null, $flags = HTML_STRICT, $extrabodyprops = "", $extra_header = "")
    {
        $this->_applyTheme();
        $this->_applyBaseJs();
        
        if ($title == null) {
            $title = $this->m_title;
        }

        $ui = Adapto_ClassLoader::getInstance('Adapto_Ui');
        $theme = Adapto_ClassLoader::getInstance('Adapto_Ui_Theme');

        if (is_bool($flags) && $flags == true) {
            $flags = HTML_STRICT;
        } elseif (is_bool($flags) && $flags == false) {
            $flags = HTML_HEADER | HTML_DOCTYPE;
        } elseif (hasFlag($flags, HTML_PARTIAL)) {
            return $this->renderPartial();
        }
        
        if ($theme->tplPath('page.tpl'))
            $this->m_content = $ui->render('page.tpl', array('content' => $this->m_content));

        $page = '';

        if ($flags & HTML_HEADER)
            $this->head($title, $extra_header);
        if ($flags & HTML_BODY)
            $this->body($extrabodyprops);

        $page .= $this->m_content . "\n";
        $page .= $this->renderHiddenVars();
     
        return $page;
    }

    /**
     * Render partial.
     */
    public function renderPartial()
    {
        $result = $this->m_content;
        $this->_applyMeta($result, true);
        $this->_applyScripts(true);
        $this->_applyStyles(true);
        return $result;
    }

    /**
     * Here we render a hidden div in the page with hidden variables
     * that we want to make accessible to client side scripts
     * @return string a hidden div with the selected ATK variabels
     */
    function renderHiddenVars()
    {
        $page = "";
        if ($this->m_hiddenvars) {
            $page .= "\n" . '<div id="hiddenvars" style="display: none">';
            foreach ($this->m_hiddenvars as $hiddenvarname => $hiddenvarvalue) {
                $page .= "\n <span id='$hiddenvarname'>$hiddenvarvalue</span>";
            }
            $page .= "\n</div>";
        }
        return $page;
    }

    /**
     * Check if the page is empty (no content).
     *
     * This is useful to check at the rendering stage of scripts whether there is something to render.
     *
     * @return boolean true if there is no content in the page, false if there is
     */
    public function isEmpty()
    {
        return ($this->m_content == "");
    }

    /**
     * Add a meta tag to the page: <meta ... />
     * 
     * @param string $code 
     */
    public function registerMetacode($code)
    {
        $this->m_metacode[] = $code;
    }

    /**
     * Adds meta lines from the member variables to HTML output
     * @param String $res Reference to the HTML output
     * @param Bool $partial Is this a partial request or a complete request
     */
    protected function _applyMeta(&$res, $partial = false)
    {
        if (!$partial) {
            foreach ($this->m_metacode as $line) {
                $res .= "  " . $line . "\n";
            }
        }
    }

}

?>
