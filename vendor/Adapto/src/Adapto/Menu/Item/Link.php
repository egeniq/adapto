<?php

namespace Adapto\Menu\Item;

class Link extends AbstractItem
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