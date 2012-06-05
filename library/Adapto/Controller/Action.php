<?php

class Adapto_Controller_Action extends Zend_Controller_Action
{
    public function preDispatch()
    {
        $layout = Zend_Layout::getMvcInstance();
        if ($layout->getMvcEnabled()) {
            $layout->setLayoutPath(APPLICATION_PATH.'/modules/adapto/layouts');
            $layout->setLayout('default');
        }
    }
}