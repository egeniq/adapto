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
        $box = $ui->renderBox(array("title"=>atktext("app_shorttitle"),
                                                   "content"=>"<br><br>".atktext("app_description")."<br><br>"));

         $page->addContent($box);
         
         return $page->render(atktext('app_shorttitle'), true);

    }


}

