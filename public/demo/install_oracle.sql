--
-- DEMO INSTALLATION FOR ORACLE DATABASES
-- Please read doc/INSTALL before you start
--

--
-- Table structure for table `lesson1_employee`
--

CREATE TABLE lesson1_employee
(
  id NUMBER NOT NULL,
  name VARCHAR2(50) NOT NULL,
  hiredate DATE,
  notes CLOB,
  salary NUMBER(10,2),
  CONSTRAINT pk_lesson1_emp PRIMARY KEY (id)
);

--
-- Demo data for table `lesson1_employee`
--

INSERT INTO lesson1_employee (id, name, hiredate, notes, salary) VALUES (1, 'Jack', TO_DATE('2004-04-27', 'YYYY-MM-DD'), '', 1000.00);
INSERT INTO lesson1_employee (id, name, hiredate, notes, salary) VALUES (2, 'Bill', TO_DATE('2000-06-01', 'YYYY-MM-DD'), 'Test employee', 60.00);
INSERT INTO lesson1_employee (id, name, hiredate, notes, salary) VALUES (3, 'Simon', TO_DATE('2004-02-09', 'YYYY-MM-DD'), 'Simon the Sourceror', 500.00);

-- ATK will insert records based on a sequence.
CREATE SEQUENCE seq_lesson1_employee START WITH 4;

--
-- Table structure for table `lesson2_department`
--

CREATE TABLE lesson2_department
(
  id NUMBER NOT NULL,
  name VARCHAR2(100) NOT NULL,
  CONSTRAINT pk_lesson2_dept PRIMARY KEY (id)
);

--
-- Demo data for table `lesson2_department`
--

INSERT INTO lesson2_department (id, name) VALUES (1, 'Sales department');
INSERT INTO lesson2_department (id, name) VALUES (2, 'Support');

--
-- Table structure for table `lesson2_employee`
--

CREATE TABLE lesson2_employee
(
  id NUMBER NOT NULL,
  name VARCHAR2(50) NOT NULL,
  department_id NUMBER,
  hiredate DATE,
  notes CLOB,
  salary NUMBER(10,2),
  manager_id NUMBER,
  CONSTRAINT pk_lesson2_emp PRIMARY KEY (id),
  CONSTRAINT fk_lesson2_empman FOREIGN KEY(manager_id) REFERENCES lesson2_employee(id) ON DELETE SET NULL,
  CONSTRAINT fk_lesson2_empdept FOREIGN KEY(department_id) REFERENCES lesson2_department(id) ON DELETE SET NULL
);

--
-- Demo data for table `lesson2_employee`
--

INSERT INTO lesson2_employee (id, name, department_id, hiredate, notes, salary, manager_id) VALUES (1, 'Jack', 1, TO_DATE('2004-05-03', 'YYYY-MM-DD'), 'test', 1000.00, NULL);
INSERT INTO lesson2_employee (id, name, department_id, hiredate, notes, salary, manager_id) VALUES (2, 'Joe', 2, TO_DATE('2004-05-03', 'YYYY-MM-DD'), 'test employee', 500.00, 1);

-- ATK will insert records based on a sequence.
CREATE SEQUENCE seq_lesson2_employee START WITH 3;
CREATE SEQUENCE seq_lesson2_department START WITH 3;


--
-- Table structure for table `lesson3_department`
--

CREATE TABLE lesson3_department
(
  id NUMBER NOT NULL,
  name VARCHAR2(100) NOT NULL,
  is_hiring NUMBER(1,0) NOT NULL,
  CONSTRAINT pk_lesson3_dept PRIMARY KEY (id)
);

--
-- Demo data for table `lesson3_department`
--

INSERT INTO lesson3_department (id, name, is_hiring) VALUES (1, 'Sales', 1);
INSERT INTO lesson3_department (id, name, is_hiring) VALUES (2, 'Support', 0);

--
-- Table structure for table `lesson3_employee`
--

