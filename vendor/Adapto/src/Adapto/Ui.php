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

namespace Adapto;

use Zend\View\Model\ViewModel;
use Zend\View\Renderer\PhpRenderer;
use Zend\View\Resolver;

/**
 * Utility class for rendering boxes, lists, tabs or other templates.
 *
 * @author ijansch
 * @package adapto
 * @subpackage ui
 */
class Ui
{
    /**
     * atkTheme instance, initialised by constructor
     * @access private
     * @var atkTheme
     */
    public $m_theme = NULL; // defaulted to public

    /**
     * atkUi constructor, initialises Smarty and atkTheme instance
     */
    public function __construct()
    {
        $this->m_theme = ClassLoader::getInstance("Adapto\Ui\Theme");
    }

    /**
     * get a singleton instance of the atkUi class.
     *
     * @return Adapto_Ui
     */
    static public function &getInstance()
    {
        static $s_instance = NULL;

        if ($s_instance==NULL)
        {
            Util\Debugger::debug("Creating a new Adapto_Ui instance");
            $s_instance = new Ui();
        }

        return $s_instance;
    }

    /**
     * Renders action templates
     * Currently only the view action is implemented
     * @param String $action the action for which to render the template
     * @param array  $vars   the template variables
     * @param string $module the name of the module requesting to render a template
     * @return String the rendered template
     */
    function renderAction($action, $vars, $module="")
    {
        // todo.. action specific templates
        $tpl = "action_$action.tpl";
        if ($this->m_theme->tplPath($tpl)=="") // no specific theme for this action
        {

            $tpl = "action.phtml";
        }
        return $this->render($tpl, $vars, $module);
    }

    /**
     * Renders a list template
     * @param String $action not used (deprecated?)
     * @param array  $vars   the variables with which to parse the list template
     * @param string $module the name of the module requesting to render a template
     */
    function renderList($action, $vars, $module="")
    {
        return $this->render("list.phtml", $vars, $module);
    }

    /**
     * Renders a box with Smarty template.
     * Call with a $name variable to provide a
     * better default than "box.phtml".
     *
     * For instance, calling renderBox($smartyvars, "menu")
     * will make it search for a menu.phtml first and use that
     * if it's available, otherwise it will just use box.tpl
     *
     * @param array $vars the variables for the template
     * @param string $name The name of the template
     * @param string $module the name of the module requesting to render a template
     */
    function renderBox($vars, $name="", $module="")
    {
        if ($name && file_exists($this->m_theme->tplPath($name.".phtml")))
        {
            return $this->render($name.".phtml", $vars);
        }
        return $this->render("box.phtml", $vars, $module);
    }

    /**
     * Renders the insides of a dialog.
     *
     * @param array $vars template variables
     * @param string $module the name of the module requesting to render a template
     * @return string rendered dialog
     */
    function renderDialog($vars, $module="")
    {
        return $this->render("dialog.phtml", $vars, $module);
    }

    /**
     * Renders a tabulated template
     * Registers some scriptcode too when the tabtype is set to dhtml
     * @param array $vars the variables with which to render the template
     * @param string $module the name of the module requesting to render a template
     * @return String the rendered template
     */
    function renderTabs($vars, $module="")
    {
        if ($this->m_theme->getAttribute("tabtype")=="dhtml")
        {
            $page = &atkPage::getInstance();
            $page->register_script(Adapto_Config::getGlobal("atkroot")."atk/javascript/tools.js");
        }
        return $this->render("tabs.phtml", $vars, $module);
    }

    /**
     * Renders the given template.
     *
     * If the name ends with ".php" PHP will be used to render the template. 
     * 
     * @param String $name   the name of the template to render
     * @param array  $vars   the variables with which to render the template
     * @param String $module the name of the module requesting to render a template
     *
     * @return String rendered template
     */
    public function render($name, $vars=array(), $module="")
    {
        $renderer = new PhpRenderer();
        
        $map = new Resolver\TemplateMapResolver(array(
                $name => $this->templatePath($name, $module),
        ));
        
        $resolver = new Resolver\TemplateMapResolver($map);
        $renderer->setResolver($resolver);
        
        $model = new ViewModel();
        $model->setVariables($vars);
        $model->setTemplate($name);
        
        return $renderer->render($model);
        
 //       $view->addHelperPath('Adapto/Ui/View/Helper', 'Adapto_Ui_View_Helper_'); //Change as per your path and class
           
    }

    /**
     * This function returns a complete themed path for a given template.
     * This is a convenience method, which calls the tplPath method on
     * the theme instance. However, if the template name contains a '/',
     * we assume the full template path is already given and we simply
     * return it.
     *
     * @param String $template  The filename (without path) of the template
     *                          for which you want to complete the path.
     * @param String $module    The name of the module requesting to render a template
     * @return String the template path
     */
    function templatePath($template, $module="")
    {
        if (strpos($template, "/")===false)
        {
            // lookup template in theme.
            $template = $this->m_theme->tplPath($template, $module);
        }

        return $template;
    }

    /**
     * This function returns a complete themed path for a given stylesheet.
     * This is a convenience method, which calls the stylePath method on
     * the theme instance.
     *
     * @param String $style The filename (without path) of the stylesheet for
     *                      which you want to complete the path.
     * @param String $module  the name of the module requesting the style path
     * @return String the path of the style
     */
    function stylePath($style, $module="")
    {
        return $this->m_theme->stylePath($style, $module);
    }

    /**
     * Return the title to render
     *
     * @param String $module   the module in which to look
     * @param String $entitytype the entitytype of the action
     * @param String $action   the action that we are trying to find a title for
     * @param bool   $actiononly wether or not to return a name of the entity
     *                          if we couldn't find a specific title
     * @return String the title for the action
     */
    function title($module, $entitytype, $action=null, $actiononly=false)
    {
        if ($module == NULL || $entitytype == NULL) return "";
        return $this->entityTitle(atkGetEntity($module.'.'.$entitytype), $action, $actiononly);
    }

    /**
     * This function returns a suitable title text for an action.
     * Example: echo $ui->title("users", "employee", "edit"); might return:
     *          'Edit an existing employee'
     * @param atkEntity $entity the entity to get the title from
     * @param String $action   the action that we are trying to find a title for
     * @param bool   $actiononly wether or not to return a name of the entity
     *                          if we couldn't find a specific title
     * @return String the title for the action
     */
    function entityTitle($entity, $action=NULL, $actiononly=false)
    {
        if ($entity == NULL) return "";

        $entitytype = $entity->m_type;
        $module = $entity->m_module;

        if ($action != NULL)
        {
            $keys = array('title_'.$module.'_'.$entitytype.'_'.$action,
                      'title_'.$entitytype.'_'.$action,
                      'title_'.$action);

            $label = $entity->text($keys, NULL, "", "", true);
        }
        else
        {
            $label = "";
        }

        if ($label=="")
        {
            $actionKeys = array(
          'action_'.$module.'_'.$entitytype.'_'.$action,
          'action_'.$entitytype.'_'.$action,
          'action_'.$action,
            $action
            );

            if ($actiononly)
            {
                return $entity->text($actionKeys);
            }
            else
            {
                $keys = array('title_'.$module.'_'.$entitytype, 'title_'.$entitytype, $entitytype);
                $label = $entity->text($keys);
                if ($action != NULL)
                $label .= " - ".$entity->text($actionKeys);
            }
        }
        return $label;
    }

}
?>
