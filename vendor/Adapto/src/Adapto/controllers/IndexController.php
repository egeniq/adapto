<?php

class Adapto_IndexController extends Adapto_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $page = new Adapto_Ui_Page($this);
        $ui = Adapto_Ui::getInstance();

        $box = $ui
                ->renderBox(
                        array("title" => Adapto_Language::_("app_shorttitle"), "content" => "<br><br>" . Adapto_Language::_("app_description")
                                . "<br><br>"));

        $this->view->content = $box;
        
        $page->finalize(Adapto_Language::_('app_shorttitle'));

    }

}