CREATE TABLE lesson3_employee
(
  id NUMBER NOT NULL,
  name VARCHAR2(50) NOT NULL,
  department_id NUMBER,
  hiredate DATE,
  notes CLOB,
  salary NUMBER(10,2),
  manager_id NUMBER,
  CONSTRAINT pk_lesson3_emp PRIMARY KEY (id),
  CONSTRAINT fk_lesson3_empman FOREIGN KEY(manager_id) REFERENCES lesson3_employee(id) ON DELETE SET NULL,
  CONSTRAINT fk_lesson3_empdept FOREIGN KEY(department_id) REFERENCES lesson3_department(id) ON DELETE SET NULL
);

--
-- Demo data for table `lesson3_employee`
--

INSERT INTO lesson3_employee (id, name, department_id, hiredate, notes, salary, manager_id) VALUES (1, 'Jack The Manager', 1, TO_DATE('2004-05-03', 'YYYY-MM-DD'), '', 1000.00, NULL);
INSERT INTO lesson3_employee (id, name, department_id, hiredate, notes, salary, manager_id) VALUES (2, 'Joe The Employee', 1, TO_DATE('2004-05-03', 'YYYY-MM-DD'), '', 500.00, 1);
INSERT INTO lesson3_employee (id, name, department_id, hiredate, notes, salary, manager_id) VALUES (3, 'Jill The Rich Employee', 1, TO_DATE('2004-05-03', 'YYYY-MM-DD'), '', 2000.00, 1);

-- ATK will insert records based on a sequence.
CREATE SEQUENCE seq_lesson3_employee START WITH 4;
CREATE SEQUENCE seq_lesson3_department START WITH 3;


--
-- Table structure for table `lesson4_department`
--

CREATE TABLE lesson4_department
(
  id NUMBER NOT NULL,
  name VARCHAR2(100) NOT NULL,
  is_hiring NUMBER(1,0) NOT NULL,
  CONSTRAINT pk_lesson4_dept PRIMARY KEY (id)
);

--
-- Demo data for table `lesson4_department`
--

INSERT INTO lesson4_department (id, name, is_hiring) VALUES (1, 'Sales', 1);
INSERT INTO lesson4_department (id, name, is_hiring) VALUES (2, 'Support', 0);

--
-- Table structure for table `lesson4_employee`
--

CREATE TABLE lesson4_employee
(
  id NUMBER NOT NULL,
  name VARCHAR2(50) NOT NULL,
  email VARCHAR2(100),
  department_id NUMBER,
  hiredate DATE,
  notes CLOB,
  salary NUMBER(10,2),
  manager_id NUMBER,
  CONSTRAINT pk_lesson4_emp PRIMARY KEY (id),
  CONSTRAINT fk_lesson4_empman FOREIGN KEY(manager_id) REFERENCES lesson4_employee(id) ON DELETE SET NULL,
  CONSTRAINT fk_lesson4_empdept FOREIGN KEY(department_id) REFERENCES lesson4_department(id) ON DELETE SET NULL
);

--
-- Demo data for table `lesson4_employee`
--

INSERT INTO lesson4_employee (id, name, email, department_id, hiredate, notes, salary, manager_id) VALUES (1, 'Jack The Manager', NULL, 1, TO_DATE('2004-05-03', 'YYYY-MM-DD'), '', 1000.00, NULL);
INSERT INTO lesson4_employee (id, name, email, department_id, hiredate, notes, salary, manager_id) VALUES (2, 'Joe The Employee', NULL, 1, TO_DATE('2004-05-03', 'YYYY-MM-DD'), '', 500.00, 1);
INSERT INTO lesson4_employee (id, name, email, department_id, hiredate, notes, salary, manager_id) VALUES (3, 'Jill The Rich Employee', '', 1, TO_DATE('2004-05-03', 'YYYY-MM-DD'), '', 2100.00, 1);

-- ATK will insert records based on a sequence.
CREATE SEQUENCE seq_lesson4_employee START WITH 4;
CREATE SEQUENCE seq_lesson4_department START WITH 3;

--
-- Table structure for table `lesson5_profile`
--

CREATE TABLE lesson5_profile
(
  id NUMBER NOT NULL,
  name VARCHAR2(50) NOT NULL,
  CONSTRAINT pk_lesson5_profile PRIMARY KEY (id)
);

--
-- Demo data for table `lesson5_profile`
--

