<?PHP

  /**
   * This entity demonstrates 'column based data internationalisation'.
   *
   * Date internationalisation is used when you want to store your data in
   * multiple different languages. Column based internationalisation means
   * that you use a single table for your data, and put each translation in
   * a separate column in that table. For example, if you have a table called
   * 'content', and you have a 'title' fields that should to be stored in
   * several different languages, then you would create a column for each
   * language, for example 'title_en', 'title_nl', 'title_de'.
   *
   * Column based data internationalisation is useful in cases where the
   * number of columns that qualify for internationalisation in a table is
   * low.
   *
   * To make use of this, you have to first tell ATK which languages you
   * support. You do this in the config.inc.php file. See the
   * 'data internationalisation configuration' section in the config.inc.php
   * file in the root of the demo application for the 2 configuration options
   * that you need to set.
   *
   * In order to make use of multilanguage columns, you need to use a set of
   * special, internationalisation-aware attributes:
   */
  useattrib("atkmlattribute");
  useattrib("atkmltextattribute");
  useattrib("atkmlnumberattribute");
  useattrib("atkmlhtmlattribute");

  /**
   * ATK will present both the default language and a selected language on
   * screen so the user can more easily translate his data.
   * To switch between the supported languages, he can use a dropdown. This
   * dropdown is of the type atkMlSelectorAttribute.
   */
  useattrib("atkmlselectorattribute");

  class translation extends Adapto_Entity
  {
    function translation()
    {
      $this->atkEntity("translation", EF_ADD_LINK);

      $this->add(new Adapto_Attribute("id", AF_AUTOKEY));

      /**
       * The atkMlSelectorAttribute does not correspond to a column in the
       * database. In fact it is a dummy attribute that just presents a
       * dropdown of available languages.
       */
      $this->add(new Adapto_MlSelectorAttribute("mlselect"));

      /**
       * The atkMlAttribute is similar to an atkAttribute, only it is data
       * internationalisation aware. This means that while you name it 'name'
       * here, it will use the columns 'name_nl', 'name_en' etc. in the
       * underlying database table.
       */
      $this->add(new Adapto_MlAttribute("name", AF_SEARCHABLE));

      /**
       * Similar to atkMlAttribute, there are some other atkMl*Attributes that
       * are also data aware.
       */
      $this->add(new Adapto_MlNumberAttribute("number"));
      $this->add(new Adapto_MlTextAttribute("notes", AF_HIDE_LIST));
      $this->add(new Adapto_MlHtmlAttribute("htmlnotes",AF_HIDE_LIST));

      /**
       * Note that methods that refer to actual table names, such as the SQL
       * block from the setOrder method, should use the actual database
       * column name, otherwise ATK wouldn't know on which language to sort.
       */
      $this->setOrder("name_en");
      $this->setIndex("name_en");
      $this->setTable("lesson7_translation");
    }

    function descriptor_def()
    {
      return "[name]";
    }

    function adminFooter()
    {
      return entitySourceUrl("lesson7.translation");
    }
  }
?>