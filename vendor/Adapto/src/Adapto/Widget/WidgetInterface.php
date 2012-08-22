<?php

namespace \Adapto\Widget;

interface WidgetInterface
{
    public function __construct($id);
    
    public function setUi(\Adapto\Ui\Ui $ui);
    
    public function getUi();
    
    public function getId();
    
    public function renderView();

    public function fetchEditValue(\Zend\Http\Request $request);
    	
    public function renderEdit();
    
    public function fetchSearchValue(\Zend\Http\Request $request);

    public function renderSearch();
    
    public function dispatchAction($action, \Zend\Http\Request $request);
}