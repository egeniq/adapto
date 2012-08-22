<?php

namespace Adapto\Menu\Item;

class Controller extends AbstractItem
{
    protected $_controllerName;
    protected $_moduleName;
    protected $_action;

    public function __construct($moduleName, $controllerName, $action = "list")
    {
        $this->_controllerName = $controllerName;
        $this->_moduleName = $moduleName;
        $this->_action = $action;
    }
    
    public function getTitle()
    {
        // Todo: use i18n and magic text determination for a proper menu name.
        return $this->_controllerName;
    }

    public function getLink()
    {
        // todo: use url helper to create this.
        return '/'.$this->_moduleName . "/" . $this->_controllerName . "/" . $this->_action;
    }

}