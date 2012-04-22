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
 * The data grid summary. Can be used to render a 
 * summary for an ATK data grid.
 *
 * @author petercv
 * @package adapto
 * @subpackage datagrid
 */
class Adapto_Datagrid_Summary extends Adapto_DGComponent
{
    /**
     * Renders the summary for the given data grid.
     *
     * @return string rendered HTML
     */

    public function render()
    {
        $grid = $this->getGrid();

        $limit = $grid->getLimit();
        $count = $grid->getCount();

        if ($count == 0) {
            return null;
        }

        if ($limit == -1) {
            $limit = $count;
        }

        $start = $grid->getOffset();
        $end = min($start + $limit, $count);
        $page = floor(($start / $limit) + 1);
        $pages = ceil($count / $limit);

        $string = $grid->text('datagrid_summary');

        $params = array('start' => $start + 1, 'end' => $end, 'count' => $count, 'limit' => $limit, 'page' => $page, 'pages' => $pages);

        $parser = new Adapto_StringParser($string);
        $result = $parser->parse($params);

        return $result;
    }
}
?>