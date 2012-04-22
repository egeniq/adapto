<?php
/**
 * This file is part of the Adapto Toolkit.
 * Detailed copyright and licensing information can be found
 * in the doc/COPYRIGHT and doc/LICENSE files which should be
 * included in the distribution.
 *
 * @package adapto
 * @subpackage handlers
 *
 * @copyright (c) 2000-2009 Ibuildings.nl BV
 * 
 * @license http://www.achievo.org/atk/licensing ATK Open Source License
 *

 */

/**
 */
class Adapto_Handler_MultiUpdatehandler extends Adapto_ActionHandler
{

    /**
     * The action handler method.
     */
    function action_multiupdate()
    {
        $data = $this->getEntity()->m_postvars['atkdatagriddata'];
        foreach ($data as $entry) {
            $entry = $this->getEntity()->updateRecord($entry, $this->getEntity()->m_editableListAttributes);
            $record = $this->getEntity()->select($entry['atkprimkey'])->mode('edit')->firstRow();
            $record = array_merge($record, $entry);
            $this->getEntity()->updateDb($record, true, '', $this->getEntity()->m_editableListAttributes);
        }

        $this->getEntity()->getDb()->commit();
        die('true');
    }
}
