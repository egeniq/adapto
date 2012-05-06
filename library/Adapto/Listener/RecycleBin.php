<?php

/**
 * This file is part of the ATK Framework distribution.
 * Detailed copyright and licensing information can be found
 * in the doc/COPYRIGHT and doc/LICENSE files which should be
 * included in the distribution.
 *
 * @package adapto
 * @subpackage listeners
 *
 * @copyright (c)2010 Ibuildings
 * @license http://www.atk-framework.com/licensing ATK Open Source License
 * 

 */

/**
 * The Adapto_Listener_RecycleBin is a generic recycle bin for records. You can add it 
 * to any entity and if a record from that entity will get deleted,
 * Adapto_Listener_RecycleBin will kick in and transfer the record to the recyclebin.
 * 
 * There are 2 modes of operation. You can build your own recyclebin entity,
 * and Adapto_Listener_RecycleBin will use that to store the deleted record.
 * 
 * Alternatively, you can skip creating an entity, and just create a table
 * that is identical to the one you're deleting records from. 
 * If you don't specify this table, Adapto_Listener_RecycleBin will assume that
 * the table is called tablename_bin, where tablename is the tablename
 * from the entity you're deleting records from. 
 * 
 * Usage: $entity->addListener(new Adapto_Listener_RecycleBin());
 * 
 * @todo a third mode of operation might be one serialized recyclebin
 * for all the tables in the application.
 *
 * @author ijansch
 * @package adapto
 * @subpackage listeners
 *
 */
class Adapto_Listener_RecycleBin extends Adapto_TriggerListener
{
    /**
     * The options for the recycle bin.
     */
    protected $_options = array();

    /**
     * Construct a new Adapto_Listener_RecycleBin
     *
     * @param array $options Supports the following keys:
     *                       "entity"  - Use a specific entity as the recyclebin
     *                       "table" - Use a specfic table as the recyclebin (table needs to be
     *                                 identical to the table the records are deleted from.
     *                       If both table and entity are ommitted, a default table with
     *                       appendix _bin is assumed.
     */

    public function __construct($options = array())
    {
        parent::__construct(array("delete"));

        $this->_options = $options;
    }

    /**
     * This is the actual trigger that moves the record to the recycle bin table.
     *
     * @param array $record The record that is being deleted
     * @return false if there was an error, true if everything is ok
     */

    public function preDelete($record)
    {
        Adapto_Util_Debugger::debug("delete performed, storing record in recyclebin");

        if (isset($this->_options["entity"])) {

            $entity = atkGetEntity($this->_options["entity"]);
            $entity->addDb($record);

        } else {

            $entity = clone ($this->m_entity);

            $pkFields = $entity->m_primaryKey;
            foreach ($pkFields as $fieldName) {

                // We need to make sure the record in the bin has the same primary key as the original
                // record, so we remove AF_AUTOINCREMENT and setForceInsert.
                $entity->getAttribute($fieldName)->setForceInsert(true)->removeFlag(AF_AUTO_INCREMENT);
            }

            if (isset($this->_options["table"])) {
                $entity->setTable($this->_options["table"]);
            } else { // default behaviour: assume table with _bin appendix
                $entity->setTable($entity->getTable() . "_bin");
            }
            Adapto_Util_Debugger::debug("adding record to recyclebin");
            $entity->addDb($record);
        }
        return true;
    }

}
