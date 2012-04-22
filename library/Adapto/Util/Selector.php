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
 * Fluent interface helper class for retrieving records from an entity.
 *
 * @author petercv
 * @package adapto
 * @subpackage utils
 */
class Adapto_Util_Selector implements ArrayAccess, Countable, IteratorAggregate
{
    /**
     * This selector's entity.
     *
     * @var atkEntity
     */
    protected $m_entity;

    /**
     * Selector parameters.
     */
    protected $m_conditions = array();
    protected $m_distinct = false;
    protected $m_mode = '';
    protected $m_order = '';
    protected $m_limit = -1;
    protected $m_offset = 0;
    protected $m_excludes = null;
    protected $m_includes = null;
    protected $m_ignoreDefaultFilters = false;
    protected $m_ignorePostvars = false;
    protected $m_ignoreForceLoad = false;
    protected $m_ignorePrimaryKey = false;

    /**
     * Rows cache.
     *
     * @var array
     */
    protected $m_rows = null;

    /**
     * Row count cache.
     *
     * @var int
     */
    protected $m_rowCount = null;

    /**
     * Indices cache.
     *
     * @var array
     */
    protected $m_indices = null;

    /**
     * Current iterator instance (if iterator is used).
     * 
     * @var atkSelectorIterator
     */
    private $m_iterator = null;

    /**
     * Current statement object (if iterator is used).
     *
     * @var atkStatement
     */
    private $m_stmt = null;

    /**
     * Current query object (if iterator is used).
     * 
     * @var atkQuery 
     */
    private $m_query = null;

    /**
     * Current attributes by load type (if iterator is used).
     * 
     * @var array
     */
    private $m_attrsByLoadType = null;

    /**
     * Constructor.
     *
     * @param atkEntity $entity this selector's entity
     */

    public function __construct($entity)
    {
        $this->m_entity = $entity;
    }

    /**
     * Returns the entity for this selector.
     *
     * @return atkEntity
     */

    protected function _getEntity()
    {
        return $this->m_entity;
    }

    /**
     * Returns the entity's database.
     *
     * @return atkDb
     */

    protected function _getDb()
    {
        return $this->_getEntity()->getDb();
    }

    /**
     * Adds a condition..
     *
     * @param string $condition where clause
     * @param array  $params    bind parameters
     *
     * @return Adapto_Util_Selector
     */

    public function where($condition, $params = array())
    {
        if (strlen(trim($condition)) > 0) {
            $this->m_conditions[] = array('condition' => $condition, 'params' => $params);
        }

        return $this;
    }

    /**
     * Ignore default entity filters.
     *
     * @param boolean $ignore ignore default entity filters?
     * @return Adapto_Util_Selector
     */

    public function ignoreDefaultFilters($ignore = true)
    {
        $this->m_ignoreDefaultFilters = $ignore;
        return $this;
    }

    /**
     * Ignore criteria set in the postvars, like search criteria etc.
     *
     * @param boolean $ignore ignore postvars?
     * @return Adapto_Util_Selector
     */

    public function ignorePostvars($ignore = true)
    {
        $this->m_ignorePostvars = $ignore;
        return $this;
    }

    /**
     * Ignore force load flags.
     *
     * @param boolean $ignore ignore force load flags
     * @return Adapto_Util_Selector
     */

    public function ignoreForceLoad($ignore = true)
    {
        $this->m_ignoreForceLoad = $ignore;
        return $this;
    }

    /**
     * Don't forcefully load the primary key. The result records also won't
     * contain the special "atkprimkey" entry.
     *
     * @param boolean $ignore ignore primary key
     * @return Adapto_Util_Selector
     */

    public function ignorePrimaryKey($ignore = true)
    {
        $this->m_ignorePrimaryKey = $ignore;
        return $this;
    }

    /**
     * Distinct selection?
     *
     * @param boolean $distinct distinct selection?
     * @return Adapto_Util_Selector
     */

    public function distinct($distinct)
    {
        $this->m_distinct = $distinct;
        return $this;
    }

    /**
     * Set the select mode.
     *
     * @param string $mode select mode
     * @return Adapto_Util_Selector
     */

    public function mode($mode)
    {
        $this->m_mode = $mode;
        return $this;
    }

    /**
     * Order by the given order by string.
     *
     * @param string $order order by string
     * @return Adapto_Util_Selector
     */

    public function orderBy($order)
    {
        $this->m_order = $order;
        return $this;
    }

