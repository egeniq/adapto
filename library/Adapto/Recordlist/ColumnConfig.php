<?php
/**
 * This file is part of the Adapto Toolkit.
 * Detailed copyright and licensing information can be found
 * in the doc/COPYRIGHT and doc/LICENSE files which should be
 * included in the distribution.
 *
 * @package adapto
 * @subpackage recordlist
 *
 * @copyright (c)2003-2006 Ibuildings.nl BV
 * @license http://www.achievo.org/atk/licensing ATK Open Source License
 *

 */

/**
 * Sort ascending
 */
define("RL_SORT_ASC", 1);

/**
 * Sort descending
 */
define("RL_SORT_DESC", 2);

/**
 * The Adapto_Recordlist_ColumnConfig class is used to add extended sorting and grouping
 * options to a recordlist.
 *
 * @author ijansch
 * @package adapto
 * @subpackage recordlist
 */
class Adapto_Recordlist_ColumnConfig
{
    public $m_colcfg = array(); // defaulted to public
    public $m_entity = NULL; // defaulted to public
    public $m_orderbyindex = 0; // defaulted to public
    public $m_custom_atkorderby; // defaulted to public

    /**
     * Constructor
     *
     * @return Adapto_Recordlist_ColumnConfig
     */

    public function __construct()
    {
    }

    /**
     * Set the entity
     *
     * @param atkEntity $entity
     */
    function setEntity(&$entity)
    {
        $this->m_entity = &$entity;
    }

    /**
     * Get the entity
     *
     * @return atkEntity The entity
     */
    function &getEntity()
    {
        return $this->m_entity;
    }

    /**
     * Get an instance of the columnconfig class
     *
     * @param atkEntity $entity
     * @param string $id
     * @param boolean $forceNew force new instance?
     *
     * @return atkColumnConfig An instance of the columnconfig class
     */
    function &getConfig(&$entity, $id = null, $forceNew = false)
    {
        global $g_sessionManager;
        static $s_instances = array();

        if ($id == null)
            $id = $entity->atkEntityType();

        if (!isset($s_instances[$id]) || $forceNew) {
            $s_instances[$id] = new Adapto_Recordlist_ColumnConfig();
            $s_instances[$id]->setEntity($entity);

            $colcfg = $g_sessionManager != null ? $g_sessionManager->pageVar("atkcolcfg_" . $id) : null;

            if (!is_array($colcfg) || $forceNew) {
                // create new
                atkdebug("New colconfig initialising");
                $s_instances[$id]->init();
            } else {
                // inherit old config from session.
                atkdebug("Resuming colconfig from session");
                $s_instances[$id]->m_colcfg = &$colcfg;
            }

            // See if there are any url params which influence this colcfg.
            $s_instances[$id]->doUrlCommands();

        }

        if ($g_sessionManager != null)
            $g_sessionManager->pageVar("atkcolcfg_" . $id, $s_instances[$id]->m_colcfg);

        return $s_instances[$id];
    }

    /**
     * Is this attribute last?
     *
     * @param string $attribute
     * @return bool False
     */
    function isLast($attribute)
    {
        return false;
    }

    /**
     * Is this attribute first?
     *
     * @param string $attribute
     * @return bool False
     */
    function isFirst($attribute)
    {
        return false;
    }

    /**
     * Move left
     *
     * @param string $attribute
     */
    function moveLeft($attribute)
    {
        // ??
    }

    /**
     * Move right
     *
     * @param string $attribute
     */
    function moveRight($attribute)
    {
        // ??
    }

    /**
     * Initialize
     *
     */
    function init()
    {
        foreach (array_keys($this->m_entity->m_attribIndexList) as $i) {
            if (isset($this->m_entity->m_attribIndexList[$i]["name"]) && ($this->m_entity->m_attribIndexList[$i]["name"] != "")) {
                $this->m_colcfg[$this->m_entity->m_attribIndexList[$i]["name"]] = array();
            }
        }

        if ($this->m_entity->getOrder() != "")
            $this->_addOrderByStatement($this->m_entity->getOrder());
    }

    /**
     * Hide a column
     *
     * @param string $attribute
     */
    function hideCol($attribute)
    {
        $this->m_colcfg[$attribute]["show"] = 0;
    }

    /**
     * Show a column
     *
     * @param string $attribute
     */
    function showCol($attribute)
    {
        $this->m_colcfg[$attribute]["show"] = 1;
    }

    /**
     * Set sort direction
     *
     * @param string $attribute
     * @param string $direction
     */
    function setSortDirection($attribute, $direction)
    {
        $this->m_colcfg[$attribute]["direction"] = $direction;
    }

    /**
     * Set sort order
     *
     * @param string $attribute
     * @param string $value
     */
    function setSortOrder($attribute, $value)
    {
        if ($value > 0) {
            $this->m_colcfg[$attribute]["sortorder"] = $value;
        } else {
            unset($this->m_colcfg[$attribute]);
        }
    }

