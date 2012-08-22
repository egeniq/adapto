<?php

namespace \Adapto\Perspective;

use \Adapto\EntityDef;
use \Adapto\Field;

class Perspective
{
    const PERSPECTIVE_NONE =   0;
    const PERSPECTIVE_LIST =   1;
    const PERSPECTIVE_EDIT =   2;
    const PERSPECTIVE_ADD  =   4;
    const PERSPECTIVE_SEARCH = 8;
    const PERSPECTIVE_VIEW =  16;
    const PERSPECTIVE_ALL  =  31; 
    
    /**
     * 
     * @var AbstractEntityDef
     */
    protected $_entityDef = NULL;
    protected $_fields = array();
    
    public function __construct(AbstractEntityDef $entityDef)
    {
        $this->_entityDef = $entityDef; 
        
        $this->_initFromEntity();
    }
    
    protected function _initFromEntity()
    {
        $policy = new \Adapto\Perspective\Policy($this); // todo, we need to inject this instead of calling constructor
        
        foreach ($this->_entityDef->getAttributes() as $attribute)
        {
           $this->add($policy->createFieldForAttribute($attribute));
           
           $policy->configurePerspective($attribute)       ;     
        }
    }
    
    public function getEntityDef()
    {
        return $this->_entityDef;
    }
    
    public function add(AbstractField $field)
    {
        $this->_fields[$field->getName()] = $field;
    }
    
    /**
     * @param array|field list $fields
     * @return FieldSetterInterface
     */
    public function get($fieldnames)
    {
        if (!is_array($fieldnames)) {
            $fieldnames = func_get_args();
        }
        
        $fields = array();
        foreach ($fieldnames as $name) {
            $fields[] = $this->_fields[$name];
        }
        
        return new Proxy($fields);
    }
    
    public function remove($name)
    {
        unset ($this->_fields[$name]);
    }
    
    public function fetchValues($request, $entity)
    {
        
    }
    
    public function moveBefore($name, $beforeName)
    {
        
    }
    
    public function moveAfter($name, $afterName)
    {
        
    }
    
    public function moveToTop($name)
    {
        
    }
    
    public function moveToBottom($name)
    {
        
    }
    
    public function dispatchAction($action, $request)
    {
        
    }
    
}