    /**
     * Limit the results bij the given limit (and from the optional offset).
     *
     * @param int $limit  limit
     * @param int $offset offset
     * @return Adapto_Util_Selector
     */

    public function limit($limit, $offset = 0)
    {
        $this->m_limit = $limit;
        $this->m_offset = $offset;
        return $this;
    }

    /**
     * Include only the following list of attributes.
     *
     * @param array $includes list of includes
     * @return Adapto_Util_Selector
     */

    public function includes($includes)
    {
        if ($includes == null) {
            $includes = null;
        } else if (!is_array($includes)) {
            $includes = func_get_args();
        }

        $this->m_includes = $includes;
        return $this;
    }

    /**
     * Exclude the following list of attributes.
     *
     * @param array $excludes list of excludes
     * @return Adapto_Util_Selector
     */

    public function excludes($excludes)
    {
        if ($excludes == null) {
            $excludes = null;
        } else if (!is_array($excludes)) {
            $excludes = func_get_args();
        }

        $this->m_excludes = $excludes;
        return $this;
    }

    /**
     * Are we searching?
     */

    protected function _isSearching()
    {
        if ($this->m_ignorePostvars) {
            return false;
        }

        $searchCriteria = atkArrayNvl($this->_getEntity()->m_postvars, 'atksearch');
        $smartSearchCriteria = atkArrayNvl($this->_getEntity()->m_postvars, 'atksmartsearch');
        $indexValue = $this->_getEntity()->m_index != '' ? atkArrayNvl($this->_getEntity()->m_postvars, 'atkindex', '') : '';

        return (is_array($searchCriteria) && count($searchCriteria) > 0) || (is_array($smartSearchCriteria) && count($smartSearchCriteria) > 0)
                || !empty($indexValue);
    }

    /**
     * Apply set conditions to query.
     *
     * @param atkQuery $query query object
     */

    protected function _applyConditionsToQuery($query)
    {
        foreach ($this->m_conditions as $condition) {
            $query->addCondition($condition['condition']);
        }
    }

    /**
     * Apply posted filter to query.
     *
     * @param atkQuery $query query object
     */

    protected function _applyPostedFilterToQuery($query)
    {
        $filter = atkArrayNvl($this->_getEntity()->m_postvars, 'atkfilter', '');
        if (empty($filter)) {
            return;
        }

        $filter = $this->_getEntity()->validateFilter($filter);
        $query->addCondition($filter);
    }

    /**
     * Apply posted index value to query.
     *
     * @param atkQuery $query query object
     */

    protected function _applyPostedIndexValueToQuery(atkQuery $query)
    {
        $indexAttrName = $this->_getEntity()->m_index;
        $indexValue = atkArrayNvl($this->_getEntity()->m_postvars, 'atkindex', '');
        if (empty($indexAttrName) || empty($indexValue) || !is_object($this->_getEntity()->getAttribute($indexAttrName))) {
            return;
        }

        $attr = $this->_getEntity()->getAttribute($indexAttrName);
        $attr->searchCondition($query, $this->_getEntity()->getTable(), $indexValue, 'wildcard', '');
    }

    /**
     * Set search method for query.
     *
     * @param atkQuery $query query object
     */

    protected function _applyPostedSearchMethodToQuery(atkQuery $query)
    {
        if (isset($this->m_postvars['atksearchmethod'])) {
            $query->setSearchMethod($this->m_postvars['atksearchmethod']);
        }
    }

    /**
     * Apply posted (normal) search criteria to query
     *
     * @param atkQuery $query           query object
     * @param array    $attrsByLoadType attributes by load type
     */

    protected function _applyPostedSearchCriteriaToQuery(atkQuery $query, array $attrsByLoadType)
    {
        $searchCriteria = atkArrayNvl($this->_getEntity()->m_postvars, 'atksearch');
        if (!is_array($searchCriteria) || count($searchCriteria) == 0) {
            return;
        }

        foreach ($searchCriteria as $key => $value) {
            if ($value === null || $value === ''
                    || ($this->m_mode != 'admin' && $this->m_mode != 'export' && !array_key_exists($key, $attrsByLoadType[ADDTOQUERY]))) {
                continue;
            }

            $attr = $this->_getEntity()->getAttribute($key);
            if (is_object($attr)) {
                if (is_array($value) && isset($value[$key]) && count($value) == 1) {
                    $value = $value[$key];
                }

                $searchMode = $this->_getEntity()->getSearchMode();
                if (is_array($searchMode)) {
                    $searchMode = $searchMode[$key];
                }

                if ($searchMode == null) {
                    $searchMode = Adapto_Config::getGlobal('search_defaultmode');
                }

                $attr->searchCondition($query, $this->_getEntity()->getTable(), $value, $searchMode, '');
            } else {
                atkdebug("Using default search method for $key");
                $condition = "LOWER(" . $this->_getEntity()->getTable() . "." . $key . ") LIKE LOWER('%" . $this->_getDb()->escapeSQL($value, true) . "%')";
                $query->addSearchCondition($condition);
            }
        }
    }

