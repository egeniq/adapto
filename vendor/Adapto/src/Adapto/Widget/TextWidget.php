<?php

namespace \Adapto\Widget;

class TextWidget extends AbstractWidget
{
    public function renderView($value)
    {
        return htmlentities($value, ENT_COMPAT, 'utf-8');
    }
    
    public function fetchEditValue(\Zend\Http\Request $request)
    {
        return $request->get($this->getId());
    }
     
    public function renderEdit($value = NULL)
    {
        return $this->getUi()->render("widgetText.phtml", array("value" => $value));
    }
    
    public function fetchSearchValue(\Zend\Http\Request $request)
    {
        return $request->get($this->getId());
    }
    
    public function renderSearch($value = NULL)
    {
        return $this->getUi()->render("widget/widgetText.phtml", array("value" => $value));
    }
    

}