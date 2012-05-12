<?php 

class Adapto_Controller_Plugin_ErrorControllerSelector extends Zend_Controller_Plugin_Abstract  
{  
    public function routeShutdown(Zend_Controller_Request_Abstract $request)  
    {  
        $front = Zend_Controller_Front::getInstance();  
  
        //If the ErrorHandler plugin is not registered, bail out  
        if( !($front->getPlugin('Zend_Controller_Plugin_ErrorHandler') instanceOf Zend_Controller_Plugin_ErrorHandler) )  
            return;  
  
        $error = $front->getPlugin('Zend_Controller_Plugin_ErrorHandler');  
    
        $error->setErrorHandlerModule("adapto");  
    }  
} 