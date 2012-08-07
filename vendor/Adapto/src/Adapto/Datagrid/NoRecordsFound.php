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
class Adapto_Datagrid_NoRecordsFound extends Adapto_DGComponent
{
    /**
     * Renders the no records found message for the given data grid.
     *
     * @return string rendered HTML
     */

    public function render()
    {
        $grid = $this->getGrid();

        $usesIndex = $grid->getIndex() != null;
        $isSearching = is_array($grid->getPostvar('atksearch')) && count($grid->getPostvar('atksearch')) > 0;

        if ($grid->getCount() == 0 && ($usesIndex || $isSearching)) {
            return $grid->text('datagrid_norecordsfound_search');
        } else if ($grid->getCount() == 0) {
            return $grid->text('datagrid_norecordsfound_general');
        } else {
            return null;
        }
    }
}
?>