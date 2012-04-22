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
 * The data grid index. Can be used to render an alphanumeric index
 * for an ATK data grid.
 *
 * @author petercv
 * @package adapto
 * @subpackage datagrid
 */
class Adapto_Datagrid_Index extends Adapto_DGComponent
{
    /**
     * Returns the available indices.
     * 
     * @return array available indices
     */

    protected function getAvailableIndices()
    {
        return $this->getEntity()->select()->mode($this->getGrid()->getMode())->getIndices();
    }

    /**
     * Returns an array with index links.
     */

    protected function getLinks()
    {
        $grid = $this->getGrid();
        $links = array();

        $chars = $this->getAvailableIndices();
        $current = $grid->getIndex();

        // indices
        foreach ($chars as $char) {
            $title = $char;
            $call = $grid->getUpdateCall(array('atkstartat' => 0, 'atkindex' => "{$char}*"));
            $links[] = array('type' => 'index', 'title' => $title, 'call' => $call, 'current' => "{$char}*" == $current);
        }

        // view all
        if (!empty($current)) {
            $title = $grid->text('view_all');
            $call = $grid->getUpdateCall(array('atkindex' => ''));
            $links[] = array('type' => 'all', 'call' => $call, 'title' => $title);
        }

        return $links;
    }

    /**
     * Renders the index for the given data grid.
     *
     * @return string rendered HTML
     */

    public function render()
    {
        if ($this->getGrid()->isEditing()) {
            return '';
        }

        $links = $this->getLinks();
        $result = $this->getUi()->render('dgindex.tpl', array('links' => $links));
        return $result;
    }
}
