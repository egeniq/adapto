<?php
  /**
   * This file is part of the Adapto Toolkit.
   * Detailed copyright and licensing information can be found
   * in the doc/COPYRIGHT and doc/LICENSE files which should be
   * included in the distribution.
   *
   * @package adapto
   * @subpackage relations
   *
   * @copyright (c)2000-2004 Ivo Jansch
   * @license http://www.achievo.org/atk/licensing ATK Open Source License
   *


   */

  /**
   * The Adapto_Relation class defines a relation to another entity.
   *
   * @author ijansch
   * @package adapto
   * @subpackage relations
   * @abstract
   *
   */
  class Adapto_Relation extends Adapto_Attribute
  {
    /**
     * @var String Destination entity.
     */
    public $m_destination; // defaulted to public

    /**
     * @var atkEntity Destination instance.
     */
    public $m_destInstance=""; // defaulted to public

    /**
     * @var String Filter for destination records.
     */
    public $m_destinationFilter=""; // defaulted to public

    /**
     * Descriptor template for destination entity.
     * @var String
     */
    public $m_descTemplate = NULL; // defaulted to public

    /**
     * Descriptor handler.
     * @var Object
     */
    public $m_descHandler = NULL; // defaulted to public

    /**
     * Constructor
     * @param String $name The name of the relation.
     * @param String $destination The destination entity (in module.name notation)
     * @param int $flags Flags for the relation
     */
    public function __construct($name, $destination, $flags=0)
    {
      parent::__construct($name, $flags);
      $this->m_destination = $destination;

    }

    /**
     * Returns the destination filter.
     * @return String The destination filter.
     */
    function getDestinationFilter()
    {
      return $this->m_destinationFilter;
    }

    /**
     * Sets the destination filter.
     * @param String $filter The destination filter.
     */
    function setDestinationFilter($filter)
    {
      $this->m_destinationFilter = $this->_cleanupDestinationFilter($filter);
    }

    /**
     * Remove redundant (more than 1 subsequently) spaces from the filter string.
     *
     * This prevents the filter from rapidly becoming too long to be passed in the URL if
     * enters are used in code to make the filter readable.
     *
     * @param string $filter
     * @return string
     */
    function _cleanupDestinationFilter($filter)
    {
      $result = '';
      $filter_length = strlen($filter);
      $quotes = array("'",'"','`');
      $quoteStack = array();
      $lastChar = '';

      for ($i = 0; $i < $filter_length; $i++)
      {
        $currentChar = $filter[$i];

        if(in_array($currentChar,$quotes))
        {
          if(sizeof($quoteStack) > 0 && $currentChar == $quoteStack[sizeof($quoteStack) - 1])
          {
            array_pop($quoteStack);
          }
          else
          {
            array_push($quoteStack,$currentChar);
          }
        }

        // not between quotes
        if(!($currentChar === ' ' && $lastChar === ' ' && sizeof($quoteStack) == 0))
        {
          if($currentChar != "\n") $result .= $currentChar;
        }
        $lastChar = $currentChar;
      }
      return $result;
    }
    /**
     * Adds a filter value to the destination filter.
     * @param String $filter Filter to be added to the destination filter.
     */
    function addDestinationFilter($filter)
    {
      $filter = $this->_cleanupDestinationFilter($filter);
      if($this->m_destinationFilter != "")
        $this->m_destinationFilter = "({$this->m_destinationFilter}) AND ({$filter})";
      else
        $this->m_destinationFilter = $filter;
      return $this;
    }

    /**
     * Get descriptor handler.
     * @return Object descriptor handler
     */
    function &getDescriptorHandler()
    {
      return $this->m_descHandler;
    }

    /**
     * Set descriptor handler.
     * @param Object $handler The descriptor handler.
     */
    function setDescriptorHandler(&$handler)
    {
      $this->m_descHandler = &$handler;
    }

    /**
     * Returns the descriptor template for the destination entity.
     * @return String The descriptor Template
     */
    function getDescriptorTemplate()
    {
      return $this->m_descTemplate;
    }

    /**
     * Sets the descriptor template for the destination entity.
     * @param String $template The descriptor template.
     */
    function setDescriptorTemplate($template)
    {
      $this->m_descTemplate = $template;
    }

    /**
     * Descriptor handler. Forwards description handler calls
     * to the real description handler.
     *
     * @param array $record The record
     * @param atkEntity $entity The atkentity object
     * @return String with the descriptor
     */
    function descriptor($record, &$entity)
    {
      $method = $this->m_name."_descriptor";
      if (method_exists($this->m_descHandler, $method))
        return $this->m_descHandler->$method($record, $entity);
      else return $this->m_descHandler->descriptor($record, $entity);
    }

    /**
     * Create the instance of the destination.
     *
     * If succesful, the instance is stored in the m_destInstance member variable.
     *
     * @return boolean true if succesful, false if something went wrong.
     */
    function createDestination()
    {
      if (!is_object($this->m_destInstance))
      {
        $cache_id = $this->m_owner.".".$this->m_name;
        $this->m_destInstance = &getEntity($this->m_destination, true, $cache_id);

        // Validate if destination was created succesfully
        if (!is_object($this->m_destInstance))
        {
          atkerror("Relation with unknown entitytype '".$this->m_destination."' (in entity '".$this->m_owner."')");
          $this->m_destInstance = NULL;
          return false;
        }

        if ($this->hasFlag(AF_NO_FILTER))
          $this->m_destInstance->m_flags |= EF_NO_FILTER;

        foreach (array_keys($this->m_destInstance->m_attribList) as $key)
        {
          $attribute = &$this->m_destInstance->m_attribList[$key];

          if (is_subclass_of($attribute, "atkrelation") && is_object($this->m_ownerInstance) && $attribute->m_destination == $this->m_ownerInstance->atkEntityType())
          {
            $attribute->m_destInstance = &$this->m_ownerInstance;

            if (count($attribute->m_tabs) == 1 && $attribute->m_tabs[0] == "default")
            {
              $attribute->setTabs($this->m_tabs);
            }
          }
        }

        if (!empty($this->m_descHandler))
          $this->m_destInstance->setDescriptorHandler($this);

        if (!empty($this->m_descTemplate))
          $this->m_destInstance->setDescriptorTemplate($this->m_descTemplate);
      }
      return true;
    }

    /**
     * Return a displayable string for a record.
     * @param array $record The record that contains the information to display.
     * @return String a displayable string for this value.
     */
    function display($record)
    {
      return $record[$this->fieldName()];
    }

    /**
     * Validation method. Empty implementation. Derived classes may override
     * this function.
     * @abstract
     *
     * @param array $record The record that holds the value for this
     *                      attribute. If an error occurs, the error will
     *                      be stored in the 'atkerror' field of the record.
     * @param String $mode The mode for which should be validated ("add" or
     *                     "update")
     */
    function validate(&$record, $mode)
    {
    }

    /**
     * Check if the relation is empty
     * @param array $record The record to check
     * @return boolean true if a destination record is present. False if not.
     */
    function isEmpty($record)
    {
      if ($this->createDestination() && isset($record[$this->fieldName()][$this->m_destInstance->primaryKeyField()]))
      {
        return empty($record[$this->fieldName()][$this->m_destInstance->primaryKeyField()]);
      }
      else if ($this->createDestination() && isset($record[$this->fieldName()]))
      {
        return empty($record[$this->fieldName()]);
      }
      return true; // always empty if error.
    }

    /**
     * Retrieve the searchmodes supported by the relation.
     * @return array A list of supported searchmodes.
     */
    function getSearchModes()
    {
      // exact match and substring search should be supported by any database.
      // (the LIKE function is ANSI standard SQL, and both substring and wildcard
      // searches can be implemented using LIKE)
      // Possible values
      //"regexp","exact","substring", "wildcard","greaterthan","greaterthanequal","lessthan","lessthanequal"
      return array("exact");
    }

    /**
     * Get the searchmode for nested/child attributes.
     *
     * @param string|array $searchmode searchmode
     * @param string       $childname  the child attribute's name
     * @return string|array the child searchmode
     */
    protected function getChildSearchMode($searchmode, $childname)
    {
      if (is_array($searchmode) && isset($searchmode[$childname]))
        return $searchmode[$childname];
      return $searchmode;
    }

    /**
     * Since most relations do not store anything in a field, the default
     * fieldtype for relations is "". Exceptions (like the many2oone relation,
     * which stores a foreign key) can implement their own dbFieldType().
     * @abstract
     * @return String
     */
    function dbFieldType()
    {
      return "";
    }

    /**
     * Returns the condition (SQL) that should be used when we want to join a relation's
     * owner entity with the parent entity.
     *
     * @param atkQuery $query The query object
     * @param string $tablename The tablename
     * @param string $fieldalias
     * @return String SQL string for joining the owner with the destination.
     *                Defaults to false.
     */
    function getJoinCondition(&$query, $tablename="",$fieldalias="")
    {
      return false;
    }

    /**
     * Returns an instance of the entity that the relation points to.
     * @return atkEntity The entity that this relation points to, or
     *                 NULL if the destination is not valid.
     */
    function &getDestination()
    {
      if ($this->createDestination())
      {
        return $this->m_destInstance;
      }
      return NULL;
    }

    /**
     * Attempts to get a translated label which can be used when composing an "add" link
     *
     * @return String Localised "add" label
     */
    function getAddLabel()
    {
      // Try to get a translation for link_fieldname_add (or if not found, a translation for link_destination_add)
      $keys = array("link_" . $this->fieldName() . "_add", "link_" . getEntityType($this->m_destination) . "_add");
      $label = atktext($keys, getEntityModule($this->m_destination), "", "", "", true);

      // If translation not found, then use a concatenation of translations of the destination and the word "add" as default
      if ($label=="")
        $label = atktext(getEntityType($this->m_destination), getEntityModule($this->m_destination)) . " " . strtolower(atktext("add", "atk"));

      // Return the translation
      return $label;
    }

    /**
     * Parses the destination filter
     *
     * @param string $destFilter filter to parse
     * @param array $record the current record
     * @return $filter string filter.
     */
    function parseFilter($destFilter,$record)
    {
      if($destFilter!="")
      {
        
        $parser = new Adapto_StringParser($destFilter);
        return $parser->parse($record);
      }
      return "";
    }
  }
?>
