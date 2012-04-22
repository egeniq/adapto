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
 * @copyright (c)2007 Ibuildings.nl BV
 * @license http://www.achievo.org/atk/licensing ATK Open Source License
 *

 */

/**
 * ATK dialog helper class.
 *
 * @author petercv
 * @package adapto
 * @subpackage ui
 */
class Adapto_Ui_Dialog
{
    protected $m_entityType;

    protected $m_action;
    protected $m_partial;
    protected $m_params;
    protected $m_sessionStatus = SESSION_NEW;

    protected $m_title;

    protected $m_themeName;
    protected $m_themeLoad;

    protected $m_width = null;
    protected $m_height = null;

    protected $m_serializeForm = null;

    protected $m_modifierObject = null;

    /**
     * Constructor.
     *
     * @param string $entityType entity type
     * @param string $action   entity action
     * @param string $partial  partial name
     * @param array  $params   url parameters
     *
     * @return Adapto_Ui_Dialog
     */
    function __construct($entityType, $action, $partial = 'dialog', $params = array())
    {
        $this->m_entityType = $entityType;
        $this->m_action = $action;
        $this->m_partial = $partial;
        $this->m_params = $params;

        $ui = atkUI::getInstance();
        $module = getEntityModule($entityType);
        $type = getEntityType($entityType);
        $this->m_title = $ui->title($module, $type, $action);

        $theme = atkTheme::getInstance();
        $this->m_themeName = $theme->getAttribute('dialog_theme_name', 'alphacube');
        $this->m_themeLoad = $theme->getAttribute('dialog_theme_load', true);
    }

    /**
     * Returns the dialog entity type.
     *
     * @return string entity type
     */

    public function getEntityType()
    {
        return $this->m_entityType;
    }

    /**
     * Returns the dialog action.
     *
     * @return string action
     */

    public function getAction()
    {
        return $this->m_action;
    }

    /**
     * Returns the dialog partial.
     * 
     * @return string partial
     */

    public function getPartial()
    {
        return $this->m_partial;
    }

    /**
     * Returns the dialog sessionStatus.
     *
     * @return int sessionStatus (SESSION_BACK=3, SESSION_DEFAULT=0, SESSION_NESTED=2, SESSION_NEW=1, SESSION_PARTIAL=5, SESSION_REPLACE=4)
     */

    public function getSessionStatus()
    {
        return $this->m_sessionStatus;
    }

    /**
     * Sets the dialog sessionStatus.
     *
     * @param int $sessionStatus (SESSION_BACK=3, SESSION_DEFAULT=0, SESSION_NESTED=2, SESSION_NEW=1, SESSION_PARTIAL=5, SESSION_REPLACE=4)
     */

    public function setSessionStatus($sessionStatus)
    {
        $this->m_sessionStatus = (int) $sessionStatus;
    }

    /**
     * Sets the dialog title.
     *
     * @param string $title
     */
    function setTitle($title)
    {
        $this->m_title = $title;
    }

    /**
     * Sets the theme to use.
     *
     * @param string  $name theme name
     * @param boolean $load load theme?
     */
    function setTheme($name, $load = true)
    {
        $this->m_themeName = $name;
        $this->m_themeLoad = $load;
    }

    /**
     * Reset to auto-size.
     */
    function setAutoSize()
    {
        $this->m_width = null;
        $this->m_height = null;
    }

    /**
     * Set dialog dimensions.
     *
     * @param int $width width
     * @param int $height height
     */
    function setSize($width, $height)
    {
        $this->m_width = $width;
        $this->m_height = $height;
    }

    /**
     * Set width.
     *
     * @param int $width
     */

    public function setWidth($width)
    {
        $this->m_width = $width;
    }

    /**
     * Set height.
     *
     * @param int $height
     */

    public function setHeight($height)
    {
        $this->m_height = $height;
    }

    /**
     * Serialize the form with the given name.
     * Defaults to the entryform.
     *
     * @param string $form form name
     */
    function setSerializeForm($form = 'entryform')
    {
        $this->m_serializeForm = $form;
    }

