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

  class translation_mr2 extends Adapto_MetaEntity  
  {
     protected $table = 'lesson7_translation_mr';
     protected $order = 'name';
     protected $index = 'name';
     protected $descriptor = '[name]';
     protected $flags = array(EF_ADD_LINK, EF_ML);

     public static function meta(atkMetaPolicy $policy)
     {
        $policy->setType('name', 'atkmlattribute');
        $policy->setType('notes', 'atkmltextattribute');
        $policy->setType('htmlnotes', 'atkmlhtmlattribute');
        $policy->addFlag('lng', AF_MULTILANGUAGE|AF_HIDE);
        // We need to remove the primary flag of the lng attribute else ATK won't retrieve the
        // other ml records
	$policy->removeFlag('lng', AF_PRIMARY);
     }    


    function adminFooter()
    {
      return entitySourceUrl("lesson7.translation_mr");;
    }
  }
?>