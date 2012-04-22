<?php
/**
 * This file is part of the Adapto Toolkit.
 * Detailed copyright and licensing information can be found
 * in the doc/COPYRIGHT and doc/LICENSE files which should be
 * included in the distribution.
 *
 * @package adapto
 * @subpackage attributes
 *
 * @copyright (c)2000-2004 Ivo Jansch
 * @license http://www.achievo.org/atk/licensing ATK Open Source License
 */

useattrib("atkMultiSelectAttribute");

/**
 * The Adapto_Attribute_FuzzySearch class represents an attribute of an entity
 * that has a field where you can enter certain keywords to search for
 * on another entity.
 *
 * @author ijansch
 * @package adapto
 * @subpackage attributes
 */
class Adapto_Attribute_FuzzySearch extends Adapto_Attribute
{
    /**
     * The entity we are searching on
     * @var String
     * @access private
     */
    public $m_searchentity = ""; // defaulted to public

    /**
     * The function to call back with the record and results
     * @var String
     * @access private
     */
    public $m_callback = ""; // defaulted to public

    /**
     * The mode of the the fuzzy search
     * @var String
     * @access private
     */
    public $m_mode = "all"; // defaulted to public

    /**
     * The matches we got from the search
     * @var Array
     * @access private
     */
    public $m_matches = array(); // defaulted to public

    /**
     * An instance of the entity we are searching on
     * @var int
     * @access private
     */
    public $m_searchentityInstance = NULL; // defaulted to public

    /**
     * @var String Filter for destination records.
     */
    public $m_destinationFilter = ""; // defaulted to public

    /**
     * The fuzzySearchAttribute, with this you can search an entity for certain keywords
     * and get a selectable list of records that match the keywords.
     * Possible modes:
     * - all (default)    return everything
     * - first            return only the first result
     * - firstperkeyword  return the first result per keyword
     * - select           make the user select
     * - selectperkeyword make the user select for every keyword
     * - multiselect      ?
     * @param String $name       The name of the attribute
     * @param String $searchentity The entity to search on
     * @param String $callback   The function of the owner entity to call
     *                           with the record to store and the results of the search
     *                           Has to return a status (true or false)
     * @param String $mode       The mode of the search (all(default)|first|firstperkeyword|
     *                                                   select|selectperkeyword|multiselect)
     * @param int    $flags      The flags of the attribute
     * @param int    $size       The size of the search field
     */

    public function __construct($name, $searchentity, $callback, $mode = "all", $flags = 0, $size = 0)
    {
        if ($size == 0) {
            $size = $this->maxInputSize();
        }
        parent::__construct($name, $flags | AF_HIDE_VIEW | AF_HIDE_LIST, $size);
        $this->m_searchentity = $searchentity;
        $this->m_callback = $callback;
        $this->m_mode = strtolower($mode);
    }

    /**
     * Creates an instance of the entity we are searching on and stores it
     * in a member variable ($this->m_searchentityInstance)
     * 
     * @return boolean
     */
    function createSearchEntityInstance()
    {
        if (!is_object($this->m_searchentityInstance)) {
            $this->m_searchentityInstance = &getEntity($this->m_searchentity);
            return is_object($this->m_searchentityInstance);
        }
        return true;
    }

    /**
     * Checks if a value is valid.
     *
     * Note that obligatory and unique fields are checked by the
     * atkEntityValidator, and not by the validate() method itself.
     *
     * @param array $rec    The record that holds the value for this
     *                      attribute. If an error occurs, the error will
     *                      be stored in the 'atkerror' field of the record.
     * @param String $mode The mode for which should be validated ("add" or
     *                     "update")
     */
    function validate(&$rec, $mode)
    {
        if (is_array($rec[$this->fieldName()])) {
            // Coming from selectscreen, no search necessary anymore.
        } else {
            $this->m_matches = $this->getMatches($rec[$this->fieldName()]);

            $mustselect = false;

            if ($this->m_mode == "multiselect" || $this->m_mode == "selectperkeyword") {
                // In multiselect and selectperkeyword mode, we present the selector
                // if one or more keywords returned more than one match. If they
                // all returned exactly one match, we pass all records and don't
                // offer selection.
                foreach ($this->m_matches as $keyword => $res) {
                    if (count($res) > 1) {
                        $mustselect = true;
                        break;
                    }
                }
            } else if ($this->m_mode == "select") {
                // In single select mode, we show the selector if they all return
                // just one match together.
                $total = 0;
                foreach ($this->m_matches as $keyword => $res) {
                    $total += count($res);
                }
                $mustselect = ($total > 1);
            }

            if ($mustselect) {
                triggerError($rec, $this->fieldName(), 'fsa_pleasemakeselection');
                return false;
            }

        }
        return true;
    }

