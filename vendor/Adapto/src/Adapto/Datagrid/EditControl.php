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
 * The data grid no records found message. Can be used to render a 
 * simple message underneath the grid stating there are no records 
 * found in the database.
 *
 * @author petercv
 * @package adapto
 * @subpackage datagrid
 */
class Adapto_Datagrid_EditControl extends Adapto_DGComponent
{
    /**
     * Renders the no records found message for the given data grid.
     *
     * @param atkDataGrid $grid the data grid
     * @return string rendered HTML
     */

    public function render()
    {
        if (count($this->getGrid()->getRecords()) == 0 || count($this->getEntity()->m_editableListAttributes) == 0) {
            return null;
        }

        if ($this->getGrid()->getPostvar('atkgridedit', false)) {
            $call = $this->getGrid()->getUpdateCall(array('atkgridedit' => 0));
            return '<a href="javascript:void(0)" onclick="' . htmlentities($call) . '">' . $this->getGrid()->text('cancel_edit') . '</a>';
        } else {
            $call = $this->getGrid()->getUpdateCall(array('atkgridedit' => 1));
            return '<a href="javascript:void(0)" onclick="' . htmlentities($call) . '">' . $this->getGrid()->text('edit') . '</a>';
        }
    }
}
