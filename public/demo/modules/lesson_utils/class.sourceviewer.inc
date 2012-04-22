<?php

  class sourceviewer extends Adapto_Entity
  {
    function sourceviewer()
    {
      $this->atkEntity("sourceviewer");
    }

    function getSourceFileName($module, $entity)
    {
      if ($entity=="")
        $filename = moduleDir($module)."module.inc";
      else
        $filename = moduleDir($module)."class.".$entity.".inc";

      if (strpos($filename, "./") === 0)
        $filename = substr($filename, 2);

      if ((strpos($filename, "..")!==false) || (!file_exists($filename)))
        return false;

      return $filename;
    }

    function getContent($filename)
    {
      if ($filename !== false)
      {
        $content = '<div align="left" style="font-size: 10pt">';
        $content.= '<br>'.$this->text("sourcecode_of").' <b>'.$filename.'</b>:<br><br>';
        $content.= highlight_file($filename, true);
        $content.='</div>';
      }
      else
      {
        $content = $this->text("invalid_file");
      }

      if (atkLevel()>0)
        $content.= '<br><br>'.atkButton("&lt;&lt; " . $this->text("back"), "", SESSION_BACK, false);

      return $content;
    }

    function getTitle($module, $entity)
    {
      $title = $this->text("the_source_for") . " ";
      if (empty($entity))
        $title .= $this->text("module") . " $module";
      else
        $title .= $this->text("entity") . " $module.$entity";
      return $title;
    }

    function action_view()
    {
      // Get used objects
      $sessionmanager = &atkGetSessionManager();
      $page = &$this->getPage();
      $theme = &atkTheme::getInstance();
      $ui = &$this->getUi();

      // Read the module and entity and filename
      $module = $sessionmanager->pageVar("module");
      $entity = $sessionmanager->pageVar("entity");
      $filename = $this->getSourceFileName($module, $entity);

      // Create the interface
      $content = $this->getContent($filename);
      $title = $this->getTitle($module, $entity);
      $output = $ui->renderBox(array("title"=>$title, "content"=>$content));
      $actionpage = $this->renderActionPage("view", $output);

      // Add the interface to the page
      $page->register_style($theme->stylePath("style.css"));
      $page->addContent($actionpage);
    }

  }

?>