    /**
     * Add orderby field
     *
     * @param string $field
     * @param string $direction
     * @param string $extra
     * @param string $sortorder
     */
    function addOrderByField($field, $direction, $extra = "", $sortorder = NULL)
    {
        if (is_null($sortorder) && $this->getMinSort() <= 1) {
            foreach ($this->m_colcfg as $fld => $config) {
                if (atkArrayNvl($config, "sortorder") > 0) {
                    $this->m_colcfg[$fld]["sortorder"] = (int) ($this->m_colcfg[$fld]["sortorder"]) + 1;
                }
            }
        }

        $this->m_colcfg[$field]["sortorder"] = $sortorder === NULL ? 1 : $sortorder;
        $this->m_colcfg[$field]["direction"] = strtolower($direction);
        $this->m_colcfg[$field]["extra"] = $extra;
    }

    /**
     * Flatten
     *
     */
    function flatten()
    {
        $result = uasort($this->m_colcfg, array("Adapto_Recordlist_ColumnConfig", "_compareSortAttrs"));

        $i = 1;
        foreach ($this->m_colcfg as $field => $config) {
            if (array_key_exists("sortorder", $this->m_colcfg[$field]) && ($this->m_colcfg[$field]["sortorder"] > 0)) {
                $this->m_colcfg[$field]["sortorder"] = $i;
                $i++;
            }
        }
    }

    /**
     * Get min sort
     *
     * @return int
     */
    function getMinSort()
    {
        $min = 999;
        foreach ($this->m_colcfg as $field => $config) {
            if (atkArrayNvl($config, "sortorder") > 0) {
                $min = min($min, $config["sortorder"]);
            }
        }
        return $min;
    }

    /**
     * Get orderby statement
     *
     * @return string Orderby statement
     */
    function getOrderByStatement()
    {
        $result = array();

        foreach ($this->m_colcfg as $field => $config) {
            if (atkArrayNvl($config, "sortorder", 0) > 0 && is_object($this->m_entity->m_attribList[$field])) {
                $direction = $config["direction"] == "desc" ? "DESC" : "ASC";
                $res = $this->m_entity->m_attribList[$field]->getOrderByStatement($config['extra'], '', $direction);
                if ($res)
                    $result[] = $res;
            }
        }
        return implode(", ", $result);
    }

    /**
     * Get order fields
     *
     * @return array
     */
    function getOrderFields()
    {
        $result = array();
        foreach ($this->m_colcfg as $field => $config) {
            if (is_object($this->m_entity->m_attribList[$field])) {
                $result[] = $field;
            }
        }
        return $result;
    }

    /**
     * Get sort direction
     *
     * @param string $attribute
     * @return string The sort direction
     */
    function getSortDirection($attribute)
    {
        return $this->m_colcfg[$attribute]["direction"];
    }

    /**
     * Get url command
     *
     * @param string $attribute
     * @param string $command
     * @return string
     */
    function getUrlCommand($attribute, $command)
    {
        return "atkcolcmd[][$command]=" . $attribute;
    }

    /**
     * Get url command params
     *
     * @param string $attribute
     * @param string $command
     * @return string
     */
    function getUrlCommandParams($attribute, $command)
    {
        return array("atkcolcmd[][$command]" => $attribute);
    }

    /**
     * Do url command
     *
     * @param array $cmd
     */
    function doUrlCommand($cmd)
    {
        if (is_array($cmd)) {
            foreach ($cmd as $command => $param) {
                switch ($command) {
                case "asc":
                    $this->setSortDirection($param, "asc");
                    break;
                case "desc":
                    $this->setSortDirection($param, "desc");
                    break;
                case "setorder":
                    list($attrib, $value) = each($param);
                    $this->setSortOrder($attrib, $value);
                    break;
                case "subtotal":
                    $this->setSubTotal($param, true);
                    break;
                case "unsubtotal":
                    $this->setSubTotal($param, false);
                    break;
                }
            }
        }
    }

    /**
     * Do url commands
     *
     */
    function doUrlCommands()
    {
        if (isset($this->m_entity->m_postvars["atkcolcmd"]) && is_array($this->m_entity->m_postvars["atkcolcmd"])) {
            foreach ($this->m_entity->m_postvars["atkcolcmd"] as $command) {
                $this->doUrlCommand($command);
            }
        } else if (isset($this->m_entity->m_postvars["atkorderby"]) && ($this->m_entity->m_postvars["atkorderby"] != "")) {
            $this->clearOrder(); // clear existing order

            // oldfashioned order by.
            $this->m_custom_atkorderby = $this->m_entity->m_postvars["atkorderby"];

            // try to parse..
            $this->_addOrderByStatement($this->m_entity->m_postvars["atkorderby"]);
        }

        // Cleanup structure
        $this->flatten();

    }

    /**
     * Get order
     *
     * @param string $attribute
     * @return string
     */
    function getOrder($attribute)
    {
        return isset($this->m_colcfg[$attribute]["sortorder"]) ? $this->m_colcfg[$attribute]["sortorder"] : 0;
    }

