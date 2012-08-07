<?php

namespace Adapto\Menu\Item;

class Entity extends AbstractItem
{
    protected $_entityName;
    protected $_moduleName;
    protected $_action;

    public function __construct($moduleName, $entityName, $action = "list")
    {
        $this->_entityName = $entityName;
        $this->_moduleName = $moduleName;
        $this->_action = $action;
    }
    
    public function getTitle()
    {
        // Todo: use i18n and magic text determination for a proper menu name.
        return $this->_entityName;
    }

    public function getLink()
    {
        return "/adapto/" . $this->_moduleName . "/" . $this->_entityName . "/" . $this->_action;
    }

}