<?php

namespace \Adapto\Field;

interface FieldSetterInterface
{   
    public function setEditable($perspectivesOrCallback);
        
    public function setVisible($perspectivesOrCallback);
        
    public function setSearchable($perspectivesOrCallback);
    
    public function setSortable(boolean $sortable);
    
    public function setLabelVisible(boolean $visible);
    
    public function setTotalCallback(/** callable */ $callback);
    
    public function setDefaultValue($value);
    
    public function setSection($sectionName);
    
    public function moveBefore($fieldName);    
    public function moveAfter($fieldName);
    public function moveToTop();
    public function moveToBottom();
    
    /**
     * Set the widget to use for this field.
     * @param \Adapto\Widget\WidgetInterface|classname $widget
     */
    public function setWidget($widget);
    
}