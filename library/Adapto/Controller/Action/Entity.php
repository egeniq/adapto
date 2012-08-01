<?php

class Adapto_Controller_Action_Entity extends Adapto_Controller_Action
{
    public function preDispatch()
    {
        $layout = Zend_Layout::getMvcInstance();
        $theme = Adapto_Ui_Theme::getInstance();
        
        $tplPath = 'Adapto/Theme/'.$theme->tplPath('list.phtml');
                
        $layout->setViewScriptPath($tplPath);
        
        parent::preDispatch();
        
        
    }
}