INSERT INTO lesson5_profile (id, name) VALUES (1, 'Projectmanagers');


--
-- Table structure for table `lesson5_accessright`
--

CREATE TABLE lesson5_accessright
(
  entity VARCHAR2(100) NOT NULL,
  action VARCHAR2(25) NOT NULL,
  profile_id NUMBER NOT NULL,
  CONSTRAINT pk_accessright PRIMARY KEY  (entity,action,profile_id),
  CONSTRAINT fk_accessright_profile FOREIGN KEY (profile_id) REFERENCES lesson5_profile(id) ON DELETE CASCADE
);

--
-- Demo data for table `lesson5_accessright`
--

INSERT INTO lesson5_accessright (entity, action, profile_id) VALUES ('lesson5.department', 'add', '1');
INSERT INTO lesson5_accessright (entity, action, profile_id) VALUES ('lesson5.department', 'admin', '1');
INSERT INTO lesson5_accessright (entity, action, profile_id) VALUES ('lesson5.department', 'delete', '1');
INSERT INTO lesson5_accessright (entity, action, profile_id) VALUES ('lesson5.department', 'edit', '1');
INSERT INTO lesson5_accessright (entity, action, profile_id) VALUES ('lesson5.employee', 'add', '1');
INSERT INTO lesson5_accessright (entity, action, profile_id) VALUES ('lesson5.employee', 'admin', '1');
INSERT INTO lesson5_accessright (entity, action, profile_id) VALUES ('lesson5.employee', 'delete', '1');
INSERT INTO lesson5_accessright (entity, action, profile_id) VALUES ('lesson5.employee', 'edit', '1');
INSERT INTO lesson5_accessright (entity, action, profile_id) VALUES ('lesson5.profile', 'admin', '1');

--
-- Table structure for table `lesson5_department`
--

CREATE TABLE lesson5_department
(
  id NUMBER NOT NULL,
  name VARCHAR2(100) NOT NULL,
  is_hiring NUMBER(1,0) NOT NULL,
  CONSTRAINT pk_lesson5_dept PRIMARY KEY (id)
);

--
-- Demo data for table `lesson5_department`
--

INSERT INTO lesson5_department (id, name, is_hiring) VALUES (1, 'Sales', 1);
INSERT INTO lesson5_department (id, name, is_hiring) VALUES (2, 'Support', 0);

--
-- Table structure for table `lesson5_employee`
--

CREATE TABLE lesson5_employee
(
  id NUMBER NOT NULL,
  login VARCHAR2(25) NOT NULL,
  name VARCHAR2(50) NOT NULL,
  password VARCHAR2(50),
  email VARCHAR2(100),
  department_id NUMBER,
  hiredate DATE,
  notes CLOB,
  salary NUMBER(10,2),
  manager_id NUMBER,
  profile_id NUMBER,
  CONSTRAINT pk_lesson5_emp PRIMARY KEY (id),
  CONSTRAINT fk_lesson5_empman FOREIGN KEY(manager_id) REFERENCES lesson5_employee(id) ON DELETE SET NULL,
  CONSTRAINT fk_lesson5_empdept FOREIGN KEY(department_id) REFERENCES lesson5_department(id) ON DELETE SET NULL,
  CONSTRAINT fk_lesson2_empprofile FOREIGN KEY(profile_id) REFERENCES lesson5_profile(id) ON DELETE SET NULL
);

--
-- Demo data for table `lesson5_employee`
--

INSERT INTO lesson5_employee (id, login, name, password, email, department_id, hiredate, notes, salary, manager_id, profile_id) VALUES (1, 'jack', 'Jack The Manager', '098f6bcd4621d373cade4e832627b4f6', '', 1, TO_DATE('2004-05-03', 'YYYY-MM-DD'), '', 1000.00, NULL, 1);
INSERT INTO lesson5_employee (id, login, name, password, email, department_id, hiredate, notes, salary, manager_id, profile_id) VALUES (2, 'joe', 'Joe The Employee', NULL, NULL, 1, TO_DATE('2004-05-03', 'YYYY-MM-DD'), '', 500.00, 1, NULL);
INSERT INTO lesson5_employee (id, login, name, password, email, department_id, hiredate, notes, salary, manager_id, profile_id) VALUES (3, 'jill', 'Jill The Rich Employee', NULL, NULL, 1, TO_DATE('2004-05-03', 'YYYY-MM-DD'), '', 2000.00, 1, NULL);

