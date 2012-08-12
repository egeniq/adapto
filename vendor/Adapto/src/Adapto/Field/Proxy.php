<?php

namespace \Adapto\Field;

class Proxy implements FieldSetterInterface
{
    protected $_fields = array();
    
    public function __constructs($fields=array())
    {
        $this->_fields = $fields;
        
    }
    
    public function __call($method, $arguments)
    {
        foreach ($this->_fields as $field) {
            $field->$method($arguments);
        }
    }
    
}