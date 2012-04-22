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
 * @copyright (c) 2010 petercv
 * @license http://www.achievo.org/atk/licensing ATK Open Source License
 *

 */

/**
 * Backwards-compatiblity selector for entitys which have selectDb and/or countDb
 * overrides. Makes sure we use the overrides. Due to this we can't support bind
 * parameters neither can we support row-by-row fetching.
 *
 * @author petercv
 * @package adapto
 * @subpackage utils
 */
class Adapto_Util_CompatSelector extends Adapto_Selector
{
    /**
     * Build selector based on the set conditions.
     * 
     * @return string selector
     */

    protected function _buildSelector()
    {
        $selector = '';

        if (count($this->m_conditions) == 1) {
            $selector = $this->m_conditions[0]['condition']; // can't support binds
        } else if (count($this->m_conditions) > 1) {
            $conditions = array(); // can't support binds
            foreach ($this->m_conditions as $condition) {
                $conditions[] = $condition['condition'];
            }

            $selector = '(' . implode(') AND (', $conditions) . ')';
        }

        return $selector;
    }

    /**
     * Return all rows.
     *
     * @return array all rows
     */

    public function getAllRows()
    {
        if ($this->m_rows === null) {
            atkwarning("Using deprecated selectDb override for entity " . $this->_getEntity()->atkEntityType());
            $selector = $this->_buildSelector();
            $this->m_rows = $this->m_entity
                    ->selectDb($selector, $this->m_order, array('limit' => $this->m_limit, 'offset' => $this->m_offset), $this->m_excludes, $this->m_includes,
                            $this->m_mode, $this->m_distinct, $this->m_ignoreDefaultFilters);
        }

        return $this->m_rows;
    }

    /**
     * Return row count.
     *
     * @return int row count
     */

    public function getRowCount()
    {
        if ($this->m_rowCount === null) {
            atkwarning("Using deprecated countDb override for entity " . $this->_getEntity()->atkEntityType());
            $selector = $this->_buildSelector();
            $this->m_rowCount = (int) $this->m_entity
                    ->countDb($selector, $this->m_excludes, $this->m_includes, $this->m_mode, $this->m_distinct, $this->m_ignoreDefaultFilters);
        }

        return $this->m_rowCount;
    }

    /**
     * Returns an iterator for this selector.
     */

    public function getIterator()
    {
        $rows = $this->getAllRows();
        return new ArrayIterator($rows);
    }
}
