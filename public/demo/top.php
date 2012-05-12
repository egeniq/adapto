<?php

  /**
   * This file is part of the Adapto Toolkit.
   * Detailed copyright and licensing information can be found
   * in the doc/COPYRIGHT and doc/LICENSE files which should be
   * included in the distribution.
   *
   * This file is the skeleton top frame file, which you can copy
   * to your application dir and modify if necessary. By default,
   * it displays the currently logged-in user and a logout link.
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
   * @internal includes.
   */
  $config_atkroot = "./";
  include_once("atk.inc");

  atksession();
  atksecure();

  $page = &Adapto_ClassLoader::create("atk.ui.atkpage");
  $ui = Adapto_ClassLoader::getInstance("atk.ui.atkui");
  $theme = &atkTheme::getInstance();
  $output = &atkOutput::getInstance();

  $page->register_style($theme->stylePath("style.css"));
  $page->register_stylecode("form{display: inline;}");
  $page->register_style($theme->stylePath("top.css"));

  //Backwards compatible $content, that is what will render when the box.tpl is used instead of a top.tpl
  $loggedin = atktext("logged_in_as", "", "atk").": <b>".$g_user["name"]."</b>";
  $content = '<br>'.$loggedin.' &nbsp; <a href="app.php?atklogout=1" target="_top">'.ucfirst(atktext("logout")).' </a>&nbsp;<br/><br/>';

  $top = $ui->renderBox(array("content"=> $content,
  			      "logintext" => atktext("logged_in_as"),
                              "logouttext" => ucfirst(atktext("logout", "", "atk")),
                              "logoutlink" => "app.php?atklogout=1",
                              "logouttarget"=>"_top",
                              "centerpiece"=>"",
                              "searchpiece"=>"",
                              "title" => atktext("app_title"),
  			      "user"   => $g_user["name"]),
                              "top");

  $page->addContent($top);

  $output->output($page->render(atktext('app_title'), true));

  $output->outputFlush();

?>