-- ATK will insert records based on a sequence.
CREATE SEQUENCE seq_lesson5_department START WITH 3;
CREATE SEQUENCE seq_lesson5_employee START WITH 4;
CREATE SEQUENCE seq_lesson5_profile START WITH 2;


--
-- Table structure for table `lesson6_department`
--

CREATE TABLE lesson6_department
(
  id NUMBER NOT NULL,
  name VARCHAR2(100) NOT NULL,
  is_hiring NUMBER(1,0) NOT NULL,
  CONSTRAINT pk_lesson6_dept PRIMARY KEY (id)
);

--
-- Demo data for table `lesson6_department`
--

INSERT INTO lesson6_department (id, name) VALUES (1, 'Sales department');
INSERT INTO lesson6_department (id, name) VALUES (2, 'Support');

-- ATK will insert records based on a sequence.
CREATE SEQUENCE seq_lesson6_department START WITH 3;

--
-- Table structure for table `lesson6_employee1`
--

CREATE TABLE lesson6_employee1
(
  id NUMBER NOT NULL,
  name VARCHAR2(50) NOT NULL,
  hiredate DATE,
  notes CLOB,
  salary NUMBER(10,2),
  CONSTRAINT pk_lesson6_emp1 PRIMARY KEY (id)
);

--
-- Demo data for table `lesson6_employee1`
--

INSERT INTO lesson6_employee1 (id, name, hiredate, notes, salary) VALUES (1, 'Jack', TO_DATE('2004-04-27', 'YYYY-MM-DD'), '', 1000.00);
INSERT INTO lesson6_employee1 (id, name, hiredate, notes, salary) VALUES (2, 'Bill', TO_DATE('2000-06-01', 'YYYY-MM-DD'), 'Test employee', 60.00);
INSERT INTO lesson6_employee1 (id, name, hiredate, notes, salary) VALUES (3, 'Simon', TO_DATE('2004-02-09', 'YYYY-MM-DD'), 'Simon the Sourceror', 500.00);

-- ATK will insert records based on a sequence.
CREATE SEQUENCE seq_lesson6_employee1 START WITH 4;

--
-- Table structure for table `lesson6_employee2`
--

CREATE TABLE lesson6_employee2
(
  id NUMBER NOT NULL,
  name VARCHAR2(50) NOT NULL,
  lesson6_department_id NUMBER,
  hiredate DATE,
  notes CLOB,
  salary NUMBER(10,2),
  CONSTRAINT pk_lesson6_emp2 PRIMARY KEY (id),
  CONSTRAINT fk_lesson6_emp2dept FOREIGN KEY(lesson6_department_id) REFERENCES lesson6_department(id) ON DELETE SET NULL
);

--
-- Demo data for table `lesson6_employee2`
--

INSERT INTO lesson6_employee2 (id, name, hiredate, notes, salary, lesson6_department_id) VALUES (1, 'Jack', TO_DATE('2004-04-27', 'YYYY-MM-DD'), '', 1000.00,1);
INSERT INTO lesson6_employee2 (id, name, hiredate, notes, salary, lesson6_department_id) VALUES (2, 'Bill', TO_DATE('2000-06-01', 'YYYY-MM-DD'), 'Test employee', 60.00,2);
INSERT INTO lesson6_employee2 (id, name, hiredate, notes, salary, lesson6_department_id) VALUES (3, 'Simon', TO_DATE('2004-02-09', 'YYYY-MM-DD'), 'Simon the Sourceror', 500.00,1);

-- ATK will insert records based on a sequence.
CREATE SEQUENCE seq_lesson6_employee2 START WITH 4;


--
-- Table structure for table `lesson6_employee3`
--

CREATE TABLE lesson6_employee3
(
  id NUMBER NOT NULL,
  name VARCHAR2(50) NOT NULL,
  hiredate DATE,
  notes CLOB,
  salary NUMBER(10,2),
  CONSTRAINT pk_lesson6_emp3 PRIMARY KEY (id)
);

