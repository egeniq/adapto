<?php
/**
 * This file is part of the Adapto Toolkit.
 * Detailed copyright and licensing information can be found
 * in the doc/COPYRIGHT and doc/LICENSE files which should be
 * included in the distribution.
 *
 * @package adapto
 * @subpackage db.statement
 *
 * @copyright (c) 2009 Peter C. Verhage
 * @license http://www.achievo.org/atk/licensing ATK Open Source License
 *

 */

/**
 * Statement exception.
 * 
 * @author petercv
 *
 * @package adapto
 * @subpackage db.statement
 */
class Adapto_Db_Statement_Exception extends Exception
{
    const MISSING_BIND_PARAMETER = 1;
    const NO_DATABASE_CONNECTION = 2;
    const PREPARE_STATEMENT_ERROR = 3;
    const STATEMENT_NOT_EXECUTED = 4;
    const STATEMENT_ERROR = 5;
    const OTHER_ERROR = 6;

    /**
     * Constructor.
     */

    public function __construct($message, $code)
    {
        parent::__construct($message, $code);
    }
}
