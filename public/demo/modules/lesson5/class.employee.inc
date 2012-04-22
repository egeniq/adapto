<?php

  /**
   * This entity is similar to the employee entity of lesson 4.
   *
   * The addition in this lesson is the loginname ('employeeid' in this
   * case), and a password.
   *
   * ATK supports many security schemes. In this case, we choose an employee
   * as login, and each employee has a profile to determine his access rights.
   * We have to tell ATK how we arranged security, so in the configuration
   * file, you will find the following config entries:
   *
   * $config_auth_usertable   = "lesson5_employee";
   * $config_auth_userfield   = "login";
   * $config_auth_passwordfield = "password";
   *
   * $config_auth_leveltable  = "lesson5_employee";
   * $config_auth_levelfield  = "profile_id";
   * $config_auth_accesstable = "lesson5_accessright";
   *
   * When you give users in the employee table a login, a password and a
   * profile, they can login, and execute those screens that you granted
   * them access to. You will notice that lesson 1 to 4 will disappear for
   * new users, since we never configured access rights for any of them,
   * so only the administrator user (who always has full access) can access
   * those lessons.
   */
  useattrib("atktextattribute");
  useattrib("atkdateattribute");
  useattrib("atknumberattribute");
  useattrib("atkemailattribute");
  useattrib("atkdummyattribute");

  /**
   * The password attribute can be used to enter passwords.
   */
  useattrib("atkpasswordattribute");

  userelation("atkmanytoonerelation");
  userelation("atkonetomanyrelation");

  class employee extends Adapto_Entity
  {

    function employee()
    {
      /**
       * Another flag is introduced in this lesson. It's EF_MRA, which is
       * short for EF_MULTI_RECORD_ACTIONS. By setting this flag, a list of
       * checkboxes appears next to the records, and multiple records can now
       * be deleted all at once.
       */
      $this->atkEntity("employee", EF_ADD_LINK|EF_TRACK_CHANGES|EF_MRA);

      $this->add(new Adapto_Attribute("id", AF_AUTOKEY));

      /**
       * The login field is the field used to login to the application.
       */
      $this->add(new Adapto_Attribute("login",AF_OBLIGATORY|AF_UNIQUE|AF_HIDE_LIST));

      $this->add(new Adapto_Attribute("name", AF_OBLIGATORY|AF_UNIQUE|AF_SEARCHABLE));

      /**
       * The following line adds the password attribute. The flag
       * AF_PASSWORD_NOVALIDATE has the effect that to change someones'
       * password, we don't need to enter the original password first. This
       * is useful for administrators.
       * Should this be a screen that users can edit themselves, this flag
       * could be removed, and then a user can only change his password if he
       * enters the original password first.
       */
      $this->add(new Adapto_PasswordAttribute("password",true,AF_HIDE_LIST|AF_PASSWORD_NOVALIDATE));

      $this->add(new Adapto_DummyAttribute("comment","The demo will send mail to the address below!",AF_HIDE_LIST));
      $this->add(new Adapto_EmailAttribute("email"));
      $this->add(new Adapto_ManyToOneRelation("department_id","lesson5.department", AF_SEARCHABLE));
      $this->add(new Adapto_ManyToOneRelation("manager_id","lesson5.employee", AF_SEARCHABLE));
      $this->add(new Adapto_DateAttribute("hiredate"));
      $this->add(new Adapto_NumberAttribute("salary", AF_TOTAL));
      $this->add(new Adapto_TextAttribute("notes", 0, AF_HIDE_LIST));

      /**
       * In the profile_id field, we store the employee profile.
       *
       * We've already seen the many2oonerelation, but what's new here is the
       * AF_RELATION_AUTOLINK flag. This flag makes direct links to the
       * profile editor. Next to the dropdown for selecting a profile, there
       * are now links for editing the current profile, or adding a new one.
       */
      $this->add(new Adapto_ManyToOneRelation("profile_id", "lesson5.profile", AF_RELATION_AUTOLINK|AF_HIDE_ADD));

      $this->setOrder("name");
      $this->setIndex("name");
      $this->setTable("lesson5_employee");
    }

    function rowColor($record)
    {
      $manager_id = $record["manager_id"]["id"];

      if ($manager_id!="")
      {
        $salary = $record["salary"];
        $attr = &$this->getAttribute("manager_id");
        $mgr = &$attr->getDestination();

        $manager_recs = $mgr->selectDb("id=$manager_id", "", "", "", array("salary"));

        $manager_salary = $manager_recs[0]["salary"];
        if ($salary > $manager_salary) return "#ff0000";
      }
    }

    function postAdd($rec)
    {
      if($rec["email"]!='')
      {
         $num = $this->countDb("","",array("id"));
         $body = "Hi ".$rec["name"].",\n\nWelcome to the company and have a good time!\n\n".
                 "We now have $num employees\n";
         mail($rec["email"],"Welcome",$body);
      }
      return true;
    }

    function postUpdate($rec)
    {
      $newsal = $rec["salary"];
      $oldsal = $rec["atkorgrec"]["salary"];

      if($rec["email"]!='' && $newsal!=$oldsal)
      {
        $body = "Hi ".$rec["name"].",\n\n".
                "We ".($oldsal>$newsal?"lowered":"raised")." your salary with ".abs($newsal-$oldsal).".\n".
                "So your current salary is now: ".$rec["salary"]."\n";

        mail($rec["email"],"Salary changed",$body);
      }
      return true;
    }

    function postDel($rec)
    {
      if($rec["email"]!='')
      {
         $body = "Hi ".$rec["name"].",\n\nThanks for working by the company!";
         mail($rec["email"],"Goodbye",$body);
      }
      return true;
    }



    function descriptor_def()
    {
      return "[name]";
    }

    function adminFooter()
    {
      return entitySourceUrl("lesson5.employee");
    }
  }

?>
