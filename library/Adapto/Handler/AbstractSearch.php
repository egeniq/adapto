<?php

/**
 * This file is part of the Adapto Toolkit.
 * Detailed copyright and licensing information can be found
 * in the doc/COPYRIGHT and doc/LICENSE files which should be
 * included in the distribution.
 *
 * @package adapto
 * @subpackage handlers
 *
 * @copyright (c)2000-2008 Ibuildings.nl BV
 * @license http://www.achievo.org/atk/licensing ATK Open Source License
 *

 */

/**
 * Abstract class for implementing an atkSearchHandler
 *
 * @package adapto
 * @subpackage testcases
 */
abstract class Adapto_Handler_AbstractSearch extends Adapto_ActionHandler
{
    /**
     * Holds the table name of the searchcriteria
     * table. Due some BC issues of the atkSmartSearchHandler
     * this value can be overwritten by the checkTable function
     *
     * @var string
     */
    protected $m_table = 'Adapto_searchcriteria';

    /**
     * Indicates if the table
     * Adapto_searchcriteria exists
     * use the function tableExists
     *
     * @var boolean
     */
    protected $m_table_exists = null;

    /**
     * Return the criteria based on the postvarse
     * used for storing
     *
     * @return array
     */

    abstract function fetchCriteria();

    /**
     * Return the type of the atkSmartSearchHandler
     *
     * @return string
     */
    function getSearchHandlerType()
    {
        return strtolower(get_class($this));
    }

    /**
     * check if database table exists
     *
     * @return boolean
     */

    protected function tableExist()
    {
        if ($this->m_table_exists !== null)
            return $this->m_table_exists;

        $db = $this->m_entity->getDb();
        $this->m_table_exists = $db->tableExists($this->m_table);

        atkdebug('tableExists checking table: ' . $this->m_table . ' exists : ' . print_r($this->m_table_exists, true));

        return $this->m_table_exists;
    }

    /**
     * List criteria.
     *
     * @return Array criteria list
     */
    function listCriteria()
    {
        if (!$this->tableExist())
            return array();

        $db = &$this->m_entity->getDb();
        $query = "SELECT c.name FROM {$this->m_table} c WHERE c.entitytype = '%s' ORDER BY UPPER(c.name) AND handlertype = '%s'";
        $rows = $db->getRows(sprintf($query, $this->m_entity->atkEntityType(), $this->getSearchHandlerType()));

        $result = array();
        foreach ($rows as $row)
            $result[] = $row['name'];

        return $result;
    }

    /**
     * Remove search criteria.
     *
     * @param String $name name of the search criteria
     */
    function forgetCriteria($name)
    {
        if (!$this->tableExist())
            return false;

        $db = &$this->m_entity->getDb();
        $query = "DELETE FROM {$this->m_table} WHERE entitytype = '%s' AND UPPER(name) = UPPER('%s') AND handlertype = '%s'";

        $db->query(sprintf($query, $this->m_entity->atkEntityType(), escapeSQL($name), $this->getSearchHandlerType()));
        $db->commit();
    }

    /**
     * Save search criteria.
     *
     * NOTE:
     * This method will overwrite existing criteria with the same name.
     *
     * @param String $name     name for the search criteria
     * @param Array  $criteria search criteria data
     */
    function saveCriteria($name, $criteria)
    {
        if (!$this->tableExist())
            return false;

        $this->forgetCriteria($name);
        $db = &$this->m_entity->getDb();
        $query = "INSERT INTO {$this->m_table} (entitytype, name, criteria, handlertype) VALUES('%s', '%s', '%s', '%s')";
        $db->query(sprintf($query, $this->m_entity->atkEntityType(), escapeSQL($name), escapeSQL(serialize($criteria)), $this->getSearchHandlerType()));
        $db->commit();
    }

