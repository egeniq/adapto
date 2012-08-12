<?php

namespace Adapto\Controller;

/**
 * This controller does the listview and its ajax actions
 * 
 * TODO: Since we're forwarded from an actual controller that implements all the getUIDef stuff,
 * how do we access that? Should we pass the original controller to this controller from inside
 * the forwarder in Adapto's CRUDController?
 *
 */
class ListController extends \Zend\Mvc\Controller\AbstractActionController
{
    public function indexAction()
    {
        
        $view = new \Zend\View\Model\ViewModel();
        
        $view->setTemplate('adapto.phtml');
        $view->content = 'Here be an awesome recordlist';
        return $view;
    }
    

}

