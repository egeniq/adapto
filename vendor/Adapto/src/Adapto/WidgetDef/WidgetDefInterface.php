<?php

namespace \Adapto\WidgetDef;

interface WidgetDefInterface
{
    public function bind(\Adapto\Binding\BaseBinding $binding);
    
    public function setReadOnly(boolean $readOnly);
    public function setReadOnlyAdd(boolean $readOnly);
    public function setReadOnlyEdit(boolean $readOnly);

    public function setSection($name);

    public function moveBefore($name);
    public function moveAfter($name);
    public function moveToTop();
    public function moveToBottom();
    
    public function serialize();
}