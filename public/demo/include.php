<?php

  /**
   * This file is part of the Adapto Toolkit.
   * Detailed copyright and licensing information can be found
   * in the doc/COPYRIGHT and doc/LICENSE files which should be 
   * included in the distribution.
   *
   * This file is the skeleton main include wrapper, which you can copy
   * to your application dir and modify if necessary. It is used to 
   * include popups in a safe manner. Any popup loaded with this wrapper
   * has session support and login support. 
   * Only files defined in the $config_allowed_includes array are allowed
   * to be included.
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
   * @internal includes
   */
  $config_atkroot = "./";
  include_once("atk.inc");

  atksession();
  atksecure();

  $file = $Adapto_VARS["file"];
  $allowed = Adapto_Config::getGlobal("allowed_includes");
  if (Adapto_in_array($file, $allowed))
    include_once(Adapto_Config::getGlobal("atkroot").$file);
?>