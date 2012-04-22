#
# DEMO INSTALLATION FOR MYSQL DATABASES
# Please read doc/INSTALL before you start
#

#
# Table structure for table `lesson1_employee`
#

CREATE TABLE lesson1_employee
(
  id int(11) NOT NULL auto_increment,
  name varchar(50) NOT NULL,
  hiredate date default NULL,
  notes text,
  salary decimal(10,2) default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

#
# Demo data for table `lesson1_employee`
#

INSERT INTO lesson1_employee (id, name, hiredate, notes, salary) VALUES (1, 'Jack', '2004-04-27', '', '1000.00');
INSERT INTO lesson1_employee (id, name, hiredate, notes, salary) VALUES (2, 'Bill', '2000-06-01', 'Test employee', '60.00');
INSERT INTO lesson1_employee (id, name, hiredate, notes, salary) VALUES (3, 'Simon', '2004-02-09', 'Simon the Sourceror', '500.00');
# --------------------------------------------------------

#
# Table structure for table `lesson2_department`
#

CREATE TABLE lesson2_department
(
  id int(11) NOT NULL auto_increment,
  name varchar(100) NOT NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

#
# Demo data for table `lesson2_department`
#

INSERT INTO lesson2_department (id, name) VALUES (1, 'Sales department');
INSERT INTO lesson2_department (id, name) VALUES (2, 'Support');
# --------------------------------------------------------

#
# Table structure for table `lesson2_employee`
#

CREATE TABLE lesson2_employee
(
  id int(11) NOT NULL auto_increment,
  name varchar(50) NOT NULL,
  department_id int(11) default NULL,
  hiredate date default NULL,
  notes text,
  salary decimal(10,2) default NULL,
  manager_id int(11) default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

#
# Demo data for table `lesson2_employee`
#

INSERT INTO lesson2_employee (id, name, department_id, hiredate, notes, salary, manager_id) VALUES (1, 'Jack', 1, '2004-05-03', 'test', '1000.00', NULL);
INSERT INTO lesson2_employee (id, name, department_id, hiredate, notes, salary, manager_id) VALUES (2, 'Joe', 2, '2004-05-03', 'test employee', '500.00', 1);
# --------------------------------------------------------

#
# Table structure for table `lesson3_department`
#

CREATE TABLE lesson3_department
(
  id int(11) NOT NULL auto_increment,
  name varchar(100) NOT NULL,
  is_hiring int(1) NOT NULL default '1',
  PRIMARY KEY  (id)
) TYPE=MyISAM;

#
# Demo data for table `lesson3_department`
#

INSERT INTO lesson3_department (id, name, is_hiring) VALUES (1, 'Sales', 1);
INSERT INTO lesson3_department (id, name, is_hiring) VALUES (2, 'Support', 0);
# --------------------------------------------------------

#
# Table structure for table `lesson3_employee`
#

CREATE TABLE lesson3_employee
(
  id int(11) NOT NULL auto_increment,
  name varchar(50) NOT NULL,
  department_id int(11) default NULL,
  hiredate date default NULL,
  notes text,
  salary decimal(10,2) default NULL,
  manager_id int(11) default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

#
# Demo data for table `lesson3_employee`
#

INSERT INTO lesson3_employee (id, name, department_id, hiredate, notes, salary, manager_id) VALUES (1, 'Jack The Manager', 1, '2004-05-03', '', '1000.00', NULL);
INSERT INTO lesson3_employee (id, name, department_id, hiredate, notes, salary, manager_id) VALUES (2, 'Joe The Employee', 1, '2004-05-03', '', '500.00', 1);
INSERT INTO lesson3_employee (id, name, department_id, hiredate, notes, salary, manager_id) VALUES (3, 'Jill The Rich Employee', 1, '2004-05-03', '', '2000.00', 1);
# --------------------------------------------------------

#
# Table structure for table `lesson4_department`
#

CREATE TABLE lesson4_department
(
  id int(11) NOT NULL auto_increment,
  name varchar(100) NOT NULL,
  is_hiring int(1) NOT NULL default '1',
  PRIMARY KEY  (id)
) TYPE=MyISAM;

#
# Demo data for table `lesson4_department`
#

INSERT INTO lesson4_department (id, name, is_hiring) VALUES (1, 'Sales', 1);
INSERT INTO lesson4_department (id, name, is_hiring) VALUES (2, 'Support', 0);
# --------------------------------------------------------

#
# Table structure for table `lesson4_employee`
#

CREATE TABLE lesson4_employee
(
  id int(11) NOT NULL auto_increment,
  name varchar(50) NOT NULL,
  email varchar(100) default NULL,
  department_id int(11) default NULL,
  hiredate date default NULL,
  notes text,
  salary decimal(10,2) default NULL,
  manager_id int(11) default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

#
# Demo data for table `lesson4_employee`
#

INSERT INTO lesson4_employee (id, name, email, department_id, hiredate, notes, salary, manager_id) VALUES (1, 'Jack The Manager', NULL, 1, '2004-05-03', '', '1000.00', NULL);
INSERT INTO lesson4_employee (id, name, email, department_id, hiredate, notes, salary, manager_id) VALUES (2, 'Joe The Employee', NULL, 1, '2004-05-03', '', '500.00', 1);
INSERT INTO lesson4_employee (id, name, email, department_id, hiredate, notes, salary, manager_id) VALUES (3, 'Jill The Rich Employee', '', 1, '2004-05-03', '', '2100.00', 1);
# --------------------------------------------------------

#
# Table structure for table `lesson5_accessright`
#

CREATE TABLE lesson5_accessright
(
  entity varchar(100) NOT NULL,
  action varchar(25) NOT NULL,
  profile_id varchar(10) NOT NULL,
  PRIMARY KEY  (entity,action,profile_id)
) TYPE=MyISAM;

#
# Demo data for table `lesson5_accessright`
#

INSERT INTO lesson5_accessright (entity, action, profile_id) VALUES ('lesson5.department', 'add', '1');
INSERT INTO lesson5_accessright (entity, action, profile_id) VALUES ('lesson5.department', 'admin', '1');
INSERT INTO lesson5_accessright (entity, action, profile_id) VALUES ('lesson5.department', 'delete', '1');
INSERT INTO lesson5_accessright (entity, action, profile_id) VALUES ('lesson5.department', 'edit', '1');
INSERT INTO lesson5_accessright (entity, action, profile_id) VALUES ('lesson5.employee', 'add', '1');
INSERT INTO lesson5_accessright (entity, action, profile_id) VALUES ('lesson5.employee', 'admin', '1');
INSERT INTO lesson5_accessright (entity, action, profile_id) VALUES ('lesson5.employee', 'delete', '1');
INSERT INTO lesson5_accessright (entity, action, profile_id) VALUES ('lesson5.employee', 'edit', '1');
INSERT INTO lesson5_accessright (entity, action, profile_id) VALUES ('lesson5.profile', 'admin', '1');
# --------------------------------------------------------

#
# Table structure for table `lesson5_department`
#

CREATE TABLE lesson5_department
(
  id int(11) NOT NULL auto_increment,
  name varchar(100) NOT NULL,
  is_hiring int(1) NOT NULL default '1',
  PRIMARY KEY  (id)
) TYPE=MyISAM;

#
# Demo data for table `lesson5_department`
#

INSERT INTO lesson5_department (id, name, is_hiring) VALUES (1, 'Sales', 1);
INSERT INTO lesson5_department (id, name, is_hiring) VALUES (2, 'Support', 0);
# --------------------------------------------------------

#
# Table structure for table `lesson5_employee`
#

CREATE TABLE lesson5_employee
(
  id int(11) NOT NULL auto_increment,
  login varchar(25) NOT NULL,
  name varchar(50) NOT NULL,
  password varchar(50) default NULL,
  email varchar(100) default NULL,
  department_id int(11) default NULL,
  hiredate date default NULL,
  notes text,
  salary decimal(10,2) default NULL,
  manager_id int(11) default NULL,
  profile_id int(11) default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

#
# Demo data for table `lesson5_employee`
#

INSERT INTO lesson5_employee (id, login, name, password, email, department_id, hiredate, notes, salary, manager_id, profile_id) VALUES (1, 'jack', 'Jack The Manager', '098f6bcd4621d373cade4e832627b4f6', '', 1, '2004-05-03', '', '1000.00', NULL, 1);
INSERT INTO lesson5_employee (id, login, name, password, email, department_id, hiredate, notes, salary, manager_id, profile_id) VALUES (2, 'joe', 'Joe The Employee', NULL, NULL, 1, '2004-05-03', '', '500.00', 1, NULL);
INSERT INTO lesson5_employee (id, login, name, password, email, department_id, hiredate, notes, salary, manager_id, profile_id) VALUES (3, 'jill', 'Jill The Rich Employee', NULL, NULL, 1, '2004-05-03', '', '2000.00', 1, NULL);
# --------------------------------------------------------

#
# Table structure for table `lesson5_profile`
#

CREATE TABLE lesson5_profile
(
  id int(10) NOT NULL auto_increment,
  name varchar(50) NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM;

#
# Demo data for table `lesson5_profile`
#

INSERT INTO lesson5_profile (id, name) VALUES (1, 'Projectmanagers');
# --------------------------------------------------------

#
# Table structure for table `lesson6_employee1`
#

CREATE TABLE lesson6_employee1
(
  id int(11) NOT NULL auto_increment,
  name varchar(50) NOT NULL,
  hiredate date default NULL,
  notes text,
  salary double(8,1) default NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM;

#
# Demo data for table `lesson6_employee1`
#

INSERT INTO lesson6_employee1 (id, name, hiredate, notes, salary) VALUES (1, 'Jack', '2004-04-27', '', '1000.00');
INSERT INTO lesson6_employee1 (id, name, hiredate, notes, salary) VALUES (2, 'Bill', '2000-06-01', 'Test employee', '60.00');
INSERT INTO lesson6_employee1 (id, name, hiredate, notes, salary) VALUES (3, 'Simon', '2004-02-09', 'Simon the Sourceror', '500.00');
# --------------------------------------------------------

#
# Table structure for table `lesson6_employee2`
#

CREATE TABLE lesson6_employee2
(
  id int(11) NOT NULL auto_increment,
  name varchar(50) NOT NULL,
  hiredate date default NULL,
  notes text,
  salary double(8,1) default NULL,
  lesson6_department_id int(11) NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM;

#
# Demo data for table `lesson6_employee2`
#

INSERT INTO lesson6_employee2 (id, name, hiredate, notes, salary, lesson6_department_id) VALUES (1, 'Jack', '2004-04-27', '', '1000.00',1);
INSERT INTO lesson6_employee2 (id, name, hiredate, notes, salary, lesson6_department_id) VALUES (2, 'Bill', '2000-06-01', 'Test employee', '60.00',2);
INSERT INTO lesson6_employee2 (id, name, hiredate, notes, salary, lesson6_department_id) VALUES (3, 'Simon', '2004-02-09', 'Simon the Sourceror', '500.00',1);
# --------------------------------------------------------

#
# Table structure for table `lesson6_employee3`
#

CREATE TABLE lesson6_employee3
(
  id int(11) NOT NULL auto_increment,
  name varchar(50) NOT NULL,
  hiredate date default NULL,
  notes text,
  salary double(8,1) default NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM;

#
# Demo data for table `lesson6_employee3`
#

INSERT INTO lesson6_employee3 (id, name, hiredate, notes, salary) VALUES (1, 'Jack', '2004-04-27', '', '1000.00');
INSERT INTO lesson6_employee3 (id, name, hiredate, notes, salary) VALUES (2, 'Bill', '2000-06-01', 'Test employee', '60.00');
INSERT INTO lesson6_employee3 (id, name, hiredate, notes, salary) VALUES (3, 'Simon', '2004-02-09', 'Simon the Sourceror', '500.00');
# --------------------------------------------------------

#
# Table structure for table `lesson6_employee4`
#

CREATE TABLE lesson6_employee4
(
  id int(11) NOT NULL auto_increment,
  name varchar(50) NOT NULL,
  hiredate date default NULL,
  notes text,
  salary double(8,1) default NULL,
  lesson6_department_id int(11) NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM;

#
# Demo data for table `lesson6_employee4`
#

INSERT INTO lesson6_employee4 (id, name, hiredate, notes, salary, lesson6_department_id) VALUES (1, 'Jack', '2004-04-27', '', '1000.00',1);
INSERT INTO lesson6_employee4 (id, name, hiredate, notes, salary, lesson6_department_id) VALUES (2, 'Bill', '2000-06-01', 'Test employee', '60.00',2);
INSERT INTO lesson6_employee4 (id, name, hiredate, notes, salary, lesson6_department_id) VALUES (3, 'Simon', '2004-02-09', 'Simon the Sourceror', '500.00',1);
# --------------------------------------------------------

#
# Table structure for table `lesson6_department`
#

CREATE TABLE lesson6_department
(
  id int(11) NOT NULL auto_increment,
  name varchar(100) NOT NULL,
  is_hiring int(1) NOT NULL default '1',
  PRIMARY KEY (id)
) TYPE=MyISAM;

#
# Demo data for table `lesson6_department`
#

INSERT INTO lesson6_department (id, name) VALUES (1, 'Sales department');
INSERT INTO lesson6_department (id, name) VALUES (2, 'Support');
# --------------------------------------------------------

#
# Table structure for table `lesson7_category`
#

CREATE TABLE lesson7_category
(
  cat_id int(11) NOT NULL auto_increment,
  title varchar(50) NOT NULL,
  parent_cat_id int(11),
  PRIMARY KEY (cat_id)
) TYPE=MyISAM;

#
# Demo data for table `lesson7_category`
#

INSERT INTO lesson7_category (cat_id, title, parent_cat_id) VALUES (1, 'Test', NULL);
INSERT INTO lesson7_category (cat_id, title, parent_cat_id) VALUES (2, 'Test', 1);
INSERT INTO lesson7_category (cat_id, title, parent_cat_id) VALUES (4, 'Test2', NULL);
INSERT INTO lesson7_category (cat_id, title, parent_cat_id)VALUES (5, 'Test2 item', 4);
INSERT INTO lesson7_category (cat_id, title, parent_cat_id)VALUES (6, 'Test2 item', 4);
# --------------------------------------------------------

#
# Table structure for table `lesson7_translation`
#

CREATE TABLE lesson7_translation
(
  id int(11) NOT NULL auto_increment,
  name_nl varchar(200) default NULL,
  name_de varchar(200) default NULL,
  name_en varchar(200) default NULL,
  number_nl int(11) default NULL,
  number_de int(11) default NULL,
  number_en int(11) default NULL,
  notes_nl text,
  notes_de text,
  notes_en text,
  htmlnotes_nl text,
  htmlnotes_de text,
  htmlnotes_en text,
  PRIMARY KEY (id)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `lesson7_translation_mr`
#

CREATE TABLE lesson7_translation_mr
(
  id int(11) NOT NULL auto_increment,
  name varchar(200),
  numbervalue int(11) default NULL,
  notes text,
  htmlnotes text,
  lng varchar(10) NOT NULL default 'EN',
  PRIMARY KEY (id, lng)
) TYPE=MyISAM;

#
# Table structure for table `lesson8_employee`
#

CREATE TABLE lesson8_employee
(
  id int(11) NOT NULL auto_increment,
  name varchar(50) NOT NULL,
  hiredate date default NULL,
  notes text,
  salary decimal(10,2) default NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM;

#
# Demo data for table `lesson8_employee`
#

INSERT INTO lesson8_employee (id, name, hiredate, notes, salary) VALUES (1, 'Jack', '2004-04-27', '', '1000.00');
INSERT INTO lesson8_employee (id, name, hiredate, notes, salary) VALUES (2, 'Bill', '2000-06-01', 'Test employee', '60.00');
INSERT INTO lesson8_employee (id, name, hiredate, notes, salary) VALUES (3, 'Simon', '2004-02-09', 'Simon the Sourceror', '500.00');

# --------------------------------------------------------

#
# Table structure for table `lesson9_employee`
#

CREATE TABLE lesson9_employee
(
  id int(11) NOT NULL auto_increment,
  name varchar(50) NOT NULL,
  hiredate date,
  notes text,
  salary decimal(10,2),
  PRIMARY KEY  (id)
) TYPE=MyISAM;

#
# Demo data for table `lesson9_employee`
#

INSERT INTO lesson9_employee (id, name, hiredate, notes, salary) VALUES (1, 'Jack', '2004-04-27', '', '1000.00');
INSERT INTO lesson9_employee (id, name, hiredate, notes, salary) VALUES (2, 'Bill', '2000-06-01', 'Test employee', '60.00');
INSERT INTO lesson9_employee (id, name, hiredate, notes, salary) VALUES (3, 'Simon', '2004-02-09', 'Simon the Sourceror', '500.00');
# --------------------------------------------------------

#
# Table structure for table `lesson9_project`
#

CREATE TABLE lesson9_project
(
  id int(11) NOT NULL auto_increment,
  name varchar(50) NOT NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

#
# Demo data for table `lesson9_project`
#

INSERT INTO lesson9_project (id, name) VALUES (1, 'Major Project');
INSERT INTO lesson9_project (id, name) VALUES (2, 'Minor Undertaking');
INSERT INTO lesson9_project (id, name) VALUES (3, 'Super Glue');
# --------------------------------------------------------

#
# Table structure for table `lesson9_employeeproject`
#

CREATE TABLE lesson9_employeeproject
(
  employee_id int(11) NOT NULL,
  project_id int(11) NOT NULL,
  PRIMARY KEY  (employee_id, project_id)
) TYPE=MyISAM;

# --------------------------------------------------------

