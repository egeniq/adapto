<?php
/**
 * This file is part of the Adapto Toolkit.
 * Detailed copyright and licensing information can be found
 * in the doc/COPYRIGHT and doc/LICENSE files which should be
 * included in the distribution.
 *
 * @package adapto
 * @subpackage utils
 *
 * @copyright (c) 2000-2007 Ibuildings.nl BV
 *
 * @license http://www.achievo.org/atk/licensing ATK Open Source License
 */

/**
 * The data grid component base class. All data grid component extend this
 * class and implement the render method.
 *
 * @author petercv
 * @package adapto
 * @subpackage datagrid
 */
abstract class Adapto_Datagrid_Component
{
    /**
     * The atkDataGrid.
     *
     * @var atkDataGrid
     */
    private $m_grid;

    /**
     * The component options.
     *
     * @var array
     */
    private $m_options;

    /**
     * Constructor.
     *
     * @param atkDataGrid $grid    grid
     * @param array       $options component options
     */

    public function __construct($grid, $options = array())
    {
        $this->m_grid = $grid;
        $this->m_options = $options;
    }

    /**
     * Destroy.
     */

    public function destroy()
    {
        $this->m_grid = null;
    }

    /**
     * Returns the value for the component option with the given name.
     *
     * @param string $name option name
     *
     * @return mixed option value
     */

    protected function getOption($name, $fallback = null)
    {
        return isset($this->m_options[$name]) ? $this->m_options[$name] : $fallback;
    }

    /**
     * Returns the data grid.
     *
     * @return atkDataGrid data grid
     */

    protected function getGrid()
    {
        return $this->m_grid;
    }

    /**
     * Returns the data grid entity.
     *
     * @return atkEntity entity
     */

    protected function getEntity()
    {
        return $this->getGrid()->getEntity();
    }

    /**
     * Returns the page object.
     *
     * @return atkPage page
     */

    protected function getPage()
    {
        return $this->getEntity()->getPage();
    }

    /**
     * Returns the UI object.
     *
     * @return atkUi ui
     */

    protected function getUi()
    {
        return $this->getEntity()->getUi();
    }

    /**
     * Return the theme object.
     *
     * @return atkTheme theme
     */

    protected function getTheme()
    {
        return Adapto_ClassLoader::getInstance("Adapto_Ui_Theme");
    }

    /**
     * Translate the given string using the grid entity.
     *
     * The value of $fallback will be returned if no translation can be found.
     * If you want NULL to be returned when no translation can be found then
     * leave the fallback empty and set $useDefault to false.
     *
     * @param string  $string      string to translate
     * @param string  $fallback    fallback in-case no translation can be found
     * @param boolean $useDefault use default ATK translation if no translation can be found?
     */

    protected function text($string, $fallback = '', $useDefault = true)
    {
        return $this->getGrid()->text($string, $fallback, $useDefault);
    }

    /**
     * Renders the component.
     *
     * @return string component HTML
     */

    public abstract function render();
}
