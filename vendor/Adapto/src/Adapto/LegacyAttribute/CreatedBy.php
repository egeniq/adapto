<?php

/**
 * This file is part of the Adapto Toolkit.
 * Detailed copyright and licensing information can be found
 * in the doc/COPYRIGHT and doc/LICENSE files which should be
 * included in the distribution.
 *
 * @package adapto
 * @subpackage attributes
 *
 * @copyright (c)2006 Ivo Jansch
 * @license http://www.achievo.org/atk/licensing ATK Open Source License
 *

 */

/**
 * @internal baseclass include
 */
useattrib("atkupdatedbyattribute");

/**
 * This attribute can be used to automatically store the user that inserted
 * a record.
 *
 * @author Yury Golovnya <ygolovnya@ccenter.utel.com.ua>
 * @package adapto
 * @subpackage attributes
 *
 */
class Adapto_Attribute_CreatedBy extends Adapto_UpdatedByAttribute
{
    /**
     * Constructor.
     *
     * @param String $name Name of the field
     * @param int $flags Flags for this attribute.
     * @return Adapto_Attribute_CreatedBy
     */

    public function __construct($name, $flags = 0)
    {
        parent::__construct($name, $flags);
    }

    /**
     * needsUpdate always returns false for this attribute.
     * @return false
     */
    function needsUpdate()
    {
        return false;
    }
}
?>