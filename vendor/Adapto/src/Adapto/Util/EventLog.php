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
 * @copyright (c)2005 Ibuildings.nl BV
 * @license http://www.achievo.org/atk/licensing ATK Open Source License
 *

 */

/**
 * Base class import.
 */

/**
 * The Adapto_Util_EventLog is a ready to use atkActionListener for logging events
 * in a table.
 *
 * You can use the Adapto_Util_EventLog by adding an instance to an entity using
 * atkEntity's addListener() method.
 *
 * In order to use the Adapto_Util_EventLog, you have to have a table in the database
 * named 'atkeventlog' with the following structure:
 *
 * CREATE TABLE atkeventlog
 * (
 *   id INT(10),
 *   userid INT(10),
 *   stamp DATETIME,
 *   entity VARCHAR(100),
 *   action VARCHAR(100),
 *   primarykey VARCHAR(255)
 * }
 *
 * The current implementation only supports the logging.
 * @todo Add visualisation of the log.
 *
 * @author ijansch
 * @package adapto
 * @subpackage utils
 */
class Adapto_Util_EventLog extends Adapto_ActionListener
{

    /**
     * This method handles the storage of the action in the database.
     *
     * @param String $action The action being performed
     * @param array $record The record on which the action is performed
     */
    function actionPerformed($action, $record)
    {
        $user = &getUser();
        $userid = $user[Adapto_Config::getGlobal("auth_userpk")];
        if ($userid == "")
            $userid = 0;
        // probably administrator
        $entity = $this->m_entity->atkEntityType();
        $db = &$this->m_entity->getDb();
        $primarykey = $db->escapeSQL($this->m_entity->primaryKey($record));

        $db
                ->query(
                        "INSERT INTO atkeventlog (id, userid, stamp, entity, action, primarykey)
                    VALUES(" . $db->nextid("atkeventlog") . ", $userid, " . $db->func_now() . ", '$entity', '$action', '$primarykey')");
        $db->commit();
    }
}

?>