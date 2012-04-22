<?php
  /**
   * This file is part of the Adapto Toolkit.
   * Detailed copyright and licensing information can be found
   * in the doc/COPYRIGHT and doc/LICENSE files which should be
   * included in the distribution.
   *
   * This file is the skeleton menu loader, which you can copy to your
   * application dir and modify if necessary. By default, it checks
   * the menu settings and loads the proper menu.
   *
   * @package adapto
   * @subpackage skel
   *
   * @author ijansch
   * @author petercv
   *
   * @copyright (c)2000-2004 Ibuildings.nl BV
   * @license http://www.achievo.org/atk/licensing ATK Open Source License
   *


   */

  /**
   * @internal includes
   */
  $config_atkroot = "./";
  include_once("atk.inc");

  atksession();
  atksecure();

  $output = &atkOutput::getInstance();
        
  /* general menu stuff */
  /* load menu layout */
  
  $menu = &atkMenu::getMenu();
                  
  if (is_object($menu)) $output->output($menu->render());
  else atkerror("no menu object created!");;
              
  $output->outputFlush();
                        
?>