    /**
     * Returns a piece of html code that can be used in a form to edit this
     * attribute's value.
     *
     * @param array $rec The record that holds the value for this attribute.
     * @param String $prefix The fieldprefix to put in front of the name
     *                            of any html form element for this attribute.
     * @param String $mode The mode we're in ('add' or 'edit')
     * @return String A piece of htmlcode for editing this attribute
     */
    function edit($rec = "", $prefix = "", $mode = "")
    {
        // There are 2 possibilities. Either we are going to search,
        // in which case we show a searchbox.
        // Or, a search has already been performed but multiple
        // matches have been found and an atkerror was set.
        // In this case, we show the selects.
        $select = false;

        if (isset($rec['atkerror'])) {
            foreach ($rec['atkerror'] as $error) {
                if ($error['attrib_name'] === $this->fieldName())
                    $select = true;
            }
        }

        if ($select && $this->createSearchEntityInstance()) {
            $res = "";

            // First lets get the results, which were lost during the redirect
            $this->m_matches = $this->getMatches($rec[$this->fieldName()]);

            // Second check if we actually found anything
            if ($this->m_matches) {
                foreach ($this->m_matches as $match) {
                    if (!empty($match)) {
                        $notempty = true;
                        continue;
                    }
                }
                if (!$notempty)
                    return atktext("no_results_found");
            }

            if ($this->m_mode == "multiselect" && count($this->m_matches > 1)) {
                // Select multiple records from all matches
                $checkboxes = array();

                foreach ($this->m_matches as $keyword => $matches) {
                    for ($i = 0, $_i = count($matches); $i < $_i; $i++) {
                        $optionArray[] = $this->m_searchentityInstance->descriptor($matches[$i]);
                        $valueArray[] = $this->m_searchentityInstance->primaryKey($matches[$i]);
                    }
                }

                $attrib = new Adapto_MultiSelectAttribute($this->m_name, $optionArray, $valueArray, 1, AF_NO_LABEL | AF_CHECK_ALL | AF_LINKS_BOTTOM);
                $res .= $attrib->edit();
            } else if ($this->m_mode == "select" || ($this->m_mode == "multiselect" && count($this->m_matches) == 1)) {
                // Select one record from all matches.
                $res .= '<SELECT NAME="' . $prefix . $this->fieldName() . '[]">';
                $res .= '<OPTION VALUE="">' . atktext('select_none');
                $selects = array();
                foreach ($this->m_matches as $keyword => $matches) {
                    for ($i = 0, $_i = count($matches); $i < $_i; $i++) {
                        $item = '<OPTION VALUE="' . $this->m_searchentityInstance->primaryKey($matches[$i]) . '">'
                                . $this->m_searchentityInstance->descriptor($matches[$i]);
                        if (!in_array($item, $selects)) {
                            $selects[] = $item;
                        }
                    }
                    $res .= implode("\n", $selects);
                }
                $res .= '</SELECT>';
            } else if ($this->m_mode == "selectperkeyword") {
                // Select one record per keyword.
                $res = '<table border="0">';
                foreach ($this->m_matches as $keyword => $matches) {
                    if (count($matches) > 0) {
                        $res .= '<tr><td>\'' . $keyword . '\': </td><td><SELECT NAME="' . $prefix . $this->fieldName() . '[]">';
                        $res .= '<OPTION VALUE="">' . atktext('select_none');
                        for ($i = 0, $_i = count($matches); $i < $_i; $i++) {
                            $res .= '<OPTION VALUE="' . $this->m_searchentityInstance->primaryKey($matches[$i]) . '">'
                                    . $this->m_searchentityInstance->descriptor($matches[$i]);
                        }
                        $res .= '</SELECT></td></tr>';
                    }
                }
                $res .= '</table>';
            }
            return $res;
        } else {
            $rec = ""; // clear the record so we always start with an empty
            // searchbox.
            return parent::edit($rec, $prefix);
        }
    }

    /**
     * The actual function that does the searching
     * @param String $searchstring The string to search for
     * @return Array The matches
     */
    function getMatches($searchstring)
    {
        atkdebug("Performing search");
        $result = array();

        if ($this->createSearchEntityInstance() && $searchstring != "") {
            $this->m_searchentityInstance->addFilter($this->getDestinationFilter());
            $tokens = explode(",", $searchstring);
            foreach ($tokens as $token) {
                $token = trim($token);
                $result[$token] = $this->m_searchentityInstance->searchDb($token);
            }
        }
        return $result;
    }