    /**
     * Apply posted smart search criteria to query.
     *
     * @param atkQuery $query query object
     */

    protected function _applyPostedSmartSearchCriteriaToQuery(atkQuery $query)
    {
        $searchCriteria = atkArrayNvl($this->_getEntity()->m_postvars, 'atksmartsearch');
        if (!is_array($searchCriteria) || count($searchCriteria) == 0) {
            return;
        }

        foreach ($searchCriteria as $id => $criterium) {
            $path = $criterium['attrs'];
            $value = $criterium['value'];
            $mode = $criterium['mode'];

            $attrName = array_shift($path);
            $attr = $this->_getEntity()->getAttribute($attrName);

            if (is_object($attr)) {
                $attr->smartSearchCondition($id, 0, $path, $query, $this->_getEntity()->getTable(), $value, $this->m_mode, '');
            }
        }
    }

    /**
     * Apply criteria that are part of the postvars (e.g. filter, index, search criteria)
     *
     * @param atkQuery $query           query
     * @param array    $attrsByLoadType attributes by load type
     */

    protected function _applyPostvarsToQuery(atkQuery $query, array $attrsByLoadType)
    {
        if (!$this->m_ignorePostvars) {
            $this->_applyPostedFilterToQuery($query);
            $this->_applyPostedIndexValueToQuery($query);
            $this->_applyPostedSearchMethodToQuery($query);
            $this->_applyPostedSearchCriteriaToQuery($query, $attrsByLoadType);
            $this->_applyPostedSmartSearchCriteriaToQuery($query);
        }
    }

    /**
     * Apply entity filters to query.
     *
     * @param atkQuery $query query
     */

    protected function _applyFiltersToQuery(atkQuery $query)
    {
        if ($this->m_ignoreDefaultFilters) {
            return;
        }

        // key/value filters
        foreach ($this->_getEntity()->m_filters as $key => $value) {
            $query->addCondition($key . "='" . $this->_getDb()->escapeSQL($value) . "'");
        }

        // fuzzy filters

        foreach ($this->_getEntity()->m_fuzzyFilters as $filter) {
            $parser = new Adapto_StringParser($filter);
            $filter = $parser->parse(array('table' => $this->_getEntity()->getTable()));
            $query->addCondition($filter);
        }
    }

    /**
     * Is attribute load required?
     *
     * @param atkAttribute $attr attribute
     *
     * @return boolean load required?
     */

    protected function _isAttributeLoadRequired($attr)
    {
        $attrName = $attr->fieldName();

        return (!$this->m_ignorePrimaryKey && in_array($attrName, $this->_getEntity()->m_primaryKey))
                || (!$this->m_ignoreForceLoad && $attr->hasFlag(AF_FORCE_LOAD))
                || (($this->m_includes != null && in_array($attrName, $this->m_includes))
                        || ($this->m_excludes != null && !in_array($attrName, $this->m_excludes))) || ($this->m_excludes == null && $this->m_includes == null);
    }

    /**
     * Returns the attributes for each load type (PRELOAD, ADDTOQUERY, POSTLOAD)
     *
     * @return array attributes by load type
     */

    protected function _getAttributesByLoadType()
    {
        $isSearching = $this->_isSearching();
        $result = array(PRELOAD => array(), ADDTOQUERY => array(), POSTLOAD => array());

        foreach ($this->_getEntity()->getAttributes() as $attr) {
            if (!$this->_isAttributeLoadRequired($attr)) {
                continue;
            }

            $loadType = $attr->loadType($this->m_mode, $isSearching);

            if (hasFlag($loadType, PRELOAD)) {
                $result[PRELOAD][$attr->fieldName()] = $attr;
            }

            if (hasFlag($loadType, ADDTOQUERY)) {
                $result[ADDTOQUERY][$attr->fieldName()] = $attr;
            }

            if (hasFlag($loadType, POSTLOAD)) {
                $result[POSTLOAD][$attr->fieldName()] = $attr;
            }
        }

        return $result;
    }

