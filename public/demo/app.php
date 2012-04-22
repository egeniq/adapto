<?php
  /**
   * This file is part of the Adapto Toolkit.
   * Detailed copyright and licensing information can be found
   * in the doc/COPYRIGHT and doc/LICENSE files which should be
   * included in the distribution.
   *
   * This file is the skeleton main frameset file, which you can copy
   * to your application dir and modify if necessary. By default, it checks
   * the settings $config_top_frame to determine how many frames to show,
   * and reads the menu config to display the proper menu.
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
  atksession();
  atksecure();
  $output='<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">';
  $output.="\n<html>\n <head>\n";
  $output.='  <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset='.atktext("charset","","atk").'">';
  $output.="\n  <title>".atktext('app_title')."</title>\n </head>\n";

  
  
  
  $menu = &atkMenu::getMenu();
  $theme = &atkinstance('atk.ui.atktheme');
  
  $position = $menu->getPosition();
  $scrolling = ($menu->getScrollable()==MENU_SCROLLABLE?FRAME_SCROLL_AUTO:FRAME_SCROLL_NO);
  if(isset($Adapto_VARS["atkentitytype"]) && isset($Adapto_VARS["atkaction"]))
  {
    $destination = "dispatch.php?atkentitytype=".$Adapto_VARS["atkentitytype"]."&atkaction=".$Adapto_VARS["atkaction"];
    if (isset($Adapto_VARS["atkselector"])) $destination.="&atkselector=".$Adapto_VARS["atkselector"];
  }
  else
  {
    $destination = "welcome.php";
  }

  $frame_top_height = $theme->getAttribute('frame_top_height');
  $frame_menu_width = $theme->getAttribute('frame_menu_width');
  $topframe = &new Adapto_Frame($frame_top_height?$frame_top_height:"75", "top", "top.php", FRAME_SCROLL_NO, true);
  $mainframe = &new Adapto_Frame("*", "main", $destination, FRAME_SCROLL_AUTO, true);
  $menuframe = &new Adapto_Frame(($position==MENU_LEFT||$position==MENU_RIGHT?($frame_menu_width?$frame_menu_width:190):$menu->getHeight()), "menu", "menu.php", $scrolling);
  $noframes = '<p>Your browser doesnt support frames, but this is required to run '.atktext('app_title')."</p>\n";

  $root = &new Adapto_RootFrameset();
  if (Adapto_Config::getGlobal("top_frame"))
  {
    $outer = &new Adapto_FrameSet("*", FRAMESET_VERTICAL, 0, $noframes);
    $outer->addChild($topframe);
    $root->addChild($outer);
  }
  else
  {
    $outer = &$root;
    $outer->m_noframes = $noframes;
  }

  $orientation = ($position==MENU_TOP||$position==MENU_BOTTOM?FRAMESET_VERTICAL:FRAMESET_HORIZONTAL);

  $wrapper = &new Adapto_FrameSet("*", $orientation);

  if($position==MENU_TOP||$position==MENU_LEFT)
  {
    $wrapper->addChild($menuframe);
    $wrapper->addChild($mainframe);
  }
  else
  {
    $wrapper->addChild($mainframe);
    $wrapper->addChild($menuframe);
  }

  $outer->addChild($wrapper);

  $output.= $root->render();
  $output.= "</html>";
  echo $output;
?>
