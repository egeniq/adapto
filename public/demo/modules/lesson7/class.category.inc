<?php

  /**
   * This entity introduces the atkTreeEntity. The atkTreeEntity is an atkEntity
   * specialisation that is created for handling data that has a hierarchical
   * structure. The adminpage is rendered as a tree instead of a recordlist,
   * to reflect the data's hierarchical structure.
   *
   * We deviate from the default employee example here because a
   * 'category' (for example product category) is more intuitive when
   * thinking of trees.
   */
  

  /**
   * Also, this entity introduces the atkManyToOneTreeRelation. It is similar
   * to the regular atkManyToOneRelation as seen in the previous lessons,
   * but it is especially suited to create a relation to atkTreeEntitys. It
   * renders the dropdown box in a tree-like fashion to reflect the data
   * structure.
   * In this example lesson, we use this relation to select a new parent
   * for a record.
   */
  userelation("atkmanytoonetreerelation");

  class category extends Adapto_TreeEntity
  {
    function category()
    {
      /**
       * The EF_TREE_* flags are specifically designed to tweak behaviour of
       * the tree. EF_TREE_NO_ROOT_COPY establishes that root items can not be
       * copied. EF_TREE_NO_ROOT_DELETE disables deletion of tree root items.
       */
      $this->atkTreeEntity("category",EF_COPY|EF_TREE_NO_ROOT_COPY|EF_TREE_NO_ROOT_DELETE|EF_ADD_LINK);

      $this->add(new Adapto_Attribute("cat_id",AF_HIDE|AF_PRIMARY|AF_AUTO_INCREMENT));
      $this->add(new Adapto_Attribute("title",AF_SEARCHABLE|AF_OBLIGATORY));

      $this->add(new Adapto_ManyToOneTreeRelation("parent_cat_id","lesson7.category",AF_PARENT));

      $this->setTable("lesson7_category");
    }

    function descriptor_def()
    {
      return "--[title]";
    }

    function title_display($rec)
    {
      return $rec["title"]."------";
    }

    function adminFooter()
    {
      return entitySourceUrl("lesson7.category");
    }
  }

?>