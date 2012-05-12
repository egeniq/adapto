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
        $theme = Adapto_Ui_Theme::getInstance();

        $page->register_style($theme->stylePath("style.css"));
        $box = $ui
                ->renderBox(
                        array("title" => Adapto_Language::_("app_shorttitsle"), "content" => "<br><br>" . Adapto_Language::_("app_description")
                                . "<br><br>"));

        $page->addContent($box);

        $this->view->content = $page->render(Adapto_Language::_('app_shorttitle'), true);

    }

}

