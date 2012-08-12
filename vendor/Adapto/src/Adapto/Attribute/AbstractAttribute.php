<?php

namespace Adapto\Attribute;

use Adapto\EntityDef;
use Adapto\Entity;

class AbstractAttribute
{
    /**
     * @var EntityDef
     */
    protected $_entityDef;
    
    protected $_name = NULL;
    
    public function setEntityDef(EntityDef $entityDef)
    {
        $this->_entityDef = $entityDef;
    }
    
    public function __construct($name)
    {
        $this->_name = $name;
    }
    
    public function getName()
    {
        return $this->_name;
    }
    
    public function isRequired(Entity $entity)
    {
         return true; // todo   
    }
    
    public function validate(Entity $entity)
    {
        return true; // todo
    }
    
    public function getDefaultFieldSuggestion()
    {
        if (isset($this->_fieldSuggestion)) {
            return $this->_fieldSuggestion;
        }
        
        return NULL;
    }
}