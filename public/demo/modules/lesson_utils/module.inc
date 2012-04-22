<?php

  class mod_lesson_utils extends Adapto_Module
  {
  }

  function entitySourceUrl($entitytype)
  {
    list($module, $entity) = explode(".", $entitytype);
    return href(dispatch_url("lesson_utils.sourceviewer", "view", array("module"=>$module, "entity"=>$entity)), atkText("view_source", "lesson_utils"), SESSION_NESTED);
  }

  function moduleSourceUrl($module)
  {
    return dispatch_url("lesson_utils.sourceviewer", "view", array("module"=>$module));
  }

?>