    /**
     * Override the store method of this attribute to search
     *
     * @param atkDb $db
     * @param array $rec The record
     * @param string $mode
     * @return boolean
     */
    function store($db, $rec, $mode)
    {
        $resultset = array();

        if (is_array($rec[$this->fieldName()])) {
            // If the value is an array, this means we must have come from a select.
            // The user has selected some options, and we must process those.

            // First, load the records, based on the where clauses.
            $wheres = array();
            $matches = $rec[$this->fieldName()];
            for ($i = 0, $_i = count($matches); $i < $_i; $i++) {
                if ($matches[$i] != "")
                    $wheres[] = $matches[$i];
            }
            if (count($wheres) && $this->createSearchEntityInstance()) {
                $whereclause = "((" . implode(") OR (", $wheres) . "))";

                $resultset = $this->m_searchentityInstance
                        ->selectDb($whereclause, $this->m_searchentityInstance->m_defaultOrder, "", $this->m_searchentityInstance->m_listExcludes, "", "admin");
            }
        } else if (count($this->m_matches) > 0) {
            // We didn't come from a select, but we found something anyway.
            // Depending on our mode parameter, we either pass all records to
            // the callback, or the first for every keyword, or the very first.
            if ($this->m_mode == "all") {
                // Pass all matches.
                foreach ($this->m_matches as $keyword => $matches) {
                    for ($i = 0, $_i = count($matches); $i < $_i; $i++) {
                        // Make sure there are no duplicates
                        if (!in_array($matches[$i], $resultset)) {
                            $resultset[] = $matches[$i];
                        }
                    }
                }
            } else if ($this->m_mode == "firstperkeyword") {
                // Pass first matches of all keywords.
                foreach ($this->m_matches as $keyword => $matches) {
                    if (count($matches)) {
                        $resultset[] = $matches[0];
                    }
                }
            } else if ($this->m_mode == "first") {
                // Pass only the first record of the first match.
                if (count($this->m_matches)) {
                    $first = reset($this->m_matches);
                    if (count($first)) {
                        $resultset[] = $first[0];
                    }
                }
            } else {
                // We get here if one of the SELECT modes is active, but no
                // selection was made. Getting here means that the validate()
                // method above decided that presenting a selector was not
                // necessary. We trust that judgement, and pass all records
                // that were found.

                foreach ($this->m_matches as $keyword => $matches) {
                    for ($i = 0, $_i = count($matches); $i < $_i; $i++) {
                        // Make sure there are no duplicates
                        if (!in_array($matches[$i], $resultset)) {
                            $resultset[] = $matches[$i];
                        }
                    }
                }
            }
        }

        if (count($resultset)) {
            if (method_exists($this->m_ownerInstance, $this->m_callback)) {
                $funcname = $this->m_callback;
                return $this->m_ownerInstance->$funcname($rec, $resultset);
            }
        }

        return true;
    }

    /**
     * Dummy method to prevent loading/storing of data.
     */ 
    function load()
    {
    }

    /**
     * Dummy method to prevent loading/storing of data.
     */
    function addToQuery()
    {
    }

    /**
     * Dummy method to prevent loading/storing of data.
     */
    function hide()
    {
    }

    /**
     * Dummy method to prevent loading/storing of data.
     */
    function search()
    {
    }

    /**
     * Dummy method to prevent loading/storing of data.
     * @return Array empty array
     */
    function getSearchModes()
    {
        return array();
    }

    /**
     * Dummy method to prevent loading/storing of data.
     */
    function searchCondition()
    {
    }

    /**
     * Dummy method to prevent loading/storing of data.
     */
    function getSearchCondition()
    {
    }

    /**
     * Dummy method to prevent loading/storing of data.
     */
    function fetchMeta()
    {
    }

    /**
     * Dummy method to prevent loading/storing of data.
     */
    function dbFieldSize()
    {
    }

    /**
     * Dummy method to prevent loading/storing of data.
     */
    function dbFieldType()
    {
    }

    /**
     * Adds a filter on the instance of the searchentity
     * @param String $filter The fieldname you want to filter OR a SQL where
     *                       clause expression.
     * @param String $value Required value. (Ommit this parameter if you pass
     *                      an SQL expression for $filter.)
     */
    function addSearchFilter($filter, $value = "")
    {
        if (!$this->m_searchentityInstance)
            $this->createSearchEntityInstance();
        $this->m_searchentityInstance->addFilter($filter, $value);
    }

    /**
     * Returns the destination filter.
     * @return String The destination filter.
     */
    function getDestinationFilter()
    {
        return $this->m_destinationFilter;
    }

    /**
     * Sets the destination filter.
     * @param String $filter The destination filter.
     */
    function setDestinationFilter($filter)
    {
        $this->m_destinationFilter = $filter;
    }

}

?>