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
 * @copyright (c)2006 Ibuildings.nl BV
 * @license http://www.achievo.org/atk/licensing ATK Open Source License
 *

 */

/**
 * The Adapto_Attribute_RowCounter can be added to an entity to have a column in listviews
 * that sequentially numbers records.
 *
 * The attribute evolved from a discussion at http://achievo.org/forum/viewtopic.php?t=478
 * and was added to ATK based on suggestion and documentation by Jorge Garifuna.
 *
 * @author Przemek Piotrowski <przemek.piotrowski@nic.com.pl>
 * @author ijansch
 *
 * @package adapto
 * @subpackage attributes
 *
 */

class Adapto_Attribute_RowCounter extends Adapto_DummyAttribute
{
    /**
     * Constructor
     * @param String $name Name of the attribute
     * @param int $flags Flags for this attribute
     */

    public function __construct($name, $flags = 0)
    {
        parent::__construct($name, '', $flags | AF_HIDE_VIEW | AF_HIDE_EDIT | AF_HIDE_ADD);
    }

    /**
     * Returns a number corresponding to the row count per record.
     * @return int Counter, starting at 1
     */
    function display()
    {
        static $s_counter = 0;
        $entity = &$this->m_ownerInstance;
        return $entity->m_postvars["atkstartat"] + (++$s_counter);
    }
}

?>