    /**
     * Returns the modifier object.
     * 
     * @return mixed modifier object
     */

    public function getModifierObject()
    {
        return $this->m_modifierObject;
    }

    /**
     * Sets an object which modifyObject method will be called (if exists) just
     * before showing the dialog. This method is allowed to alter the dialog.
     * 
     * @see Adapto_Ui_Dialog::getCall
     *
     * @param mixed $object modifier object
     */

    public function setModifierObject($object)
    {
        $this->m_modifierObject = $object;
    }

    /**
     * Load JavaScript and stylesheets.
     */
    function load()
    {
        self::loadScriptsAndStyles($this->m_themeLoad ? $this->m_themeName : false);
    }

    /**
     * Load JavaScript and stylesheets.
     *
     * @param string|boolean $theme uses the given window theme, by default checks the current ATK theme
     *                              which window theme should be used, if set explicitly to false no
     *                              theme will be loaded 
     */

    public static function loadScriptsAndStyles($theme = null)
    {
        if ($theme === null && atkTheme::getInstance()->getAttribute('dialog_theme_load', true)) {
            $theme = atkTheme::getInstance()->getAttribute('dialog_theme_name', 'alphacube');
        }

        $page = &atkPage::getInstance();
        $page->register_script(Adapto_Config::getGlobal('atkroot') . 'atk/javascript/prototype-ui/window/window.packed.js');
        $page->register_script(Adapto_Config::getGlobal('atkroot') . 'atk/javascript/prototype-ui-ext.js');
        $page->register_script(Adapto_Config::getGlobal('atkroot') . 'atk/javascript/class.atkdialog.js');
        $page->register_style(Adapto_Config::getGlobal('atkroot') . 'atk/javascript/prototype-ui/window/themes/window/window.css');
        $page->register_style(Adapto_Config::getGlobal('atkroot') . 'atk/javascript/prototype-ui/window/themes/shadow/mac_shadow.css');

        if ($theme) {
            $page->register_style(Adapto_Config::getGlobal('atkroot') . 'atk/javascript/prototype-ui/window/themes/window/' . $theme . '.css');
        }
    }

    /**
     * Returns the dialog URL.
     *
     * @return string dialog URL
     * @access private
     */
    function getUrl()
    {
        return partial_url($this->getEntityType(), $this->m_action, $this->m_partial, $this->m_params, $this->m_sessionStatus);
    }

    /**
     * Returns the dialog options.
     *
     * @return array dialog options
     * @access private
     */
    function getOptions()
    {
        $options = array();

        if ($this->m_width !== null)
            $options['width'] = $this->m_width;
        if ($this->m_height !== null)
            $options['height'] = $this->m_height;
        if ($this->m_serializeForm != null)
            $options['serializeForm'] = $this->m_serializeForm;

        return $options;
    }

    /**
     * Window options like the show effect etc. These options can be controlled
     * by setting the special theme attribute 'dialog_window_options'. This
     * attribute should contain a JavaScript object with the window options.
     *
     * @return unknown
     */

    protected function getWindowOptions()
    {
        if (atkTheme::getInstance()->getAttribute('dialog_window_options', null) != null)
            return atkTheme::getInstance()->getAttribute('dialog_window_options');
        else
            return '{}';
    }

    /**
     * Returns the JavaScript call to open the dialog.
     *
     * @param boolean $load         load JavaScript and stylesheets needed to show this dialog?
     * @param boolean $encode       encode using htmlentities (needed to use in links etc.)
     * @param boolean $callModifier call entity's dialog modifier (modifyDialog method)?
     * @param boolean $lateParamBinding 
     *
     * @return string JavaScript call for opening the dialog
     */
    function getCall($load = true, $encode = true, $callModifier = true, $lateParamBinding = false)
    {
        if ($load) {
            $this->load();
        }

        if ($callModifier) {
            $method = 'modifyDialog';
            if ($this->getModifierObject() != null && method_exists($this->getModifierObject(), $method)) {
                $this->getModifierObject()->$method($this);
            }
        }

        $call = "(new Adapto_.Dialog(%s, %s, " . ($lateParamBinding ? 'params' : '{}') . ", %s, %s, %s)).show();";
        $params = array(atkJSON::encode($this->m_title), atkJSON::encode($this->getUrl()), atkJSON::encode($this->m_themeName),
                count($this->getOptions()) == 0 ? '{}' : atkJSON::encode($this->getOptions()), $this->getWindowOptions());

        $result = vsprintf($call, $params);
        $result = $encode ? Adapto_htmlentities($result) : $result;
        $result = $lateParamBinding ? "function(params) { $result }" : $result;

        return $result;
    }

