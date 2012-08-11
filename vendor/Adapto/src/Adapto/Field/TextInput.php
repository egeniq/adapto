<?php

namespace \Adapto\Widget;

class TextInput extends AbstractWidget
{
    public function fetchValue(\Zend\Http\Request $request)
    {
        
    }
    
    public function render()
    {
        // How do we determine our name? From the field binding?
        return '<input type="text" name="" id="" />';'
    }
    

}