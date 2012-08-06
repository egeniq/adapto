<?php

class Adapto_ListController extends Adapto_Controller_Action_Entity
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $page = new Adapto_Ui_Page($this);
        $ui = Adapto_Ui::getInstance();
        
        $this->view->grid = "Here comes the recordlist enzo";
        
        $page->finalize("List Page");

    }

}

