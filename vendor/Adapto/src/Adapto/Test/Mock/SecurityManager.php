<?php

/**
 * This file is part of the Adapto Toolkit.
 * Detailed copyright and licensing information can be found
 * in the doc/COPYRIGHT and doc/LICENSE files which should be
 * included in the distribution.
 *
 * @package adapto
 * @subpackage security
 *
 * @copyright (c)2006 Ibuildings.nl BV
 * @license http://www.achievo.org/atk/licensing ATK Open Source License
 *

 */

/** @internal include */

/**
 * The Adapto_Test_Mock_SecurityManager class is an atkSecurityManager mock 
 * object for testing purposes
 * 
 * The most important feature of the Adapto_Test_Mock_SecurityManager is the 
 * ability to influence the result of each function call.
 * 
 * @todo mock every function call. This can't be done nicely until
 * we feature PHP5. For now, we add mock methods on a per-need basis
 *
 * @author ijansch
 * @package adapto
 * @subpackage security
 */
class Adapto_Test_Mock_SecurityManager extends Adapto_SecurityManager
{
    /**
     * Set the entitypriviledges 
     *
     * @var array
     */
    public $m_resultallowed = array(); // defaulted to public

    /**
     * Set which privileges are allowed
     *
     * @param bool $result
     * @param string $entityprivilege
     */
    function setAllowed($result, $entityprivilege = "all")
    {
        $this->m_resultallowed[$entityprivilege] = $result;
    }

    /**
     * Check if the currently logged-in user has a certain privilege on a
     * entity.
     * @param String $entity The full entityname of the entity for which to check
     *                     access privileges. (modulename.entityname notation).
     * @param String $privilege The privilege to check (atkaction).
     * @return boolean True if the user has the privilege, false if not.
     */
    function allowed($entity, $privilege)
    {
        if (isset($this->m_resultallowed["all"]))
            return $this->m_resultallowed["all"];
        if (isset($this->m_resultallowed[$entity . "." . $privilege]))
            return $this->m_resultallowed[$entity . "." . $privilege];
        return parent::allowed($entity, $privilege);
    }
}

?>