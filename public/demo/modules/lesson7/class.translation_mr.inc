<?PHP

/**
   * This entity demonstrates 'row based data internationalisation'.
   *
   * This entity is very similar to the translation entity (read the
   * documentation over there first).
   *
   * The difference is in storage. With row based data
   * internationalisation, there aren't multiple columns as in the
   * previous example. Instead, multiple records are inserted, each
   * for a different language. This requires the addition of one extra
   * column, for example 'language', that stores the language of the
   * row in question.
   *
   * While editing, ATK retrieves all records that belong together to
   * present them in the same interface as with column based i18n.
   * So for the user, both methods look similar. From a database point of
   * view they are entirely different.
   *
   */

  useattrib("atkmlattribute");
  useattrib("atkmltextattribute");
  useattrib("atkmlnumberattribute");
  useattrib("atkmlhtmlattribute");
  useattrib("atkmlselectorattribute");

  class translation_mr extends Adapto_Entity
  {
    function translation_mr()
    {
      /**
       * To tell ATK that we want to use row based internationalisation
       * instead of column based, we have to pass the EF_ML flag to the
       * entity
       */
      $this->atkEntity("translation_mr", EF_ADD_LINK|EF_ML);

      $this->add(new Adapto_Attribute("id", AF_AUTOKEY));
      $this->add(new Adapto_MlSelectorAttribute("mlselect"));
      $this->add(new Adapto_MlAttribute("name", AF_SEARCHABLE));
      $this->add(new Adapto_MlNumberAttribute("numbervalue"));
      $this->add(new Adapto_MlTextAttribute("notes", AF_HIDE_LIST));
      $this->add(new Adapto_MlHtmlAttribute("htmlnotes",AF_HIDE_LIST));

      /**
       * Also, we have to add the language column. It doesn't really matter
       * what you name this column, but you have to add the AF_MULTILANGUAGE
       * flag to tell ATK that this is the column that you use to filter
       * for languages. It can be hidden as the user will not have any use
       * for this column as ATK will present the user with all languages of
       * the same record.
       */
      $this->add(new Adapto_Attribute("lng",AF_MULTILANGUAGE|AF_HIDE));

      $this->setOrder("name");
      $this->setIndex("name");
      $this->setTable("lesson7_translation_mr");
    }

    function descriptor_def()
    {
      return "[name]";
    }

    function adminFooter()
    {
      return entitySourceUrl("lesson7.translation_mr");;
    }
  }
?>