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

/** @internal Include base class */

/**
 * Oracle 9i ddl driver. 
 *
 * Based on 8i ddl class. Should work with Oracle 10i databases too.
 *
 * @author Martin Roest <martin@ibuildings.nl>
 * @package adapto
 * @subpackage db
 *
 */ 
class Adapto_Db_Oci9DDL extends Adapto_Oci8DDL
{
    /**
     * Constructor
     *
     * @return Adapto_Db_Oci9DDL
     */

    public function __construct()
    {
    }
}
?>