    /**
     * Load search criteria.
     *
     * @param String $name name of the search criteria
     * @return Array search criteria
     */
    function loadCriteria($name)
    {
        if (!$this->tableExist())
            return array();

        $db = &$this->m_entity->getDb();
        $query = "SELECT c.criteria FROM {$this->m_table} c WHERE c.entitytype = '%s' AND UPPER(c.name) = UPPER('%s') AND handlertype = '%s'";

        Adapto_var_dump(sprintf($query, $this->m_entity->atkEntityType(), escapeSQL($name), $this->getSearchHandlerType()), 'loadCriteria query');

        list($row) = $db->getRows(sprintf($query, $this->m_entity->atkEntityType(), escapeSQL($name), $this->getSearchHandlerType()));
        $criteria = $row == NULL ? NULL : unserialize($row['criteria']);

        Adapto_var_dump($criteria, 'loadCriteria criteria');
        return $criteria;
    }

    /**
     * Load base criteria.
     *
     * @return Array search criteria
     */
    function loadBaseCriteria()
    {
        return array(array('attrs' => array()));
    }

    /**
     * Returns a select list of loadable criteria which will on-selection
     * refresh the smart search page with the loaded criteria.
     *
     * @param String $current The current load criteria
     * @return String criteria load HTML
     */
    function getLoadCriteria($current)
    {
        $criteria = $this->listCriteria();
        if (count($criteria) == 0)
            return NULL;

        $result = '
      <select name="load_criteria" onchange="this.form.submit();">
        <option value=""></option>';

        foreach ($criteria as $name)
            $result .= '<option value="' . Adapto_htmlentities($name) . '"' . ($name == $current ? ' selected' : '') . '>' . Adapto_htmlentities($name)
                    . '</option>';

        $result .= '</select>';
        return $result;
    }

    /**
     * Take the necessary 'saved criteria' actions based on the
     * posted variables.
     * Returns the name of the saved criteria
     *
     * @param array $criteria array with the current criteria
     * @return string name of the saved criteria
     */
    function handleSavedCriteria($criteria)
    {
        $name = array_key_exists('load_criteria', $this->m_postvars) ? $this->m_postvars['load_criteria'] : '';
        if (!empty($this->m_postvars['forget_criteria'])) {
            $forget = $this->m_postvars['forget_criteria'];
            $this->forgetCriteria($forget);
            $name = NULL;
        } else if (!empty($this->m_postvars['save_criteria'])) {
            $save = $this->m_postvars['save_criteria'];
            $this->saveCriteria($save, $criteria);
            $name = $save;
        }
        return $name;
    }

    /**
     * Returns an array with all the saved criteria
     * information. This information will be parsed
     * to the different
     *
     * @param string $current
     * @return array
     */
    function getSavedCriteria($current)
    {
        // check if table is present
        if (!$this->tableExist())
            return array();

        return array('load_criteria' => $this->getLoadCriteria($current), 'forget_criteria' => $this->getForgetCriteria($current),
                'toggle_save_criteria' => $this->getToggleSaveCriteria(), 'save_criteria' => $this->getSaveCriteria($current),
                'label_load_criteria' => Adapto_htmlentities(atktext('load_criteria', 'atk')),
                'label_forget_criteria' => Adapto_htmlentities(atktext('forget_criteria', 'atk')),
                'label_save_criteria' => '<label for="toggle_save_criteria">' . Adapto_htmlentities(atktext('save_criteria', 'atk')) . '</label>');
    }

    /**
     * Returns a link for removing the currently selected criteria. If
     * nothing (valid) is selected nothing is returned.
     *
     * @param String $current currently loaded criteria
     * @return String forget url
     */
    function getForgetCriteria($current)
    {
        if (empty($current) || $this->loadCriteria($current) == NULL)
            return NULL;
        else
            return session_url(dispatch_url($this->m_entity->atkEntityType(), $this->m_action, array('forget_criteria' => $current)), SESSION_REPLACE);
    }

    /**
     * Returns a checkbox for enabling/disabling the saving of criteria.
     *
     * @return checkbox HTML
     */
    function getToggleSaveCriteria()
    {
        return '<input id="toggle_save_criteria" type="checkbox" class="atkcheckbox" onclick="$(save_criteria).disabled = !$(save_criteria).disabled">';
    }

    /**
     * Returns a textfield for entering a name to save the search criteria as.
     *
     * @param String $current currently loaded criteria
     * @param String textfield HTML
     */
    function getSaveCriteria($current)
    {
        return '<input id="save_criteria" type="text" size="30" name="save_criteria" value="' . Adapto_htmlentities($current) . '" disabled="disabled">';
    }

}
