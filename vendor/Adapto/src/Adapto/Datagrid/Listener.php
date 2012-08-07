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
 * @copyright (c) 2000-2007 Ibuildings.nl BV
 *
 * @license http://www.achievo.org/atk/licensing ATK Open Source License
 */

/**
 * The data grid listener can be implemented and registered for a data grid
 * to listen for data grid events. 
 *
 * @author petercv
 * @package adapto
 * @subpackage datagrid
 */
interface Adapto_Datagrid_Listener
{

    /**
     * Will be called for each data grid event.
     *
     * @param atkDGEvent $event event
     */

    public function notify(atkDGEvent $event);
}
