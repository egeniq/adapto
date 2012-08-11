<?php

namespace \Adapto\Widget;

interface WidgetInterface
{

    public function renderView();

    public function fetchEditValue($request);
    	
    public function renderEdit();
    
    public function fetchSearchValue($request);

    public function renderSearch();
    
    public function dispatchAction($action, \Zend\Http\Request $request);
}