<?php
/**
 * This file is part of the Adapto Toolkit.
 * Detailed copyright and licensing information can be found
 * in the doc/COPYRIGHT and doc/LICENSE files which should be
 * included in the distribution.
 *
 * @package adapto
 * @subpackage relations
 *
 * @copyright (c)2000-2004 Ibuildings.nl BV
 * @license http://www.achievo.org/atk/licensing ATK Open Source License
 *

 */

/**
 * @internal includes..
 */
userelation("atkManyToOneRelation");
include_once(Adapto_Config::getGlobal("atkroot") . "atk/atktreetools.inc");

/**
 * Extension of the atkManyToOneRelation, that is aware of the treestructure
 * (parent/child relation) in the destination entity, and renders items in the
 * dropdown accordingly. You need to set the AF_PARENT flag to the parent 
 * column in the destination entity in order to make the tree rendering work.
 *
 * @author Sandy Pleyte <sandy@ibuildings.nl>
 * @package adapto
 * @subpackage relations
 *
 */
class Adapto_Relation_ManyToOneTree extends Adapto_ManyToOneRelation
{
    public $m_current = ""; // defaulted to public
    public $m_level = ""; // defaulted to public

    /**
     * Constructor
     * @param string $name Name of the attribute
     * @param string $destination Destination entity for this relation
     * @param int $flags Flags for the relation
     */

    public function __construct($name, $destination, $flags = 0)
    {
        parent::__construct($name, $destination, $flags);
    }

    /**
     * Returns a piece of html code that can be used in a form to edit this
     * attribute's value.
     * 
     * @param array $record The record that holds the value for this attribute.
     * @param String $fieldprefix The fieldprefix to put in front of the name
     *                            of any html form element for this attribute.
     * @param String $mode The mode we're in ('add' or 'edit')
     * @return Piece of html code that can  be used in a form to edit this
     */
    function edit($record = "", $fieldprefix = "", $mode = "")
    {
        $this->createDestination();
        $tmp1 = Adapto_array_merge($this->m_destInstance->descriptorFields(), $this->m_destInstance->m_primaryKey);
        $tmp2 = Adapto_array_merge($tmp1, array($this->m_destInstance->m_parent));
        if ($this->m_destinationFilter != "") {
            $this->m_destInstance->addFilter(stringparse($this->m_destinationFilter, $record));
        }
        $recordset = $this->m_destInstance->selectDb("", $this->m_destInstance->m_primaryKey[0], "", "", $tmp2);
        $this->m_current = $this->m_ownerInstance->primaryKey($record);
        $result = '<select name="' . $fieldprefix . $this->formName() . '">';

        if ($this->hasFlag(AF_OBLIGATORY) == false) {
            // Relation may be empty, so we must provide an empty selectable..
            $result .= '<option value="0">' . atktext('select_none');
        }
        $result .= $this->createdd($recordset);
        $result .= '</select>';
        return $result;
    }

    /**
     * Returns a piece of html code that can be used in a form to search
     * @param array $record Record
     * @return Piece of html code that can  be used in a form to edit this
     */
    function search($record = "")
    {
        $this->createDestination();
        if ($this->m_destinationFilter != "") {
            $this->m_destInstance->addFilter(stringparse($this->m_destinationFilter, $record));
        }
        $recordset = $this->m_destInstance
                ->selectDb("", "", "", "", Adapto_array_merge($this->m_destInstance->descriptorFields(), $this->m_destInstance->m_primaryKey));

        $result = '<select name="atksearch[' . $this->fieldName() . ']">';

        $pkfield = $this->m_destInstance->primaryKeyField();

        $result .= '<option value="">' . atktext("search_all", "atk");
        $result .= $this->createdd($recordset);
        $result .= '</select>';
        return $result;
    }

    /**
     * Create all the options
     *
     * @param array $recordset
     * @return string The HTML code for the options
     */
    function createdd($recordset)
    {
        $t = new tree;
        for ($i = 0; $i < count($recordset); $i++) {
            $group = $recordset[$i];
            $t
                    ->addEntity($recordset[$i][$this->m_destInstance->m_primaryKey[0]], $this->m_destInstance->descriptor($group),
                            $recordset[$i][$this->m_destInstance->m_parent][$this->m_destInstance->m_primaryKey[0]]);
        }
        $tmp = $this->render($t->m_tree);
        return $tmp;
    }

    /**
     * Render the tree
     *
     * @param array $tree Array of tree entitys
     * @param int $level
     * @return string The rendered tree
     */
    function render($tree = "", $level = 0)
    {
        $res = "";
        while (list($id, $objarr) = each($tree)) {
            $i++;
            if ($this->m_current != $this->m_destInstance->m_table . "." . $this->m_destInstance->m_primaryKey[0] . "='" . $objarr->m_id . "'") {
                $this->m_level = $level;
                $sel = "";
            } else {
                // if equal, select the option it and do not render childs (parent cannot be moved to a childentity of its own)
                $sel = "SELECTED";
            }

            $res .= '<option value="' . $this->m_destInstance->m_table . "." . $this->m_destInstance->m_primaryKey[0] . "='" . $objarr->m_id . "'" . '" '
                    . $sel . '>' . str_repeat("-", (2 * $level)) . " " . $objarr->m_label;

            if (count($objarr->m_sub) > 0 && $sel == "") {
                $res .= $this->render($objarr->m_sub, $level + 1);
            }
        }
        $this->m_level = 0;
        return $res;
    }
}
?>
