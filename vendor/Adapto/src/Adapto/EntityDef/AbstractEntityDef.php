<?php

namespace \Adapto\EntityDef;

use Adapto\Attribute\AbstractAttribute;


abstract class AbstractEntityDef 
{
    protected $_attrributes = array();
    
    public function add(AbstractAttribute $attribute)
    {
        $this->_attributes[$attribute->getName()] = $attribute;
        return $this;
    }
    
    public function getAttributes() 
    {
        return $this->_attributes;    
    }
    
    public function get($name)
    {
        if (isset($this->_attributes[$name])) {
            return $this->_attributes[$name];
        }
    }
    
    public function remove($name)
    {
        if (isset($this->_attributes[$name])) {
            unset($this->_attributes[$name]);
        }
    }
}