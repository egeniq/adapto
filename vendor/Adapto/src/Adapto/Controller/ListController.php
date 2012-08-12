<?php

namespace Adapto\Controller;

/**
 * This controller does the listview and its ajax actions
 * 
 *
 */
class ListController extends AbstractController
{
    public function indexAction()
    {
        $view = new \Zend\View\Model\ViewModel();
        
        $view->setTemplate('adapto.phtml');
        $view->content = 'Here be an awesome recordlist';
        return $view;
    }
    

}

