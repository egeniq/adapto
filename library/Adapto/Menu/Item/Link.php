<?php

class Adapto_Menu_Item_Link extends Adapto_Menu_Item_Abstract
{
    protected $_link;
    protected $_title;
    
    public function __construct($link, $title)
    {
        $this->_link = $link;
        $this->_title = $title;
    }
    
    public function getTitle()
    {
        return $this->_link;
    }
    
    public function getLink()
    {
        return $this->_link;    
    }

}