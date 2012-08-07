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
 * Special selector for multi-language entitys. Due to the way multi-language
 * entitys work we need to fetch all rows at once, might need to set some
 * extra conditions etc.
 *
 * @todo I don't understand the multi-language code for one bit and I don't have
 *       a setup where I can test this. So can anyone try this code and probably
 *       fix it?
 *
 * @author petercv
 * @package adapto
 * @subpackage utils
 */
class Adapto_Util_MLSelector extends Adapto_Selector
{
    /**
     * Apply multi-language conditions.
     *
     * @param atkQuery $query query object
     */

    protected function _applyMultiLanguageConditionsToQuery(atkQuery $query)
    {
        $mlSplitter = Adapto_ClassLoader::getInstance("atk.utils.atkmlsplitter");
        $mlSplitter->addMlCondition($query, $this->_getEntity(), $this->m_mode, $this->_getEntity()->getTable());
    }

    /**
     * Override build query so we can add the multi language conditions.
     *
     * @return atkQuery query object
     */

    protected function _buildQuery(array $attrsByLoadType)
    {
        $query = parent::_buildQuery($attrsByLoadType);
        $this->_applyMultiLanguageConditionsToQuery($query);
        return $query;
    }

    /**
     * Transform raw database rows to entity compatible rows.
     *
     * @param array    $rows            raw database rows
     * @param atkQuery $query           query object
     * @param array    $attrsByLoadType attributes by load type
     *
     * @return array entity compatible rows
     */

    protected function _transformRows($rows, atkQuery $query, array $attrsByLoadType)
    {
        // When copying there could be more than one multi-language record.
        // So we split this before sending it to the multi-language splitter.
        // This only happens in case of an atkOneToManyRelation.
        if ($this->m_mode == 'copy') {
            $mlSplitter = Adapto_ClassLoader::getInstance("atk.utils.atkmlsplitter");

            $result = array();
            $ids = array();
            $indexes = array();

            foreach ($rows as $row) {
                if (!in_array($row[$this->_getEntity()->primaryKeyField()], $ids)) {
                    $key = count($result);
                    $result[$key][] = $row;
                    $ids[] = $row[$this->_getEntity()->primaryKeyField()];
                    $indexes[] = $key;
                } else {
                    $index = array_search($row[$this->_getEntity()->primaryKeyField()], $ids);
                    $result[$indexes[$index]][] = $row;
                }
            }

            // combine the multi-language records and put them in the rows array.
            $rows = array();
            foreach ($result as $entry) {
                $mlSplitter->combineMlRecordSet($this->_getEntity(), $entry, $query);
                $rows[] = $entry[0];
            }
        }
        // Combine multi-language records on edit.
 else if ($this->m_mode == 'edit') {
            $mlSplitter = Adapto_ClassLoader::getInstance("atk.utils.atkmlsplitter");
            $mlSplitter->combineMlRecordSet($this->_getEntity(), $rows, $query);
        }

        return parent::_transformRows($rows, $query, $attrsByLoadType);
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
