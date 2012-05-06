<?php

class Adapto_IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $page = new Adapto_Ui_Page();
        $ui = Adapto_Ui::getInstance();
        $theme = Adapto_Ui_Theme::getInstance();

        $page->register_style($theme->stylePath("style.css"));
        $box = $ui->renderBox(array("title"=>Adapto_Language::text("app_shorttitle"),
                                                   "content"=>"<br><br>".Adapto_Language::text("app_description")."<br><br>"));

         $page->addContent($box);
         
         $this->view->content = $page->render(Adapto_Language::text('app_shorttitle'), true);
         
         
     
    }


}