--
-- Demo data for table `lesson6_employee3`
--

INSERT INTO lesson6_employee3 (id, name, hiredate, notes, salary) VALUES (1, 'Jack', TO_DATE('2004-04-27', 'YYYY-MM-DD'), '', 1000.00);
INSERT INTO lesson6_employee3 (id, name, hiredate, notes, salary) VALUES (2, 'Bill', TO_DATE('2000-06-01', 'YYYY-MM-DD'), 'Test employee', 60.00);
INSERT INTO lesson6_employee3 (id, name, hiredate, notes, salary) VALUES (3, 'Simon', TO_DATE('2004-02-09', 'YYYY-MM-DD'), 'Simon the Sourceror', 500.00);

-- ATK will insert records based on a sequence.
CREATE SEQUENCE seq_lesson6_employee3 START WITH 4;


--
-- Table structure for table `lesson6_employee4`
--

CREATE TABLE lesson6_employee4
(
  id NUMBER NOT NULL,
  name VARCHAR2(50) NOT NULL,
  lesson6_department_id NUMBER,
  hiredate DATE,
  notes CLOB,
  salary NUMBER(10,2),
  CONSTRAINT pk_lesson6_emp4 PRIMARY KEY (id),
  CONSTRAINT fk_lesson6_emp4dept FOREIGN KEY(lesson6_department_id) REFERENCES lesson6_department(id) ON DELETE SET NULL
);

--
-- Demo data for table `lesson6_employee4`
--

INSERT INTO lesson6_employee4 (id, name, hiredate, notes, salary, lesson6_department_id) VALUES (1, 'Jack', TO_DATE('2004-04-27', 'YYYY-MM-DD'), '', 1000.00,1);
INSERT INTO lesson6_employee4 (id, name, hiredate, notes, salary, lesson6_department_id) VALUES (2, 'Bill', TO_DATE('2000-06-01', 'YYYY-MM-DD'), 'Test employee', 60.00,2);
INSERT INTO lesson6_employee4 (id, name, hiredate, notes, salary, lesson6_department_id) VALUES (3, 'Simon', TO_DATE('2004-02-09', 'YYYY-MM-DD'), 'Simon the Sourceror', 500.00,1);

-- ATK will insert records based on a sequence.
CREATE SEQUENCE seq_lesson6_employee4 START WITH 4;

--
-- Table structure for table `lesson7_category`
--

CREATE TABLE lesson7_category
(
  cat_id NUMBER NOT NULL,
  title VARCHAR2(50) NOT NULL,
  parent_cat_id NUMBER,
  CONSTRAINT pk_lesson7_cat PRIMARY KEY (cat_id),
  CONSTRAINT fk_lesson7_cat_par FOREIGN KEY (parent_cat_id) REFERENCES lesson7_category(cat_id) ON DELETE CASCADE
);

--
-- Demo data for table `lesson7_category`
--

INSERT INTO lesson7_category (cat_id, title, parent_cat_id) VALUES (1, 'Test', NULL);
INSERT INTO lesson7_category (cat_id, title, parent_cat_id) VALUES (2, 'Test', 1);
INSERT INTO lesson7_category (cat_id, title, parent_cat_id) VALUES (4, 'Test2', NULL);
INSERT INTO lesson7_category (cat_id, title, parent_cat_id) VALUES (5, 'Test2 item', 4);
INSERT INTO lesson7_category (cat_id, title, parent_cat_id) VALUES (6, 'Test2 item', 4);

-- ATK will insert records based on a sequence.
CREATE SEQUENCE seq_lesson7_category START WITH 5;

--
-- Table structure for table `lesson7_translation`
--

CREATE TABLE lesson7_translation
(
  id NUMBER NOT NULL,
  name_nl VARCHAR2(200),
  name_de VARCHAR2(200),
  name_en VARCHAR2(200),
  number_nl NUMBER,
  number_de NUMBER,
  number_en NUMBER,
  notes_nl CLOB,
  notes_de CLOB,
  notes_en CLOB,
  htmlnotes_nl CLOB,
  htmlnotes_de CLOB,
  htmlnotes_en CLOB,
  CONSTRAINT pk_lesson7_translation PRIMARY KEY (id)
);