    /**
     * Returns JavaScript code to save the contents of the current
     * active ATK dialog.
     *
     * @param string $url        save URL
     * @param string $formName   form name (will be serialized)
     * @param array $extraParams key/value array with URL parameters that need to be send
     *                           the parameters will override form element with the same name!
     * @param boolean $encode    encode using htmlentities (needed to use in links etc.)
     *
     * @return string JavaScript call for saving the current dialog
     */

    public static function getSaveCall($url, $formName = 'dialogform', $extraParams = array(), $encode = true)
    {

        $call = 'ATK.Dialog.getCurrent().save(%s, %s, %s);';
        $params = array(atkJSON::encode($url), atkJSON::encode($formName), count($extraParams) == 0 ? '{}' : atkJSON::encode($extraParams));

        $result = vsprintf($call, $params);
        return $encode ? Adapto_htmlentities($result) : $result;
    }

    /**
     * Returns JavaScript code to close the current ATK dialog.
     *
     * @return string JavaScript call for closing the current dialog
     */

    public static function getCloseCall()
    {
        return 'ATK.Dialog.getCurrent().close();';
    }

    /**
     * Returns JavaScript code to save the contents of the current
     * active ATK dialog and close it immediately.
     *
     * @param string $url        save URL
     * @param string $formName   form name (will be serialized)
     * @param array $extraParams key/value array with URL parameters that need to be send
     *                           the parameters will override form element with the same name!
     * @param boolean $encode    encode using htmlentities (needed to use in links etc.)
     *
     * @return string JavaScript call for saving the current dialog and closing it immediately
     */

    public static function getSaveAndCloseCall($url, $formName = 'dialogform', $extraParams = array(), $encode = true)
    {

        $call = 'ATK.Dialog.getCurrent().saveAndClose(%s, %s, %s);';
        $params = array(atkJSON::encode($url), atkJSON::encode($formName), count($extraParams) == 0 ? '{}' : atkJSON::encode($extraParams));

        $result = vsprintf($call, $params);
        return $encode ? Adapto_htmlentities($result) : $result;
    }

    /**
     * Returns JavaScript code to update the contents of the current modal dialog.
     *
     * @param string $content new dialog contents
     * @param boolean $encode encode using htmlentities (needed to use in links etc.)
     *
     * @return string JavaScript call for updating the dialog contents
     */

    public static function getUpdateCall($content, $encode = true)
    {

        $call = 'ATK.Dialog.getCurrent().update(%s);';
        $params = array(atkJSON::encode($content));

        $result = vsprintf($call, $params);
        return $encode ? Adapto_htmlentities($result) : $result;
    }

    /**
     * Returns JavaScript code to update the contents of the current modal dialog
     * using an Ajax request to the given URL.
     *
     * @param string $url url for the Ajax request
     *
     * @return string JavaScript call for updating the dialog contents
     */

    public static function getAjaxUpdateCall($url)
    {
        $call = "ATK.Dialog.getCurrent().ajaxUpdate('%s');";
        $result = sprintf($call, addslashes($url));
        return $result;
    }

    /**
     * Returns JavaScript code to reload the contents of the current modal dialog.
     *
     * @return string JavaScript call for reloading the dialog contents
     */

    public static function getReloadCall()
    {
        return "ATK.Dialog.getCurrent().reload();";
    }
}
