<?php

namespace \Adapto\Widget;

interface WidgetInterface extends \Adapto\WidgetDef\WidgetDefInterface
{
    public function fetchValue(\Zend\Http\Request $request);
    public function render();
    public function dispatchAction($action, \Zend\Http\Request $request);
}