    /**
     * Get direction
     *
     * @param string $attribute
     * @return string
     */
    function getDirection($attribute)
    {
        return (array_key_exists("direction", $this->m_colcfg[$attribute]) ? $this->m_colcfg[$attribute]["direction"] : "desc");
    }

    /**
     * Get attribute by order
     *
     * @param int $order
     * @return atkAttribute
     */
    function getAttributeByOrder($order)
    {
        foreach ($this->m_colcfg as $attrib => $info) {
            if (atkArrayNvl($info, "sortorder", 0) == $order) {
                return $attrib;
            }
        }
        return "";
    }

    /**
     * Count sort attributes
     *
     * @return int
     */
    function countSortAttribs()
    {
        $total = 0;
        foreach ($this->m_colcfg as $attrib => $info) {
            if (atkArrayNvl($info, "sortorder", 0) > 0)
                $total++;
        }
        return $total;
    }

    /**
     * Get direction by order
     *
     * @param int $order
     * @return string
     */
    function getDirectionByOrder($order)
    {
        foreach ($this->m_colcfg as $attrib => $info) {
            if (atkArrayNvl($info, "sortorder", 0) == $order)
                return $this->getDirection($attrib);
        }
        return "asc";
    }

    /**
     * Clear order
     *
     */
    function clearOrder()
    {
        $this->m_colcfg = array();
    }

    /**
     * Has subtotals?
     *
     * @return bool True or false
     */
    function hasSubTotals()
    {
        foreach (array_keys($this->m_colcfg) as $attribute) {
            if ($this->hasSubTotal($attribute))
                return true;
        }
        return false;
    }

    /**
     * Has subtotal?
     *
     * @param string $attribute
     * @return bool True or false
     */
    function hasSubTotal($attribute)
    {
        return ((isset($this->m_colcfg[$attribute]["subtotal"]) ? $this->m_colcfg[$attribute]["subtotal"] : 0) == 1);
    }

    /**
     * Has subtotal by order?
     *
     * @param int $order
     * @return bool True or false
     */
    function hasSubTotalByOrder($order)
    {
        foreach ($this->m_colcfg as $attrib => $info) {
            if (atkArrayNvl($info, "sortorder", 0) == $order)
                return $this->hasSubTotal($attrib);
        }
        return false;
    }

    /**
     * Set subtotal
     *
     * @param string $attribute
     * @param bool $active
     */
    function setSubTotal($attribute, $active)
    {
        $this->m_colcfg[$attribute]["subtotal"] = ($active ? 1 : 0);
    }

    /**
     * Compare sortorder of two attributes
     *
     * @param array $a
     * @param array $b
     * @return int
     */
    function _compareSortAttrs($a, $b)
    {
        return (atkArrayNvl($a, "sortorder", 0) <= atkArrayNvl($b, "sortorder", 0) ? -1 : 1);
    }

    /**
     * Totalizable?
     *
     * @return bool True or false
     */
    function totalizable()
    {
        foreach (array_keys($this->m_entity->m_attribList) as $attribname) {
            $p_attrib = &$this->m_entity->m_attribList[$attribname];
            if ($p_attrib->hasFlag(AF_TOTAL))
                return true;
        }
        return false;
    }

    /**
     * Get the totalizable columns
     *
     * @return array
     */
    function totalizableColumns()
    {
        $result = array();
        foreach (array_keys($this->m_entity->m_attribList) as $attribname) {
            $p_attrib = &$this->m_entity->m_attribList[$attribname];
            if ($p_attrib->hasFlag(AF_TOTAL))
                $result[] = $attribname;
        }
        return $result;
    }

    /**
     * Get the subtotal columns
     *
     * @return array
     */
    function subtotalColumns()
    {
        $result = array();
        foreach (array_keys($this->m_colcfg) as $attribute) {
            if ($this->hasSubTotal($attribute))
                $result[] = $attribute;
        }
        return $result;
    }

    /**
     * Add orderby statement
     *
     * @param string $orderby
     */
    function _addOrderByStatement($orderby)
    {
        if (strpos($orderby, '(') !== false) {
            return; // can't do anything with complex order by's
        }

        $i = 0;
        $expressions = explode(",", $orderby);
        foreach ($expressions as $expression) {
            $expression = trim($expression);
            $expressionParts = preg_split("/\\s+/", $expression);

            if (count($expressionParts) == 2) {
                list($column, $direction) = $expressionParts;
            } else {
                $column = $expression;
                $direction = 'ASC';
            }

            $direction = strtoupper($direction) == 'DESC' ? 'DESC' : 'ASC';

            $part1 = $column;
            $part2 = null;

            if (strpos($column, '.') !== false) {
                list($part1, $part2) = explode('.', $column);
            }

            if ($this->getEntity()->getAttribute($part1) != null) {
                $this->addOrderByField($part1, $direction, $part2, ++$i);
            } else if ($part1 == $this->getEntity()->getTable() && $this->getEntity()->getAttribute($part2) != null) {
                $this->addOrderByField($part2, $direction, null, ++$i);
            } else {
                // custom order by
                $this->addOrderByField($column, $direction, "", ++$i);
            }
        }
    }
}
?>
