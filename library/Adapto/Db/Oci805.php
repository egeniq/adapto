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
 * @internal Include baseclass
 */
require_once(Adapto_Config::getGlobal("atkroot") . "atk/db/class.atkoci8db.inc");

/**
 * Oracle 8.0.5 database driver.
 *
 * Handles database connectivity and database interaction
 * with the Oracle database server version 8.0.5. 
 * (This class might also work with 8.0.x versions prior
 * to 8.0.5)
 *
 * @internal This class does not differ from its baseclass atkoci8db, but
 *           exists because the query builder class part of the driver does
 *           differ from the 8i version.
 *
 * @author ijansch
 * @package adapto
 * @subpackage db
 */
class Adapto_Db_Oci805 extends Adapto_Oci8Db
{
    public $m_type = "oci805"; // defaulted to public

    /**
     * Base constructor
     */

    public function __construct()
    {
        return parent::__construct();
    }

}

?>