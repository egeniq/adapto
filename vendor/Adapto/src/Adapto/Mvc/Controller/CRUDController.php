<?php

namespace Adapto\Mvc\Controller;

use Zend\Mvc\MvcEvent;

class CRUDController extends \Zend\Mvc\Controller\AbstractActionController
{
    protected function _init()
    {
        
    }
    
    protected function _setEntityDefClass($class)
    {
        
    }

    protected function _createEntityDef()
    {
        
    }

    public function getEntityDef()
    {
        
    }
    
    protected function _setUIDefClass($class)
    {
        
    }

    protected function _createUIDef()
    {
        
    }
    
    public function getUIDef()
    {
        
    }
    
    protected function _addAction($action, $class, $params=array())
    {
        
    }
    
    protected function _removeAction($action, $class)
    {
        
    }
    
    protected function _setActionParams($action, $params) 
    {
        
    }
    
    protected function _createAction($name)
    {
        // Todo, this won't work, since we use ZF2 forwarding. We don't
        // actually instantiate the controller ourselves. 
    }
    
    /**
     * Execute the request.
     * 
     * This overrides ZF2's default executor so we can hook up our
     * standard set of crud actions without actually implementing them
     * in the controllers.
     *
     * @param  MvcEvent $e
     * @return mixed
     * @throws Exception\DomainException
     */
    public function onDispatch(MvcEvent $e)
    {
        $routeMatch = $e->getRouteMatch();
        if (!$routeMatch) {
            /**
             * @todo Determine requirements for when route match is missing.
             *       Potentially allow pulling directly from request metadata?
             */
            throw new Exception\DomainException('Missing route matches; unsure how to retrieve action');
        }

        $action = $routeMatch->getParam('action', 'not-found');
        $method = static::getMethodFromAction($action);

        if (!method_exists($this, $method)) {
            if (in_array($action, array("list","edit","add","etc"))) {
                // todo, here is where we do magic lookups. For now let's see if this works.
                
                // Forward the response to one of Adapto's standard action controllers.
                $e->setParam('parentController', $this);
                $actionResponse = $this->forward()->dispatch('Adapto/Controller/'.ucfirst($action), array('action'=>'index'));            
                $e->setResult($actionResponse);
                return $actionResponse;
            } else {
                // Original ZF behaviour
                $method = 'notFoundAction';
            }
        }

        $actionResponse = $this->$method();

        $e->setResult($actionResponse);
        return $actionResponse;
    }
    
}