<?php
  /**
   * Adapto_Document_Writer class file
   *
   * @package adapto
   * @subpackage document
   *
   * @author guido <guido@ibuildings.nl>
   *
   * @copyright (c) 2005 Ibuildings.nl BV
   * @license http://www.achievo.org/atk/licensing/ ATK open source license
   *


   */

  /**
   * General DocumentWriter framework class. Should be extended to support specific file formats.
   *
   * @author guido <guido@ibuildings.nl>
   * @package adapto
   * @subpackage document
   */
  class Adapto_Document_Writer
  {

    /**
     * Template vars array
     *
     * @access protected
     * @var array
     */
    public $m_tpl_vars = array(); // defaulted to public

    public $m_taglist = ""; // defaulted to public

    /**
     * Adapto_Document_Writer Constructor.
     *
     * Dont use this, use Adapto_Document_Writer::getInstance($format) instead to get a singleton instance for any format used
     */
    public function __construct()
    {
    }

    /**
     * Assigns values to template variables
     *
     * @param string|array $tpl_var Template variable name or array of variable name/value pairs
     * @param mixed $value Value to assign (only used if $tpl_var is a string)
     */
    function assign($tpl_var, $value = null)
    {
      if (is_array($tpl_var))
      {
        foreach ($tpl_var as $key => $val)
        {
          if ($key != '')
          {
            $this->m_tpl_vars[$key] = $val;
          }
        }
      }
      else
      {
        if ($tpl_var != '')
          $this->m_tpl_vars[$tpl_var] = $value;
      }
    }

    /**
     * Returns labels for all attributes of an entity
     *
     * @param atkEntity $entity Entity for which the labels should be retrieved
     * @return Array Associative array containing attributename=>label pairs
     */
    function getRecordLabels(&$entity)
    {
      // Initialize the result array
      $result = array();

      // Loop through the attributes in order to assign them all to the documentwriter
      foreach(array_keys($entity->m_attribList) as $key)
      {

        // Get a reference to the attribute
        $p_attrib = &$entity->m_attribList[$key];

        // Get the Label of the attribute (can be suppressed with AF_NOLABEL or AF_BLANKLABEL)
        if ($p_attrib->hasFlag(AF_NOLABEL) || $p_attrib->hasFlag(AF_BLANKLABEL))
          $result[$key] = "";
        else
          $result[$key] = $p_attrib->label(array());
      }

      // Return the array containing attributename=>label pairs
      return $result;
    }

    /**
     * Returns labels for all attributes of an entity
     *
     * @param atkEntity $entity Entity for which the displayvalues should be retrieved
     * @param Array $record Record for which the display values should be determined
     * @return Array Associative array containing attributename=>displayvalue pairs
     */
    function getRecordDisplayValues(&$entity, $record)
    {
      // Initialize the result array
      $result = array();

      // Loop through the attributes in order to assign them all to the documentwriter
      foreach(array_keys($entity->m_attribList) as $key)
      {

        // Get a reference to the attribute
        $p_attrib = &$entity->m_attribList[$key];

        // Get the display value by calling <attribute>_display().
        // An <attributename>_display function may be provided in a derived
        // class to display an attribute. If it exists we will use that method
        // else we will just use the attribute's display method.
        $funcname = $p_attrib->m_name."_display";
        if (method_exists($entity, $funcname))
          $result[$key] = $entity->$funcname($record, "plain");
        else
          $result[$key] = $p_attrib->display($record, "plain");
      }

      // Return the array containing attributename=>displayvalue pairs
      return $result;
    }

    /**
     * Assigns the labels for all attributes of an entity to the documentWriter
     *
     * @param atkEntity $entity Entity for which the labels should be retrieved
     * @param String $prefix Prefix to be used when assigning the variables (used to avoid conflicting names)
     */
    function _assignLabels(&$entity, $prefix)
    {
      // Get all labels for the given entity
      $labels = $this->getRecordLabels($entity);

      // Assign all labels to the documentwriter
      foreach($labels as $key => $label)
        $this->Assign($prefix . $key . "_label", $label);
    }

    /**
     * Enter description here...
     *
     * @param atkEntity $entity Entity to be used when displaying the records
     * @param Array $records Array of records that should be assigned to the documentwriter
     * @param String $prefix Prefix to be used when assigning the variables (used to avoid conflicting names)
     */
    function assignDocumentMultiRecord(&$entity, $records, $prefix = "")
    {
      // Assign all labels to the documentwriter
      $this->_assignLabels($entity, $prefix);

      // Initialize the displayvalues array
      $displayvalues = array();

      // Loop through all records and add the displayvalues to the array
      foreach($records as $record)
        $displayvalues[] = $this->getRecordDisplayValues($entity, $record);

      // Assign the displayvalues array to the documentwriter
      $this->Assign($prefix . $entity->m_type, $displayvalues);

      // Register the taglist
      $this->m_taglist .= sprintf("%s codes (all prefixed by %s%s.)\n", $entity->text($entity->m_type), $prefix, $entity->m_type);
      foreach(array_keys($entity->m_attribList) as $key)
        $this->m_taglist .= "[$prefix{$entity->m_type}.$key]\n";
      $this->m_taglist .= "You can use these tags in a table. More info: http://www.achievo.org/wiki/AtkDocumentWriter\n";
      $this->m_taglist .= "\n";
    }

    /**
     * Enter description here...
     *
     * @param atkEntity $entity Entity to be used when displaying the record
     * @param Array $record Record that should be assigned to the documentwriter
     * @param String $prefix Prefix to be used when assigning the variables (used to avoid conflicting names)
     */
    function assignDocumentSingleRecord(&$entity, $record, $prefix = "")
    {
      // Assign all labels to the documentwriter
      $this->_assignLabels($entity, $prefix);

      // Get all display values from the given record
      $displayvalues = $this->getRecordDisplayValues($entity, $record);

      // Loop through all display values and assign them to the documentwriter
      foreach($displayvalues as $key => $displayvalue)
        $this->Assign($prefix . $key, $displayvalue);

      // Register the taglist
      $this->m_taglist .= sprintf("%s codes%s\n", $entity->text($entity->m_type), empty($prefix)?"":" (all prefixed by $prefix)");
      foreach(array_keys($entity->m_attribList) as $key)
        $this->m_taglist .= "[$prefix$key]\n";
      $this->m_taglist .= "\n";
    }

    /**
     * Assigns data to the document based on only an entityname and a selector
     *
     * @param string $entityname Name (module.entity) for the entity
     * @param string $selector Selector containing a SQL expression
     * @param string $prefix Prefix to be used when assigning variables to the document
     * @return boolean True if a record is found and assigned, false if not
     */
    function assignRecordByEntityAndSelector($entityname, $selector, $prefix = "")
    {
      // Do not continue and return false if no selector given
      if ($selector == "")
        return false;

      // Assign the quotation owner to the document
      $entity = &atkGetEntity($entityname);

      // Get the record from the database
      $records = $entity->selectDb($selector, "", "", "", "", "view");

      // Do not continue and return false if no records were found
      if (count($records) == 0)
        return false;

      // Assign the record to the document.
      $this->assignDocumentSingleRecord($entity, $records[0], $prefix);

      // Return succesfully
      return true;
    }

    /**
     * Assigns commonly used variables to a documentWriter
     *
     * @param string $prefix Prefix to be used when assigning the variables (used to avoid conflicting names)
     */
    function assignDocumentGenericVars($prefix = "")
    {
      // Get the current date and a reference to an atkDateAttribute in order to format the current date
      $date = adodb_getdate();
      $dateattribute = new Adapto_DateAttribute("dummy");

      // Assign the date in short and long format as [shortdate] and [longdate]
      $this->Assign($prefix . "shortdate", $dateattribute->formatDate($date, "d-m-Y", 0));
      $this->Assign($prefix . "longdate", $dateattribute->formatDate($date, "j F Y", 0));

      // Assign the taglist
      $this->Assign($prefix . "taglist", $this->m_taglist);
    }

    /**
     * Get a singleton instance of the Adapto_Document_Writer class for any format used
     *
     * @param string $format Document format to be used (defaults to opendocument).
     * @return Adapto_Document_Writer Returns singleton instance of Adapto_Document_Writer descendant (depends on given format)
     */
    function &getInstance($format = "opendocument")
    {
      static $s_oo_instance = NULL;
      static $s_docx_instance = NULL;

      if ($format == "opendocument") 
      {
        if ($s_oo_instance == NULL)
        {
          Adapto_Util_Debugger::debug("Creating a new Adapto_OpenDocumentWriter instance");
          
          $s_oo_instance = new Adapto_OpenDocumentWriter();
        }
        
        return $s_oo_instance;
      }
      else if ($format == "docx") 
      {
        if ($s_docx_instance == NULL)
        {
          Adapto_Util_Debugger::debug("Creating a new Adapto_DocxWriter instance");
          
          $s_docx_instance = new Adapto_DocxWriter();
        }
        
        return $s_docx_instance;
      }
      else 
      {
        Adapto_Util_Debugger::debug(sprintf("Failed to create Adapto_Document_Writer instance (unknown format: %s)", $format));
      }
    }
  }

?>