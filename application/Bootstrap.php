<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initAdapto()
    {
        // Setup the adapto module error handler
        $front = Zend_Controller_Front::getInstance();  
        $front->registerPlugin( new Adapto_Controller_Plugin_ErrorControllerSelector() ); 
        
        // Set up configuration
        $config = new Zend_Config_Ini(APPLICATION_PATH."/configs/adapto.ini", APPLICATION_ENV);
        Zend_Registry::set("Config_Adapto", $config);
        
        // Set up translations
        $translate = new Adapto_Language();
        Zend_Registry::set("Adapto_Language", $translate);

        // Set up session management
        $session = new Zend_Session_Namespace("Adapto");
        Zend_Registry::set("Session_Adapto", $session);
       
        
    }

}

