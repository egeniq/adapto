<?php

  /**
   * This file is part of the Adapto Toolkit.
   * Detailed copyright and licensing information can be found
   * in the doc/COPYRIGHT and doc/LICENSE files which should be
   * included in the distribution.
   *
   * This file is the skeleton main welcome file, which you can copy
   * to your application dir and modify if necessary. By default, it
   * displays a welcome message from the language file (app_description).
   *
   * @package adapto
   * @subpackage skel
   *
   * @author ijansch
   *
   * @copyright (c)2000-2004 Ibuildings.nl BV
   * @license http://www.achievo.org/atk/licensing ATK Open Source License
   *


   */

  /**
   * @internal includes..
   */
  $config_atkroot = "./";
  include_once("atk.inc");

  $page = &atknew("atk.ui.atkpage");
  $ui = &atkinstance("atk.ui.atkui");
  $theme = &atkTheme::getInstance();
  $output = &atkOutput::getInstance();

  $page->register_style($theme->stylePath("style.css"));
  $box = $ui->renderBox(array("title"=>atktext("app_shorttitle"),
                                            "content"=>"<br><br>".atktext("app_description")."<br><br>"));

  $page->addContent($box);
  $output->output($page->render(atktext('app_shorttitle'), true));

  $output->outputFlush();

?>
