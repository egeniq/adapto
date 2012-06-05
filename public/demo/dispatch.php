<?php

  /**
   * This file is part of the Adapto Toolkit.
   * Detailed copyright and licensing information can be found
   * in the doc/COPYRIGHT and doc/LICENSE files which should be
   * included in the distribution.
   *
   * This file is the skeleton dispatcher file, which you can copy
   * to your application dir and modify if necessary. By default, it
   * checks the $atkentitytype and $atkaction postvars and creates the
   * entity and dispatches the action.
   *
   * @package adapto
   * @subpackage skel
   *
   * @author ijansch
   *
   * @copyright (c)2000-2004 Ivo Jansch
   * @license http://www.achievo.org/atk/licensing ATK Open Source License
   *


   */

  /**
   * @internal Setup the system
   */
  $config_atkroot = "./";
  include_once("atk.inc");

  atksession();   

  $session = &atkSessionManager::getSession();
  $output = &atkOutput::getInstance();

  if($Adapto_VARS["atkentitytype"]=="" || $session["login"]!=1)
  {
    // no entitytype passed, or session expired

    $page = &Adapto_ClassLoader::create("atk.ui.atkpage");
    $ui = Adapto_ClassLoader::getInstance("atk.ui.atkui");
    $theme = &atkTheme::getInstance();
    

    $page->register_style($theme->stylePath("style.css"));

    $destination = "index.php?atklogout=true";
    if(isset($Adapto_VARS["atkentitytype"]) && isset($Adapto_VARS["atkaction"]))
    {
      $destination .= "&atkentitytype=".$Adapto_VARS["atkentitytype"]."&atkaction=".$Adapto_VARS["atkaction"];
      if (isset($Adapto_VARS["atkselector"])) $destination.="&atkselector=".$Adapto_VARS["atkselector"];
    }

    $title = atktext("title_session_expired");
    $contenttpl = '<br>%s<br><br><input type="button" onclick="top.location=\'%s\';" value="%s"><br><br>';
    $content = sprintf($contenttpl, atktext("explain_session_expired"), str_replace("'", "\\'", $destination), atktext("relogin"));
    $box = $ui->renderBox(array("title" => $title, "content" => $content));

    $page->addContent($box);
    $output->output($page->render(atktext("title_session_expired"), true));
  }
  else
  {
    atksecure();
    

    $lockType = Adapto_Config::getGlobal("lock_type");
    if (!empty($lockType)) atklock();

    // Create entity
    $obj = &atkGetEntity($Adapto_VARS["atkentitytype"]);

    $flags = array_key_exists("atkpartial", $Adapto_VARS) ? HTML_PARTIAL : HTML_STRICT;

    //Handle http request   
    $controller = Adapto_ClassLoader::getInstance("atk.atkcontroller");
    $controller->dispatch($Adapto_VARS, $flags);
  }
  $output->outputFlush();
?>