-- ATK will insert records based on a sequence.
CREATE SEQUENCE seq_lesson7_translation START WITH 1;

--
-- Table structure for table `lesson7_translation_mr`
--

CREATE TABLE lesson7_translation_mr
(
  id NUMBER NOT NULL,
  name VARCHAR2(200),
  numbervalue NUMBER,
  notes CLOB,
  htmlnotes CLOB,
  lng VARCHAR2(10) NOT NULL DEFAULT 'EN',
  CONSTRAINT pk_lesson7_transmr PRIMARY KEY (id, lng)
);

-- ATK will insert records based on a sequence.
CREATE SEQUENCE seq_lesson7_translation_mr START WITH 1;

--
-- Table structure for table `lesson8_employee`
--
CREATE TABLE lesson8_employee
(
  id NUMBER NOT NULL,
  name VARCHAR2(50) NOT NULL,
  hiredate DATE,
  notes CLOB,
  salary NUMBER(10,2),
  CONSTRAINT pk_lesson8_emp PRIMARY KEY (id)
);

--
-- Demo data for table `lesson8_employee`
--

INSERT INTO lesson8_employee (id, name, hiredate, notes, salary) VALUES (1, 'Jack', TO_DATE('2004-04-27', 'YYYY-MM-DD'), '', 1000.00);
INSERT INTO lesson8_employee (id, name, hiredate, notes, salary) VALUES (2, 'Bill', TO_DATE('2000-06-01', 'YYYY-MM-DD'), 'Test employee', 60.00);
INSERT INTO lesson8_employee (id, name, hiredate, notes, salary) VALUES (3, 'Simon', TO_DATE('2004-02-09', 'YYYY-MM-DD'), 'Simon the Sourceror', 500.00);


-- ATK will insert records based on a sequence.
CREATE SEQUENCE seq_lesson8_employee START WITH 4;


--
-- Table structure for table `lesson9_employee`
--

CREATE TABLE lesson9_employee
(
  id NUMBER NOT NULL,
  name VARCHAR2(50) NOT NULL,
  hiredate DATE,
  notes CLOB,
  salary NUMBER(10,2),
  CONSTRAINT pk_lesson9_emp PRIMARY KEY  (id)
);

CREATE SEQUENCE seq_lesson9_employee START WITH 4;

--
-- Demo data for table `lesson9_employee`
--

INSERT INTO lesson9_employee (id, name, hiredate, notes, salary) VALUES (1, 'Jack', TO_DATE('2004-04-27', 'YYYY-MM-DD'), '', 1000.00);
INSERT INTO lesson9_employee (id, name, hiredate, notes, salary) VALUES (2, 'Bill', TO_DATE('2000-06-01', 'YYYY-MM-DD'), 'Test employee', 60.00);
INSERT INTO lesson9_employee (id, name, hiredate, notes, salary) VALUES (3, 'Simon', TO_DATE('2004-02-09', 'YYYY-MM-DD'), 'Simon the Sourceror', 500.00);

--
-- Table structure for table `lesson9_project`
--

CREATE TABLE lesson9_project
(
  id NUMBER NOT NULL,
  name VARCHAR2(50) NOT NULL,
  CONSTRAINT pk_lesson9_proj PRIMARY KEY  (id)
);

--
-- Demo data for table `lesson9_project`
--

INSERT INTO lesson9_project (id, name) VALUES (1, 'Major Project');
INSERT INTO lesson9_project (id, name) VALUES (2, 'Minor Undertaking');
INSERT INTO lesson9_project (id, name) VALUES (3, 'Super Glue');

CREATE SEQUENCE seq_lesson9_project START WITH 4;

--
-- Table structure for table `lesson9_employeeproject`
--

CREATE TABLE lesson9_employeeproject
(
  employee_id NUMBER NOT NULL,
  project_id NUMBER NOT NULL,
  CONSTRAINT pk_lesson9_empprj PRIMARY KEY  (employee_id, project_id)
);

