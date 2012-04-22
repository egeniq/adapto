<?php
 /**
   * This entity is similar to the employee entity of lesson 1.
   * The addition is a new attribute for exporting to openoffice
   * documents
   */
  useattrib("atktextattribute");
  useattrib("atkdateattribute");
  useattrib("atktimeattribute");
  useattrib("atknumberattribute");
  useattrib("atkCurrencyAttribute");

  // Include the atkDocumentAttribute for export to openoffice
  useattrib("atkdocumentattribute");


  class employee extends Adapto_Entity
  {
    function employee()
    {
      $this->atkEntity("employee", EF_ADD_LINK);

      $this->add(new Adapto_Attribute("id", AF_AUTOKEY));
      $this->add(new Adapto_Attribute("name", AF_OBLIGATORY|AF_UNIQUE|AF_SEARCHABLE));
      $this->add(new Adapto_DateAttribute("hiredate"));
      $this->add(new Adapto_CurrencyAttribute("salary",AF_TOTAL,10,"EUR",2,",","."));
      $this->add(new Adapto_TextAttribute("notes", 0, AF_HIDE_LIST));

      /**
       * For document attributes, ATK automatically searches for template
       * documents in a specific directory. The base directory to search in
       * can be specified below. The document templates must be put in a
       * specific directory structure under this base directory: first of all
       * a subdirectory must be made for every module for which you want to
       * include document templates (equal to the modulename of that module, as
       * set in config.inc.php). Then a subdirectory in that directory must be
       * made according to the name of the entity for which you want to include
       * document templates. In this subdirectory you can put your document
       * template files. So if you have $config_doctemplatedir (see config.inc.php)
       * set to "doctemplates/", then you can put your documents in
       * "doctemplates/modulename/entityname/".
       * A template is just like a normal document, but you can put strings named after
       * fieldnames. For example [name] will be replaced with the name in the database.
       * [name_label] will be replaced with the label of the name field.
       * In the next version we will make some more examples with loops etc.
       * The template in this demo is made with openoffice 2.
       */
      $this->add(new Adapto_DocumentAttribute("documents"));

      $this->setOrder("name");
      $this->setIndex("name");
      $this->setTable("lesson8_employee");
    }

    function descriptor_def()
    {
      return "[name]";
    }

    function adminFooter()
    {
      return entitySourceUrl("lesson8.employee");
    }
  }

?>