    /**
     * Apply attributes to query, e.g. add columns etc.
     *
     * @param atkQuery $query           query object
     * @param array    $attrsByLoadType attributes by load type
     */

    protected function _applyAttributesToQuery(atkQuery $query, array $attrsByLoadType)
    {
        $record = array();
        foreach ($attrsByLoadType[PRELOAD] as $attr) {
            $record[$attr->fieldName()] = $attr->load($this->_getDb(), $record, $this->m_mode);
        }

        foreach ($attrsByLoadType[ADDTOQUERY] as $attr) {
            $attr->addToQuery($query, $this->_getEntity()->getTable(), '', $record, 1, $this->m_mode);
        }
    }

    /**
     * Build base query object.
     *
     * @param array $attrsByLoadType attributes by load type
     *
     * @return atkQuery query object
     */

    protected function _buildQuery(array $attrsByLoadType)
    {
        $query = $this->_getEntity()->getDb()->createQuery();
        $query->setDistinct($this->m_distinct);
        $query->addTable($this->_getEntity()->getTable());

        $this->_applyConditionsToQuery($query);
        $this->_applyFiltersToQuery($query);
        $this->_applyPostvarsToQuery($query, $attrsByLoadType);
        $this->_applyAttributesToQuery($query, $attrsByLoadType);

        return $query;
    }

    /**
     * Build select query object.
     *
     * @param array $attrsByLoadType attributes by load type
     *
     * @return atkQuery query object
     */

    protected function _buildSelectQuery(array $attrsByLoadType)
    {
        $query = $this->_buildQuery($attrsByLoadType);

        if (!empty($this->m_order)) {
            $query->addOrderBy($this->m_order);
        }

        if ($this->m_limit >= 0) {
            $query->setLimit($this->m_offset, $this->m_limit);
        }

        return $query;
    }

    /**
     * Build count query object.
     *
     * @param array $attrsByLoadType attributes by load type
     *
     * @return atkQuery query object
     */

    protected function _buildCountQuery(array $attrsByLoadType)
    {
        return $this->_buildQuery($attrsByLoadType);
    }

    /**
     * Returns all bind parameters for all conditions.
     *
     * @return array bind parameters
     */

    protected function _getBindParameters()
    {
        $params = array();

        foreach ($this->m_conditions as $condition) {
            $params = array_merge($params, $condition['params']);
        }

        return $params;
    }

    /**
     * Transform raw database row to entity compatible row.
     *
     * @param array    $row             raw database row
     * @param atkQuery $query           query object
     * @param array    $attrsByLoadType attributes by load type
     *
     * @return array entity compatible row
     */

    protected function _transformRow($row, atkQuery $query, array $attrsByLoadType)
    {
        $query->deAlias($row);
        atkDataDecode($row);

        $result = array();
        foreach ($attrsByLoadType[ADDTOQUERY] as $attr) {
            $result[$attr->fieldName()] = $attr->db2value($row);
        }

        if (!$this->m_ignorePrimaryKey) {
            $result['atkprimkey'] = $this->_getEntity()->primaryKey($result);
        }

        foreach ($attrsByLoadType[POSTLOAD] as $attr) {
            $result[$attr->fieldName()] = $attr->load($this->_getDb(), $result, $this->m_mode);
        }

        return $result;
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
        foreach ($rows as &$row) {
            $row = $this->_transformRow($row, $query, $attrsByLoadType);
        }

        return $rows;
    }

    /**
     * Transform raw database row to entity compatible row for the current iterator.
     * 
     * @param array $row raw database row
     * 
     * @return array entity compatible row
     */

    public function transformRow($row)
    {
        if ($this->m_iterator == null) {
            throw new Exception(__METHOD__ . ' should only be called by the current atkSelectorIterator instance!');
        }

        return $this->_transformRow($row, $this->m_query, $this->m_attrsByLoadType);
    }

    /**
     * Returns the first found row.
     *
     * @return array first row
     */

    public function getFirstRow()
    {
        $this->limit(1, $this->m_offset);
        $rows = $this->getAllRows();
        return count($rows) == 1 ? $rows[0] : null;
    }

    /**
     * Return all rows.
     *
     * @return array all rows
     */

