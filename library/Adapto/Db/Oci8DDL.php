<?php

/**
 * This file is part of the Adapto Toolkit.
 * Detailed copyright and licensing information can be found
 * in the doc/COPYRIGHT and doc/LICENSE files which should be 
 * included in the distribution.
 *
 * @package adapto
 * @subpackage db
 *
 * @copyright (c)2000-2004 Ibuildings.nl BV
 * @license http://www.achievo.org/atk/licensing ATK Open Source License
 *

 */

/**
 * Oracle 8i ddl driver. 
 *
 * Implements specific ddl statements for Oracle 8i databases.
 *
 * @author Martin Roest <martin@ibuildings.nl>
 * @package adapto
 * @subpackage db
 *
 */
class Adapto_Db_Oci8DDL extends Adapto_DDL
{
    /**
     * Constructor
     *
     * @return Adapto_Db_Oci8DDL
     */

    public function __construct()
    {
    }

    /**
     * Convert an ATK generic datatype to a database specific type.
     *
     * @param string $generictype  The datatype to convert.
     */
    function getType($generictype)
    {
        switch ($generictype) {
        case "number":
            return "NUMBER";
        case "decimal":
            return "FLOAT";
        case "string":
            return "VARCHAR2";
        case "date":
            return "DATE";
        case "text":
            return "CLOB";
        case "datetime":
            return "DATE";
        case "time":
            return "DATE";
        case "boolean":
            return "NUMBER(1,0)"; // size is added fixed. (because a boolean has no size of its own)          
        }
        return ""; // in case we have an unsupported type.      
    }

    /**
     * Convert an database specific type to an ATK generic datatype.
     * 
     * This function will be overrided by the database specific subclasses of
     * atkDb.     
     * 
     * @param string $type  The database specific datatype to convert.
     */
    function getGenericType($type)
    {
        $type = strtolower($type);
        switch ($type) {
        case "number":
            return "number";
        case "char":
        case "varchar2":
            return "string";
        case "date":
            return "date";
        case "clob":
            return "text";
        case "date":
            return "date";
        case "timestamp":
            return "datetime";
        }

        if (preg_match('/^timestamp\([0-9]\)$/', $type))
            return "datetime";

        return ""; // in case we have an unsupported type.      
    }
}
?>