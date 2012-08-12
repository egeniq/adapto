<?php

namespace \Adapto\Field;

interface FieldInterface extends FieldSetterInterface
{   
    public function isEditable($perspective, $entity);
        
    public function isVisible($perspective, $entity);
        
    public function isSearchable($perspective, $entity = NULL);    
    
    public function isSortable();    
    
    public function isLabelVisible();
    
    public function getTotalCallback();
        
    public function getDefaultValue();
    
    public function getSection();
    
    /**
     * Get the widget that represents this field.
     * @return \Adapto\Widget\WidgetInterface
     */
    public function getWidget();
    
}