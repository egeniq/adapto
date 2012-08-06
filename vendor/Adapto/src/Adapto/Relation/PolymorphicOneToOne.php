<?php
userelation("atkonetoonerelation");

class Adapto_Relation_PolymorphicOneToOne extends Adapto_OneToOneRelation
{
    /**
     * The name of the foreign key field in the master entity to the type table.
     * @access private
     * @var String
     */
    public $m_typefk = ""; // defaulted to public

    /**
     * The name of the foreign key field in the master entity to the type table.
     * @access private
     * @var String
     */
    public $m_discriminatorfield = ""; // defaulted to public

    /**
     * $modulename The module name
     * @access private
     * @var String
     */
    public $m_modulename = ""; // defaulted to public

    /**
     * Default Constructor
     *
     * The Adapto_Relation_PolymorphicOneToOne extends Adapto_OneToOneRelation:
     * <b>Example:</b>
     * <code>
     *  $this->add(new Adapto_Relation_PolymorphicOneToOne("details","fruittype_id","table","poly.orange",
     *               "poly","fruit_id",AF_CASCADE_DELETE ));
     * </code>
     *
     * @param String $name The unique name of the attribute.
     * @param String $typefk The name of the foreign key field in the master entity to the type table .
     * @param String $discriminatorfield The name of the field in the type table wich stores the type tablename
     * (an entity with the same name must be created).
     * @param String $defaultdest The default destination entity (in module.entityname
     *                            notation)
     * @param String $modulename The module name
     * @param String $refKey Specifies the foreign key
     *                       field from the destination entity that points to
     *                       the master record.
     * @param int $flags Attribute flags that influence this attributes'
     *                   behavior.
     */

    public function __construct($name, $typefk, $discriminatorfield, $defaultdest, $modulename, $refKey, $flags = 0)
    {
        parent::__construct($name, "", $refKey, $flags | AF_HIDE_LIST);
        $this->m_typefk = $typefk;
        $this->m_discriminatorfield = $discriminatorfield;
        $this->m_destination = $defaultdest;
        $this->m_modulename = $modulename;
    }

    function loadType()
    {
        return POSTLOAD;
    }

    /**
     * Retrieve detail records from the database.
     *
     * Called by the framework to load the detail records.
     *
     * @param atkDb $db The database used by the entity.
     * @param array $record The master record
     * @param String $mode The mode for loading (admin, select, copy, etc)
     *
     * @return array Sets the destination from the record and
     *                       return the atkonetoone load function
     */
    function load(&$db, $record, $mode)
    {
        $this->m_destination = $this->m_modulename . "." . $record[$this->m_typefk][$this->m_discriminatorfield];
        $this->m_destInstance = $this->m_modulename . "." . $record[$this->m_typefk][$this->m_discriminatorfield];
        return parent::load($db, $record, $mode);
    }
}
?>