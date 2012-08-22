<?php

namespace \Adapto\Widget;

class AbstractWidget implements WidgetInterface
{
    protected $_id;
    
    /**
     * The UI context (for use of themes, tpls etc)
     * @var \Adapto\Ui\Ui
     */
    protected $_ui = NULL;
    
    public function __construct($id)
    {
        $this->_id = $id;
    }
    
    public function getId()
    {
        return $this->_id;
    }
    
    public function setUi(\Adapto\UI\Ui $ui)
    {
        $this->_ui = $ui;
    }
    
    public function getUi()
    {
        return $this->_ui;
    }
    
    public function dispatchAction($action, \Zend\Http\Request $request)
    {
        // The abstract widget doesn't have any ajax requests to handle
    }
}