    public function getAllRows()
    {
        if ($this->m_rows === null) {
            $attrsByLoadType = $this->_getAttributesByLoadType();
            $query = $this->_buildSelectQuery($attrsByLoadType);
            $stmt = $this->_getDb()->prepare($query->buildSelect());
            $stmt->execute($this->_getBindParameters());
            $rows = $stmt->getAllRows();
            $stmt->close();
            $this->m_rows = $this->_transformRows($rows, $query, $attrsByLoadType);
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
            $attrsByLoadType = $this->_getAttributesByLoadType();
            $query = $this->_buildCountQuery($attrsByLoadType);
            $stmt = $this->_getDb()->prepare($query->buildCount());
            $stmt->execute($this->_getBindParameters());
            $rows = $stmt->getAllRows();
            $stmt->close();
            $this->m_rowCount = count($rows) == 1 ? $rows[0]['count'] : count($rows); // group by fix
        }

        return $this->m_rowCount;
    }

    /**
     * Returns the available indices for the index field based on the criteria.
     *
     * @return array available indices
     */

    public function getIndices()
    {
        if ($this->_getEntity()->m_index == null) {
            return array();
        } else if ($this->m_indices != null) {
            return $this->m_indices;
        }

        $attrsByLoadType = $this->_getAttributesByLoadType();

        $index = $this->_getEntity()->m_index;
        $this->_getEntity()->m_index = null;
        $query = $this->_buildQuery($attrsByLoadType);
        $this->_getEntity()->m_index = $index;

        $query->clearFields();
        $query->clearExpressions();

        $indexColumn = $this->_getDb()->quoteIdentifier($this->_getEntity()->getTable()) . '.' . $this->_getDb()->quoteIdentifier($index);
        $expression = "UPPER(" . $this->_getDb()->func_substring($indexColumn, 1, 1) . ")";
        $query->addExpression('index', $expression);
        $query->addGroupBy($expression);
        $query->addOrderBy($expression);

        $stmt = $this->_getDb()->prepare($query->buildSelect());
        $stmt->execute($this->_getBindParameters());
        $this->m_indices = $stmt->getAllValues();
        $stmt->close();

        return $this->m_indices;
    }

    /**
     * Does the given offset exist?
     *
     * @param string|int $key key
     * @return boolean offset exists?
     */

    public function offsetExists($key)
    {
        $this->getAllRows();
        return isset($this->m_rows[$key]);
    }

    /**
     * Returns the given offset.
     *
     * @param string|int $key key
     * @return mixed
     */

    public function offsetGet($key)
    {
        $this->getAllRows();
        return $this->m_rows[$key];
    }

    /**
     * Sets the value for the given offset.
     *
     * @param string|int $key
     * @param mixed $value
     */

    public function offsetSet($key, $value)
    {
        $this->getAllRows();
        return $this->m_rows[$key] = $value;
    }

    /**
     * Unset the given element.
     *
     * @param string|int $key
     */

    public function offsetUnset($key)
    {
        $this->getAllRows();
        unset($this->m_rows[$key]);
    }

    /**
     * Returns this selector's iterator.
     * 
     * NOTE: if you call this method multiple times, the same iterator will
     *       be returned, unless you have closed the selector first
     */

    public function getIterator()
    {
        if ($this->m_iterator == null) {
            $attrsByLoadType = $this->_getAttributesByLoadType();
            $query = $this->_buildSelectQuery($attrsByLoadType);
            $stmt = $this->_getDb()->prepare($query->buildSelect());
            $stmt->execute($this->_getBindParameters());

            $this->m_attrsByLoadType = $attrsByLoadType;
            $this->m_query = $query;
            $this->m_stmt = $stmt;

            $this->m_iterator = new Adapto_SelectorIterator($this->m_stmt->getIterator(), $this);
        }

        return $this->m_iterator;
    }

    /**
     * Closes the current statement used for this selector.
     * Also clears the row and row count cache.
     */

    public function close()
    {
        if ($this->m_iterator != null) {
            $this->m_iterator = null;
            $this->m_stmt->close();
            $this->m_stmt = null;
            $this->m_query = null;
            $this->m_attrsByLoadType = null;
        }

        $this->m_rows = null;
        $this->m_rowCount = null;
        $this->m_indices = null;
    }

    /**
     * Returns the row count (used when calling count on an Adapto_Util_Selector object,
     * don't use this if you want to efficiently retrieve the row count using
     * a count() select statement, use rowCount instead!
     *
     * @return int row count
     */

    public function count()
    {
        $this->getAllRows();
        return count($this->m_rows);
    }
}
