<?php

namespace \Adapto\Field;

abstract class AbstractField implements FieldInterface
{
    protected $_editablePerspectivesOrCallback = 0;
    protected $_visiblePerspectivesOrCallback = 0;
    protected $_searchablePerspectivesOrCallback = 0;
    protected $_sortable = true;
    protected $_labelVisible = true;
    
    
    /**
     * A callback function for summarizing values
     * @var Callable
     */
    protected $_totalCallback = NULL;
    protected $_defaultValue = NULL;
    protected $_section = NULL; 
    
    /**
     * The uidef that this field is attached to.
     * @var \Adapto\UIDef\UIDef
     */
    protected $_UIDef = NULL;
    protected $_widget = NULL;
    protected $_name = NULL;
    
    public function __construct($name)
    {
        $this->_name = $name;
    }
    
    public function isEditable($perspective, $entity)
    {
        if (is_callable($this->_editablePerspectivesOrCallback)) {
            $this->_editablePerspectivesOrCallback($perspective, $entity);
        } 
        return $this->_editablePerspectives & $perspective;
    }
    
    public function setEditable($perspectivesOrCallback)
    {
        $this->_editablePerspectivesOrCallback = $perspectivesOrCallback;
    }
    
    public function isVisible($perspective, $entity)
    {
        if (is_callable($this->visiblePerspectivesOrCallback)) {
            return $this->_visiblePerspectivesOrCallback($perspective, $entity);
        }
        return $this->_visiblePerspectivesOrCallback & $perspective;
    }
    
    public function setVisible($perspectivesOrCallback)
    {
        $this->_visiblePerspectivesOrCallback = $perspectivesOrCallback;
    }
    
    public function isSearchable($perspective, $entity = NULL)
    {
        if (is_callable($this->_searchablePerspectivesOrCallback)) {
            return $this->_searchablePerspectivesOrCallback($perspective, $entity);
        }
        return $this->_searchablePerspectivesOrCallback & $perspective;
    }
    
    public function setSearchable($perspectivesOrCallback)
    {
        $this->_searchablePerspectivesOrCallback = $perspectivesOrCallback;
    }
    
    public function isSortable()
    {
        return $this->_sortable;    
    }
    
    public function setSortable(boolean $sortable)
    {
        $this->_sortable = $sortable;
        return $this;
    }
    
    public function isLabelVisible()
    {
        return $this->_labelVisible;
    }
    
    public function setLabelVisible(boolean $visible)
    {
        $this->_labelVisible = $visible;
        return $this;    
    }
    
    public function getTotalCallback()
    {
        if ($this->_totalCallback == NULL) {
            $this->_totalCallback = function ($sum, $value, $index) {
                return $sum + $value; 
            };
        }
        return $this->_totalCallback;
    }
    
    public function setTotalCallback(/** callable */ $callback)
    {
        $this->_totalCallback = $callback;
        return $this;
    }
    
    public function getDefaultValue()
    {
        return $this->_defaultValue;
    }
    
    public function setDefaultValue($value)
    {
        $this->_defaultValue = $value;
        return $this;    
    }
    
    public function setSection($sectionName)
    {
        $this->_section = $sectionName;
        return $this;
    }
    
    public function setUIDef(\Adapto\UIDef\UIdef $UIDef)
    {
        $this->_UIDef = $UIDef;    
        return $this;
    }

    public function moveBefore($fieldName)
    {
        $this->_UIDef->moveBefore($fieldName);
        return $this;
    }
    
    public function moveAfter($fieldName)
    {
        $this->_UIDef->moveAfter($fieldName);
        return $this;
    }
    
    public function moveToTop()
    {
        $this->_UIDef->moveToTop($this->_name);
        return $this;
    }
    
    public function moveToBottom()
    {
        $this->_UIDef->moveToBottom($this->_name);
        return $this;
    }

    public function serialize()
    {
        // ?
    }
    
    public function setWidget($widget)
    {
        $this->_widget = $widget;
    }
    
    protected function _createWidget()
    {
        return NULL;
    }
    
    public function getWidget()
    {
        if ($this->_widget == NULL) {
            
            // Instantiate the default widget for this field type
            $this->_widget = $this->_createWidget();
        }
    
        return $this->_widget;
    }
}