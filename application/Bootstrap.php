<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initAdapto()
    {
        $front = Zend_Controller_Front::getInstance();  
        $front->registerPlugin( new Adapto_Controller_Plugin_ErrorControllerSelector() ); 
        
        $config = new Zend_Config_Ini(APPLICATION_PATH."/configs/adapto.ini", APPLICATION_ENV);
        
        Zend_Registry::set("Config_Adapto", $config);
    }

}

