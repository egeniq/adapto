<?php

namespace Adapto\Controller;

use Zend\Mvc\MvcEvent;

abstract class AbstractController extends \Zend\Mvc\Controller\AbstractActionController
{
    protected $_parentController = NULL;
    
    
    
    public function onDispatch(MvcEvent $e)
    {
        $this->_parentController = $e->getParam('parentController');
        
        parent::onDispatch($e);
    }
    
    /**
     * @return \Adapto\Mvc\Controller\CRUDController
     */
    public function getParentController()
    {
        return $this->_parentController();
    }
}