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
 * The data grid paginator. Can be used to render pagination 
 * links for an ATK data grid.
 *
 * @author petercv
 * @package adapto
 * @subpackage datagrid
 */
class Adapto_Datagrid_Paginator extends Adapto_DGComponent
{
    /**
     * Maximum number of pagination links (excluding
     * the previous and next links).
     *
     * @var int
     */
    protected $m_maxLinks = 10;

    /**
     * Constructor.
     *
     * @param atkDataGrid $grid    grid
     * @param array       $options component options
     */

    public function __construct($grid, $options = array())
    {
        parent::__construct($grid, $options);
        $this->m_maxLinks = Adapto_Config::getGlobal('pagelinks', 10);
    }

    /**
     * Returns an array with pagination links.
     */

    protected function getLinks()
    {
        $grid = $this->getGrid();
        $links = array();

        $count = $grid->getCount();
        $limit = $grid->getLimit();
        $offset = $grid->getOffset();

        // no navigation links needed
        if ($limit == 0 || $count <= $limit) {
            return $links;
        }

        // calculate pages, first and last page
        $pages = ceil($count / $limit);
        $current = floor($offset / $limit) + 1;
        $first = $current - floor(($this->m_maxLinks - 1) / 2);
        $last = $current + ceil(($this->m_maxLinks - 1) / 2);

        // fix invalid first page
        if ($first < 1) {
            $first = 1;
            $last = min($pages, $this->m_maxLinks);
        }

        // fix invalid last page
        if ($last > $pages) {
            $first = max(1, $pages - $this->m_maxLinks + 1);
            $last = $pages;
        }

        // previous link
        if ($current > 1) {
            $title = $grid->text('previous');
            $url = $grid->getUpdateCall(array('atkstartat' => $offset - $limit));
            $links[] = array('type' => 'previous', 'call' => $url, 'title' => $title);
        }

        // normal pagination links
        for ($i = $first; $i <= $last; $i++) {
            if ($i == $current) {
                $title = $i;
                $links[] = array('type' => 'page', 'title' => $i, 'current' => true);
            } else {
                $title = $i;
                $url = $grid->getUpdateCall(array('atkstartat' => max(0, ($i - 1) * $limit)));
                $links[] = array('type' => 'page', 'call' => $url, 'title' => $title, 'current' => false);
            }
        }

        // next link
        if ($current < $pages) {
            $title = $grid->text('next');
            $url = $grid->getUpdateCall(array('atkstartat' => $offset + $limit));
            $links[] = array('type' => 'next', 'call' => $url, 'title' => $title);
        }

        return $links;
    }

    /**
     * Renders the paginator for the given data grid.
     *
     * @return string rendered HTML
     */

    public function render()
    {
        if ($this->getGrid()->isEditing()) {
            return '';
        }

        $links = $this->getLinks();
        $result = $this->getUi()->render('dgpaginator.tpl', array('links' => $links));
        return $result;
    }
}
