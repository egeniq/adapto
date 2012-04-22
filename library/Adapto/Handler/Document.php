<?php
/**
 * Adapto_Handler_Document class file
 *
 * @package adapto
 * @subpackage handlers
 *
 * @author guido <guido@ibuildings.nl>
 *
 * @copyright (c) 2005 Ibuildings.nl BV
 * @license http://www.achievo.org/atk/licensing/ ATK open source license
 *

 */

/**
 * Handler class for the document action
 *
 * @author guido <guido@ibuildings.nl>
 * @package adapto
 * @subpackage handlers
 */
class Adapto_Handler_Document extends Adapto_ActionHandler
{

    /**
     * The action handler.
     */
    function action_document()
    {
        // Add "Action document" to debug log to indicate this function is entered
        atkdebug("Action document");

        // Load and instantiate the documentwriter

        $openDocumentWriter = &atkDocumentWriter::getInstance("opendocument");

        // ATKSelector must be available to perform this action
        if ($this->m_postvars["atkselector"] == "") {
            atkerror("Selector parameter not available.");
            return false;
        }

        // ATKDocTpl must be available to perform this action
        if (!isset($this->m_postvars["atkdoctpl"])) {
            atkerror("atkdoctpl parameter not available.");
            return false;
        }

        $tpl_file = $this->getFilenameForTemplate($this->m_postvars["atkdoctpl"]);

        // Check for invalid characters in filename, modulename and entityname in order to prevent hacking
        if (ereg("[<>\\/|;]", $module . $entity . $this->m_postvars["atkdoctpl"]) !== false) {
            atkerror("Invalid filename given.");
            return false;
        }

        // Check if the file exists
        if (!is_file($tpl_file)) {
            atkerror("Given file does not exist.");
            return false;
        }

        // Assign the record variables to the OpenOffice.org DocumentWriter
        if (method_exists($this->m_entity, "assignDocumentVars"))
            $this->m_entity->assignDocumentVars($openDocumentWriter, $this->m_postvars["atkselector"]);
        else
            $this->assignDocumentVars($openDocumentWriter, $this->m_postvars["atkselector"]);

        // Send the document to the browser
        if (!$openDocumentWriter->display($tpl_file, $this->m_postvars["atkdoctpl"]))
            return false;

        // Halt further execution to prevent atk rendering it's interface causing to corrupt the opendocument file
        exit;
    }

    /**
     * Default document assignment function (assigns the given record and
     * the generic vars)
     *
     * @param atkDocumentWriter $documentWriter DocumentWriter to which the variables should be assigned
     * @param String $selector String containing the selector used to get the document from the database
     */
    function assignDocumentVars(&$documentWriter, $selector)
    {
        // Load the selected record from the database
        $record = $this->m_entity->selectDb($selector, "", "", $this->m_viewExcludes, "", "document");

        // Assign the record to the documentWriter
        $documentWriter->assignDocumentSingleRecord($this->m_entity, $record[0]);

        // Also assign the generic (date) vars tot the documentWriter
        $documentWriter->assignDocumentGenericVars();
    }

    /**
     * Compose the filename to be used (doctemplatedir/module/entity/<docmentfilename)
     *
     * @param String $template
     * @return String
     */
    function getFilenameForTemplate($template)
    {
        $basepath = Adapto_Config::getGlobal("doctemplatedir", "doctemplates/");
        $module = $this->m_entity->m_module;
        $entity = $this->m_entity->m_type;

        return $basepath . $module . "/" . $entity . "/" . $template;
    }
}
?>