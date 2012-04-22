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
 * @copyright (c)2000-2004 Ivo Jansch
 * @license http://www.achievo.org/atk/licensing ATK Open Source License
 *

 */

/**
 * Handler class for the exporting a record to an XML file.
 *
 * @author ijansch
 * @package adapto
 * @subpackage handlers
 *
 */
class Adapto_Handler_Xml extends Adapto_ActionHandler
{

    /**
     * The action handler method. Creates an xml document and outputs it to the browser.
     */
    function action_xml()
    {
        $recordset = $this->m_entity->selectDb($this->m_postvars['atkselector'], "", "", "", "", "xml");

        $output = &atkOutput::getInstance();

        $document = '<?xml version="1.0"?>' . "\n";

        for ($i = 0, $_i = count($recordset); $i < $_i; $i++) {
            $document .= $this->invoke("xml", $recordset[$i]) . "\n";
        }
        $output->output($document);
    }

    /**
     * Convert a record to an XML fragment.
     * @param array $record The record to convert to xml.
     * @return String XML document.
     * @todo This handler can only handle 'simple' key/value attributes
     *       like atkAttribute. Relation support should be added.
     *
     */
    function xml($record)
    {
        $entity = &$this->m_entity;
        $xml = "<" . $entity->m_type . " ";

        $attrs = array();
        foreach (array_keys($entity->m_attribList) as $attribname) {
            $p_attrib = &$entity->m_attribList[$attribname];
            if (!$p_attrib->isEmpty($record)) {
                $attrs[] = $attribname . '="' . $p_attrib->display($record, "xml") . '"';
            }
        }
        if (count($attrs)) {
            $xml .= implode(" ", $attrs);
        }

        $xml .= '/>';

        if (isset($entity->m_postvars['tohtml']) && $entity->m_postvars['tohtml'] == 1) {
            return htmlspecialchars($xml) . '<br>';
        } else {
            return $xml;
        }
    }
}

?>