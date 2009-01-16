-- MySQL dump 10.9
--
-- Host: localhost    Database: dokeos_main
-- ------------------------------------------------------
-- Server version	4.1.14

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


--
-- Table structure for table user
--

DROP TABLE IF EXISTS user;
CREATE TABLE user (
  user_id int unsigned NOT NULL auto_increment,
  lastname varchar(60) default NULL,
  firstname varchar(60) default NULL,
  username varchar(20) NOT NULL default '',
  password varchar(50) NOT NULL default '',
  auth_source varchar(50) default 'platform',
  email varchar(100) default NULL,
  status tinyint NOT NULL default '5',
  official_code varchar(40) default NULL,
  phone varchar(30) default NULL,
  picture_uri varchar(250) default NULL,
  creator_id int unsigned default NULL,
  competences text,
  diplomas text,
  openarea text,
  teach text,
  productions varchar(250) default NULL,
  chatcall_user_id int unsigned NOT NULL default '0',
  chatcall_date datetime NOT NULL default '0000-00-00 00:00:00',
  chatcall_text varchar(50) NOT NULL default '',
  language varchar(40) default NULL,
  registration_date datetime NOT NULL default '0000-00-00 00:00:00',
  expiration_date datetime NOT NULL default '0000-00-00 00:00:00',
  active tinyint unsigned NOT NULL default 1,
  openid varchar(255) DEFAULT NULL,
  theme varchar(255) DEFAULT NULL,
  hr_dept_id smallint unsigned NOT NULL default 0,
  PRIMARY KEY  (user_id),
  UNIQUE KEY username (username)
);
ALTER TABLE user ADD INDEX (status);

--
-- Dumping data for table user
--

/*!40000 ALTER TABLE user DISABLE KEYS */;
LOCK TABLES user WRITE;
INSERT INTO user (lastname, firstname, username, password, auth_source, email, status, official_code,phone, creator_id, registration_date, expiration_date,active,openid,language) VALUES ('{ADMINLASTNAME}','{ADMINFIRSTNAME}','{ADMINLOGIN}','{ADMINPASSWORD}','{PLATFORM_AUTH_SOURCE}','{ADMINEMAIL}',1,'ADMIN','{ADMINPHONE}',1,NOW(),'0000-00-00 00:00:00','1',NULL,'{ADMINLANGUAGE}');
-- Insert anonymous user
INSERT INTO user (lastname, firstname, username, password, auth_source, email, status, official_code, creator_id, registration_date, expiration_date,active,openid,language) VALUES ('Anonymous', 'Joe', '', '', 'platform', 'anonymous@localhost', 6, 'anonymous', 1, NOW(), '0000-00-00 00:00:00', 1,NULL,'{ADMINLANGUAGE}');
UNLOCK TABLES;
/*!40000 ALTER TABLE user ENABLE KEYS */;

--
-- Table structure for table user_friend
--
CREATE TABLE user_friend(
  id bigint unsigned not null auto_increment,
  user_id int unsigned not null,
  friend_user_id int unsigned not null,
  relation_type int not null default 0,
  PRIMARY KEY(id)
);

ALTER TABLE user_friend ADD INDEX idx_user_friend_user (user_id);
ALTER TABLE user_friend ADD INDEX idx_user_friend_friend_user(friend_user_id);
ALTER TABLE user_friend ADD INDEX idx_user_friend_user_friend_user(user_id,friend_user_id);

--
-- Table structure for table user_friend_relation_type
--
CREATE TABLE user_friend_relation_type(
  id int unsigned not null auto_increment,
  title char(20),
  PRIMARY KEY(id)
);


--
-- Table structure for table admin
--

DROP TABLE IF EXISTS admin;
CREATE TABLE admin (
  user_id int unsigned NOT NULL default '0',
  UNIQUE KEY user_id (user_id)
);

--
-- Dumping data for table admin
--


/*!40000 ALTER TABLE admin DISABLE KEYS */;
LOCK TABLES admin WRITE;
INSERT INTO admin VALUES (1);
UNLOCK TABLES;
/*!40000 ALTER TABLE admin ENABLE KEYS */;

--
-- Table structure for table class
--

DROP TABLE IF EXISTS class;
CREATE TABLE class (
  id mediumint unsigned NOT NULL auto_increment,
  code varchar(40) default '',
  name text NOT NULL,
  PRIMARY KEY  (id)
);

--
-- Dumping data for table class
--


/*!40000 ALTER TABLE class DISABLE KEYS */;
LOCK TABLES class WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE class ENABLE KEYS */;

--
-- Table structure for table class_user
--

DROP TABLE IF EXISTS class_user;
CREATE TABLE class_user (
  class_id mediumint unsigned NOT NULL default '0',
  user_id int unsigned NOT NULL default '0',
  PRIMARY KEY  (class_id,user_id)
);

--
-- Dumping data for table class_user
--


/*!40000 ALTER TABLE class_user DISABLE KEYS */;
LOCK TABLES class_user WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE class_user ENABLE KEYS */;

--
-- Table structure for table course
--

DROP TABLE IF EXISTS course;
CREATE TABLE course (
  code varchar(40) NOT NULL,
  directory varchar(40) default NULL,
  db_name varchar(40) default NULL,
  course_language varchar(20) default NULL,
  title varchar(250) default NULL,
  description text,
  category_code varchar(40) default NULL,
  visibility tinyint default '0',
  show_score int NOT NULL default '1',
  tutor_name varchar(200) default NULL,
  visual_code varchar(40) default NULL,
  department_name varchar(30) default NULL,
  department_url varchar(180) default NULL,
  disk_quota int unsigned default NULL,
  last_visit datetime default NULL,
  last_edit datetime default NULL,
  creation_date datetime default NULL,
  expiration_date datetime default NULL,
  target_course_code varchar(40) default NULL,
  subscribe tinyint NOT NULL default '1',
  unsubscribe tinyint NOT NULL default '1',
  registration_code varchar(255) NOT NULL default '',
  PRIMARY KEY  (code)
);

--
-- Dumping data for table course
--


/*!40000 ALTER TABLE course DISABLE KEYS */;
LOCK TABLES course WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE course ENABLE KEYS */;

--
-- Table structure for table course_category
--

DROP TABLE IF EXISTS course_category;
CREATE TABLE course_category (
  id int unsigned NOT NULL auto_increment,
  name varchar(100) NOT NULL default '',
  code varchar(40) NOT NULL default '',
  parent_id varchar(40) default NULL,
  tree_pos int unsigned default NULL,
  children_count smallint default NULL,
  auth_course_child enum('TRUE','FALSE') default 'TRUE',
  auth_cat_child enum('TRUE','FALSE') default 'TRUE',
  PRIMARY KEY  (id),
  UNIQUE KEY code (code),
  KEY parent_id (parent_id),
  KEY tree_pos (tree_pos)
);

--
-- Dumping data for table course_category
--


/*!40000 ALTER TABLE course_category DISABLE KEYS */;
LOCK TABLES course_category WRITE;
INSERT INTO course_category VALUES (1,'Language skills','LANG',NULL,1,0,'TRUE','TRUE'),(2,'PC Skills','PC',NULL,2,0,'TRUE','TRUE'),(3,'Projects','PROJ',NULL,3,0,'TRUE','TRUE');
UNLOCK TABLES;
/*!40000 ALTER TABLE course_category ENABLE KEYS */;

--
-- Table structure for table course_module
--

DROP TABLE IF EXISTS course_module;
CREATE TABLE course_module (
  id int unsigned NOT NULL auto_increment,
  name varchar(100) NOT NULL,
  link varchar(255) NOT NULL,
  image varchar(100) default NULL,
  `row` int unsigned NOT NULL default '0',
  `column` int unsigned NOT NULL default '0',
  position varchar(20) NOT NULL default 'basic',
  PRIMARY KEY  (id)
);

--
-- Dumping data for table course_module
--


/*!40000 ALTER TABLE course_module DISABLE KEYS */;
LOCK TABLES course_module WRITE;
INSERT INTO course_module VALUES
(1,'calendar_event','calendar/agenda.php','agenda.gif',1,1,'basic'),
(2,'link','link/link.php','links.gif',4,1,'basic'),
(3,'document','document/document.php','documents.gif',3,1,'basic'),
(4,'student_publication','work/work.php','works.gif',3,2,'basic'),
(5,'announcement','announcements/announcements.php','valves.gif',2,1,'basic'),
(6,'user','user/user.php','members.gif',2,3,'basic'),
(7,'forum','forum/index.php','forum.gif',1,2,'basic'),
(8,'quiz','exercice/exercice.php','quiz.gif',2,2,'basic'),
(9,'group','group/group.php','group.gif',3,3,'basic'),
(10,'course_description','course_description/','info.gif',1,3,'basic'),
(11,'chat','chat/chat.php','chat.gif',0,0,'external'),
(12,'dropbox','dropbox/index.php','dropbox.gif',4,2,'basic'),
(13,'tracking','tracking/courseLog.php','statistics.gif',1,3,'courseadmin'),
(14,'homepage_link','link/link.php?action=addlink','npage.gif',1,1,'courseadmin'),
(15,'course_setting','course_info/infocours.php','reference.gif',1,1,'courseadmin'),
(16,'External','','external.gif',0,0,'external'),
(17,'AddedLearnpath','','scormbuilder.gif',0,0,'external'),
(18,'conference','conference/index.php?type=conference','conf.gif',0,0,'external'),
(19,'conference','conference/index.php?type=classroom','conf.gif',0,0,'external'),
(20,'learnpath','newscorm/lp_controller.php','scorm.gif',5,1,'basic'),
(21,'blog','blog/blog.php','blog.gif',1,2,'basic'),
(22,'blog_management','blog/blog_admin.php','blog_admin.gif',1,2,'courseadmin'),
(23,'course_maintenance','course_info/maintenance.php','backup.gif',2,3,'courseadmin'),
(24,'survey','survey/survey_list.php','survey.gif',2,1,'basic'),
(25,'wiki','wiki/index.php','wiki.gif',2,3,'basic'),
(26,'gradebook','gradebook/index.php','gradebook.gif',2,2,'basic'),
(27,'glossary','glossary/index.php','glossary.gif',2,1,'basic'),
(28,'notebook','notebook/index.php','notebook.gif',2,1,'basic');
UNLOCK TABLES;
/*!40000 ALTER TABLE course_module ENABLE KEYS */;

--
-- Table structure for table course_rel_class
--

DROP TABLE IF EXISTS course_rel_class;
CREATE TABLE course_rel_class (
  course_code char(40) NOT NULL,
  class_id mediumint unsigned NOT NULL,
  PRIMARY KEY  (course_code,class_id)
);

--
-- Dumping data for table course_rel_class
--


/*!40000 ALTER TABLE course_rel_class DISABLE KEYS */;
LOCK TABLES course_rel_class WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE course_rel_class ENABLE KEYS */;

--
-- Table structure for table course_rel_user
--

DROP TABLE IF EXISTS course_rel_user;
CREATE TABLE course_rel_user (
  course_code varchar(40) NOT NULL,
  user_id int unsigned NOT NULL default '0',
  status tinyint NOT NULL default '5',
  role varchar(60) default NULL,
  group_id int NOT NULL default '0',
  tutor_id int unsigned NOT NULL default '0',
  sort int default NULL,
  user_course_cat int default '0',
  PRIMARY KEY  (course_code,user_id)
);
ALTER TABLE course_rel_user ADD INDEX (user_id);

--
-- Dumping data for table course_rel_user
--


/*!40000 ALTER TABLE course_rel_user DISABLE KEYS */;
LOCK TABLES course_rel_user WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE course_rel_user ENABLE KEYS */;

--
-- Table structure for table language
--

DROP TABLE IF EXISTS language;
CREATE TABLE language (
  id tinyint unsigned NOT NULL auto_increment,
  original_name varchar(255) default NULL,
  english_name varchar(255) default NULL,
  isocode varchar(10) default NULL,
  dokeos_folder varchar(250) default NULL,
  available tinyint NOT NULL default '1',
  PRIMARY KEY  (id)
);

--
-- Dumping data for table language
--


/*!40000 ALTER TABLE language DISABLE KEYS */;
LOCK TABLES language WRITE;
INSERT INTO language (original_name, english_name, isocode, dokeos_folder, available) VALUES
('Arabija (el)','arabic','ar','arabic',0),
('Asturian','asturian','','asturian',0),
('Balgarski','bulgarian','bg','bulgarian',0),
('Bosanski','bosnian',NULL,'bosnian',1),
('Catal&agrave;','catalan','ca','catalan',0),
('Chinese (simplified)','simpl_chinese','zh','simpl_chinese',0),
('Czech','czech','','czech',0),
('Dansk','danish','da','danish',0),
('Dari','dari','','dari',0),
('Deutsch','german','de','german',1),
('Ellinika','greek','el','greek',0),
('English','english','en','english',1),
('Espa&ntilde;ol','spanish','es','spanish',1),
('Esperanto','esperanto','eo','esperanto',0),
('Euskera','euskera','','euskera',0),
('Farsi','persian','fa','persian',0),
('Fran&ccedil;ais','french','fr','french',1),
('Friulian','friulian','','friulian',0),
('Galego','galician','gl','galician',0),
('Georgian','georgian','','georgian',0),
('Hrvatski','croatian','hr','croatian',0),
('Hebrew','hebrew','','hebrew',0),
('Indonesia (Bahasa I.)','indonesian','id','indonesian',1),
('Italiano','italian','it','italian',1),
('Korean','korean','','korean',0),
('Latvian','latvian','','latvian',0),
('Lithuanian','lithuanian','','lithuanian',0),
('Macedonian','macedonian','','macedonian',0),
('Magyar','hungarian','hu','hungarian',1),
('Melayu (Bahasa M.)','malay','ms','malay',0),
('Nederlands','dutch','nl','dutch',1),
('Nihongo','japanese','ja','japanese',0),
('Norsk','norwegian','no','norwegian',0),
('Pashto','pashto','','pashto',0),
('Polski','polish','pl','polish',0),
('Portugu&ecirc;s (Portugal)','portuguese','pt','portuguese',1),
('Portugu&ecirc;s (Brazil)','brazilian','pt-BR','brazilian',1),
('Romanian','romanian','','romanian',0),
('Runasimi','quechua_cusco','','quechua_cusco',0),
('Russkij','russian','ru','russian',0),
('Slovak','slovak','','slovak',0),
('Slovenscina','slovenian','sl','slovenian',1),
('Srpski','serbian','sr','serbian',0),
('Suomi','finnish','fi','finnish',0),
('Svenska','swedish','sv','swedish',0),
('Thai','thai','th','thai',0),
('T&uuml;rk&ccedil;e','turkce','tr','turkce',0),
('Ukrainian','ukrainian','','ukrainian',0),
('Vi&ecirc;t (Ti&ecirc;ng V.)','vietnamese','vi','vietnamese',0),
('Swahili (kiSw.)','swahili','sw','swahili',0),
('Yoruba','yoruba','','yoruba',0);

UNLOCK TABLES;
/*!40000 ALTER TABLE language ENABLE KEYS */;

--
-- Table structure for table php_session
--

DROP TABLE IF EXISTS php_session;
CREATE TABLE php_session (
  session_id varchar(32) NOT NULL default '',
  session_name varchar(10) NOT NULL default '',
  session_time int NOT NULL default '0',
  session_start int NOT NULL default '0',
  session_value mediumtext NOT NULL,
  PRIMARY KEY  (session_id)
);

--
-- Table structure for table session
--
DROP TABLE IF EXISTS session;
CREATE TABLE session (
  id smallint unsigned NOT NULL auto_increment,
  id_coach int unsigned NOT NULL default '0',
  name char(50) NOT NULL default '',
  nbr_courses smallint unsigned NOT NULL default '0',
  nbr_users mediumint unsigned NOT NULL default '0',
  nbr_classes mediumint unsigned NOT NULL default '0',
  date_start date NOT NULL default '0000-00-00',
  date_end date NOT NULL default '0000-00-00',
  nb_days_access_before_beginning TINYINT UNSIGNED NULL default '0',
  nb_days_access_after_end TINYINT UNSIGNED NULL default '0',
  session_admin_id INT UNSIGNED NOT NULL,
  PRIMARY KEY  (id),
  INDEX (session_admin_id),
  UNIQUE KEY name (name)
);
-- --------------------------------------------------------

--
-- Table structure for table session_rel_course
--
DROP TABLE IF EXISTS session_rel_course;
CREATE TABLE session_rel_course (
  id_session smallint unsigned NOT NULL default '0',
  course_code char(40) NOT NULL default '',
  id_coach int unsigned NOT NULL default '0',
  nbr_users smallint unsigned NOT NULL default '0',
  PRIMARY KEY  (id_session,course_code),
  KEY course_code (course_code)
);

-- --------------------------------------------------------

--
-- Table structure for table session_rel_course_rel_user
--
DROP TABLE IF EXISTS session_rel_course_rel_user;
CREATE TABLE session_rel_course_rel_user (
  id_session smallint unsigned NOT NULL default '0',
  course_code char(40) NOT NULL default '',
  id_user int unsigned NOT NULL default '0',
  PRIMARY KEY  (id_session,course_code,id_user),
  KEY id_user (id_user),
  KEY course_code (course_code)
);

-- --------------------------------------------------------

--
-- Table structure for table session_rel_user
--
DROP TABLE IF EXISTS session_rel_user;
CREATE TABLE session_rel_user (
  id_session mediumint unsigned NOT NULL default '0',
  id_user mediumint unsigned NOT NULL default '0',
  PRIMARY KEY  (id_session,id_user)
);


--
-- Table structure for table settings_current
--

DROP TABLE IF EXISTS settings_current;
CREATE TABLE settings_current (
  id int unsigned NOT NULL auto_increment,
  variable varchar(255) default NULL,
  subkey varchar(255) default NULL,
  type varchar(255) default NULL,
  category varchar(255) default NULL,
  selected_value varchar(255) default NULL,
  title varchar(255) NOT NULL default '',
  comment varchar(255) default NULL,
  scope varchar(50) default NULL,
  subkeytext varchar(255) default NULL,
  access_url int unsigned not null default 1,
  access_url_changeable int unsigned not null default 0,
  PRIMARY KEY id (id),
  INDEX (access_url)
);

ALTER TABLE settings_current ADD UNIQUE unique_setting ( variable , subkey , category ) ;

--
-- Dumping data for table settings_current
--


/*!40000 ALTER TABLE settings_current DISABLE KEYS */;
LOCK TABLES settings_current WRITE;
INSERT INTO settings_current 
(variable, subkey, type, category, selected_value, title, comment, scope, subkeytext)
VALUES
('Institution',NULL,'textfield','Platform','{ORGANISATIONNAME}','InstitutionTitle','InstitutionComment','platform',NULL),
('InstitutionUrl',NULL,'textfield','Platform','{ORGANISATIONURL}','InstitutionUrlTitle','InstitutionUrlComment',NULL,NULL),
('siteName',NULL,'textfield','Platform','{CAMPUSNAME}','SiteNameTitle','SiteNameComment',NULL,NULL),
('emailAdministrator',NULL,'textfield','Platform','{ADMINEMAIL}','emailAdministratorTitle','emailAdministratorComment',NULL,NULL),
('administratorSurname',NULL,'textfield','Platform','{ADMINLASTNAME}','administratorSurnameTitle','administratorSurnameComment',NULL,NULL),
('administratorName',NULL,'textfield','Platform','{ADMINFIRSTNAME}','administratorNameTitle','administratorNameComment',NULL,NULL),
('show_administrator_data',NULL,'radio','Platform','true','ShowAdministratorDataTitle','ShowAdministratorDataComment',NULL,NULL),
('homepage_view',NULL,'radio','Course','activity','HomepageViewTitle','HomepageViewComment',NULL,NULL),
('show_toolshortcuts',NULL,'radio','Course','false','ShowToolShortcutsTitle','ShowToolShortcutsComment',NULL,NULL),
('allow_group_categories',NULL,'radio','Course','false','AllowGroupCategories','AllowGroupCategoriesComment',NULL,NULL),
('server_type',NULL,'radio','Platform','production','ServerStatusTitle','ServerStatusComment',NULL,NULL),
('platformLanguage',NULL,'link','Languages','{PLATFORMLANGUAGE}','PlatformLanguageTitle','PlatformLanguageComment',NULL,NULL),
('showonline','world','checkbox','Platform','true','ShowOnlineTitle','ShowOnlineComment',NULL,'ShowOnlineWorld'),
('showonline','users','checkbox','Platform','true','ShowOnlineTitle','ShowOnlineComment',NULL,'ShowOnlineUsers'),
('showonline','course','checkbox','Platform','true','ShowOnlineTitle','ShowOnlineComment',NULL,'ShowOnlineCourse'),
('profile','name','checkbox','User','false','ProfileChangesTitle','ProfileChangesComment',NULL,'name'),
('profile','officialcode','checkbox','User','false','ProfileChangesTitle','ProfileChangesComment',NULL,'officialcode'),
('profile','email','checkbox','User','false','ProfileChangesTitle','ProfileChangesComment',NULL,'Email'),
('profile','picture','checkbox','User','true','ProfileChangesTitle','ProfileChangesComment',NULL,'UserPicture'),
('profile','login','checkbox','User','false','ProfileChangesTitle','ProfileChangesComment',NULL,'Login'),
('profile','password','checkbox','User','false','ProfileChangesTitle','ProfileChangesComment',NULL,'UserPassword'),
('profile','language','checkbox','User','true','ProfileChangesTitle','ProfileChangesComment',NULL,'Language'),
('default_document_quotum',NULL,'textfield','Course','50000000','DefaultDocumentQuotumTitle','DefaultDocumentQuotumComment',NULL,NULL),
('registration','officialcode','checkbox','User','false','RegistrationRequiredFormsTitle','RegistrationRequiredFormsComment',NULL,'OfficialCode'),
('registration','email','checkbox','User','true','RegistrationRequiredFormsTitle','RegistrationRequiredFormsComment',NULL,'Email'),
('registration','language','checkbox','User','true','RegistrationRequiredFormsTitle','RegistrationRequiredFormsComment',NULL,'Language'),
('default_group_quotum',NULL,'textfield','Course','5000000','DefaultGroupQuotumTitle','DefaultGroupQuotumComment',NULL,NULL),
('allow_registration',NULL,'radio','Platform','{ALLOWSELFREGISTRATION}','AllowRegistrationTitle','AllowRegistrationComment',NULL,NULL),
('allow_registration_as_teacher',NULL,'radio','Platform','{ALLOWTEACHERSELFREGISTRATION}','AllowRegistrationAsTeacherTitle','AllowRegistrationAsTeacherComment',NULL,NULL),
('allow_lostpassword',NULL,'radio','Platform','true','AllowLostPasswordTitle','AllowLostPasswordComment',NULL,NULL),
('allow_user_headings',NULL,'radio','Course','false','AllowUserHeadings','AllowUserHeadingsComment',NULL,NULL),
('course_create_active_tools','course_description','checkbox','Tools','true','CourseCreateActiveToolsTitle','CourseCreateActiveToolsComment',NULL,'CourseDescription'),
('course_create_active_tools','agenda','checkbox','Tools','true','CourseCreateActiveToolsTitle','CourseCreateActiveToolsComment',NULL,'Agenda'),
('course_create_active_tools','documents','checkbox','Tools','true','CourseCreateActiveToolsTitle','CourseCreateActiveToolsComment',NULL,'Documents'),
('course_create_active_tools','learning_path','checkbox','Tools','true','CourseCreateActiveToolsTitle','CourseCreateActiveToolsComment',NULL,'LearningPath'),
('course_create_active_tools','links','checkbox','Tools','true','CourseCreateActiveToolsTitle','CourseCreateActiveToolsComment',NULL,'Links'),
('course_create_active_tools','announcements','checkbox','Tools','true','CourseCreateActiveToolsTitle','CourseCreateActiveToolsComment',NULL,'Announcements'),
('course_create_active_tools','forums','checkbox','Tools','true','CourseCreateActiveToolsTitle','CourseCreateActiveToolsComment',NULL,'Forums'),
('course_create_active_tools','dropbox','checkbox','Tools','true','CourseCreateActiveToolsTitle','CourseCreateActiveToolsComment',NULL,'Dropbox'),
('course_create_active_tools','quiz','checkbox','Tools','true','CourseCreateActiveToolsTitle','CourseCreateActiveToolsComment',NULL,'Quiz'),
('course_create_active_tools','users','checkbox','Tools','true','CourseCreateActiveToolsTitle','CourseCreateActiveToolsComment',NULL,'Users'),
('course_create_active_tools','groups','checkbox','Tools','true','CourseCreateActiveToolsTitle','CourseCreateActiveToolsComment',NULL,'Groups'),
('course_create_active_tools','chat','checkbox','Tools','true','CourseCreateActiveToolsTitle','CourseCreateActiveToolsComment',NULL,'Chat'),
('course_create_active_tools','online_conference','checkbox','Tools','true','CourseCreateActiveToolsTitle','CourseCreateActiveToolsComment',NULL,'OnlineConference'),
('course_create_active_tools','student_publications','checkbox','Tools','true','CourseCreateActiveToolsTitle','CourseCreateActiveToolsComment',NULL,'StudentPublications'),
('allow_personal_agenda',NULL,'radio','User','false','AllowPersonalAgendaTitle','AllowPersonalAgendaComment',NULL,NULL),
('display_coursecode_in_courselist',NULL,'radio','Platform','true','DisplayCourseCodeInCourselistTitle','DisplayCourseCodeInCourselistComment',NULL,NULL),
('display_teacher_in_courselist',NULL,'radio','Platform','true','DisplayTeacherInCourselistTitle','DisplayTeacherInCourselistComment',NULL,NULL),
('use_document_title',NULL,'radio','Tools','false','UseDocumentTitleTitle','UseDocumentTitleComment',NULL,NULL),
('permanently_remove_deleted_files',NULL,'radio','Tools','false','PermanentlyRemoveFilesTitle','PermanentlyRemoveFilesComment',NULL,NULL),
('dropbox_allow_overwrite',NULL,'radio','Tools','true','DropboxAllowOverwriteTitle','DropboxAllowOverwriteComment',NULL,NULL),
('dropbox_max_filesize',NULL,'textfield','Tools','100000000','DropboxMaxFilesizeTitle','DropboxMaxFilesizeComment',NULL,NULL),
('dropbox_allow_just_upload',NULL,'radio','Tools','true','DropboxAllowJustUploadTitle','DropboxAllowJustUploadComment',NULL,NULL),
('dropbox_allow_student_to_student',NULL,'radio','Tools','true','DropboxAllowStudentToStudentTitle','DropboxAllowStudentToStudentComment',NULL,NULL),
('dropbox_allow_group',NULL,'radio','Tools','true','DropboxAllowGroupTitle','DropboxAllowGroupComment',NULL,NULL),
('dropbox_allow_mailing',NULL,'radio','Tools','false','DropboxAllowMailingTitle','DropboxAllowMailingComment',NULL,NULL),
('administratorTelephone',NULL,'textfield','Platform','(000) 001 02 03','administratorTelephoneTitle','administratorTelephoneComment',NULL,NULL),
('extended_profile',NULL,'radio','User','true','ExtendedProfileTitle','ExtendedProfileComment',NULL,NULL),
('student_view_enabled',NULL,'radio','Platform','true','StudentViewEnabledTitle','StudentViewEnabledComment',NULL,NULL),
('show_navigation_menu',NULL,'radio','Course','false','ShowNavigationMenuTitle','ShowNavigationMenuComment',NULL,NULL),
('enable_tool_introduction',NULL,'radio','course','false','EnableToolIntroductionTitle','EnableToolIntroductionComment',NULL,NULL),
('page_after_login', NULL, 'radio','Platform','user_portal.php', 'PageAfterLoginTitle','PageAfterLoginComment', NULL, NULL),
('time_limit_whosonline', NULL, 'textfield','Platform','30', 'TimeLimitWhosonlineTitle','TimeLimitWhosonlineComment', NULL, NULL),
('breadcrumbs_course_homepage', NULL, 'radio','Course','course_title', 'BreadCrumbsCourseHomepageTitle','BreadCrumbsCourseHomepageComment', NULL, NULL),
('example_material_course_creation', NULL, 'radio','Platform','true', 'ExampleMaterialCourseCreationTitle','ExampleMaterialCourseCreationComment', NULL, NULL),
('account_valid_duration',NULL, 'textfield','Platform','3660', 'AccountValidDurationTitle','AccountValidDurationComment', NULL, NULL),
('use_session_mode', NULL, 'radio','Platform','true', 'UseSessionModeTitle','UseSessionModeComment', NULL, NULL),
('allow_email_editor', NULL, 'radio', 'Tools', 'false', 'AllowEmailEditorTitle', 'AllowEmailEditorComment', NULL, NULL),
('registered', NULL, 'textfield', NULL, 'false', NULL, NULL, NULL, NULL),
('donotlistcampus', NULL, 'textfield', NULL, 'false', NULL, NULL, NULL, NULL),
('show_email_addresses', NULL,'radio','Platform','false','ShowEmailAddresses','ShowEmailAddressesComment',NULL,NULL),
('profile','phone','checkbox','User','false','ProfileChangesTitle','ProfileChangesComment',NULL,'phone'),
('service_visio', 'active', 'radio',NULL,'false', 'VisioEnable','', NULL, NULL),
('service_visio', 'visio_host', 'textfield',NULL,'', 'VisioHost','', NULL, NULL),
('service_visio', 'visio_port', 'textfield',NULL,'1935', 'VisioPort','', NULL, NULL),
('service_visio', 'visio_pass', 'textfield',NULL,'', 'VisioPassword','', NULL, NULL),
('service_ppt2lp', 'active', 'radio',NULL,'false', 'ppt2lp_actived','', NULL, NULL),
('service_ppt2lp', 'host', 'textfield', NULL, NULL, 'Host', NULL, NULL, NULL),
('service_ppt2lp', 'port', 'textfield', NULL, 2002, 'Port', NULL, NULL, NULL),
('service_ppt2lp', 'user', 'textfield', NULL, NULL, 'UserOnHost', NULL, NULL, NULL),
('service_ppt2lp', 'ftp_password', 'textfield', NULL, NULL, 'FtpPassword', NULL, NULL, NULL),
('service_ppt2lp', 'path_to_lzx', 'textfield', NULL, NULL, '', NULL, NULL, NULL),
('service_ppt2lp', 'size', 'radio', NULL, '720x540', '', NULL, NULL, NULL),
('wcag_anysurfer_public_pages', NULL, 'radio','Platform','false','PublicPagesComplyToWAITitle','PublicPagesComplyToWAIComment', NULL, NULL),
('stylesheets', NULL, 'textfield','stylesheets','public_admin','',NULL, NULL, NULL),
('upload_extensions_list_type', NULL, 'radio', 'Security', 'blacklist', 'UploadExtensionsListType', 'UploadExtensionsListTypeComment', NULL, NULL),
('upload_extensions_blacklist', NULL, 'textfield', 'Security', '', 'UploadExtensionsBlacklist', 'UploadExtensionsBlacklistComment', NULL, NULL),
('upload_extensions_whitelist', NULL, 'textfield', 'Security', 'htm;html;jpg;jpeg;gif;png;swf;avi;mpg;mpeg', 'UploadExtensionsWhitelist', 'UploadExtensionsWhitelistComment', NULL, NULL),
('upload_extensions_skip', NULL, 'radio', 'Security', 'true', 'UploadExtensionsSkip', 'UploadExtensionsSkipComment', NULL, NULL),
('upload_extensions_replace_by', NULL, 'textfield', 'Security', 'dangerous', 'UploadExtensionsReplaceBy', 'UploadExtensionsReplaceByComment', NULL, NULL),
('show_number_of_courses', NULL, 'radio','Platform','false', 'ShowNumberOfCourses','ShowNumberOfCoursesComment', NULL, NULL),
('show_empty_course_categories', NULL, 'radio','Platform','true', 'ShowEmptyCourseCategories','ShowEmptyCourseCategoriesComment', NULL, NULL),
('show_back_link_on_top_of_tree', NULL, 'radio','Platform','false', 'ShowBackLinkOnTopOfCourseTree','ShowBackLinkOnTopOfCourseTreeComment', NULL, NULL),
('show_different_course_language', NULL, 'radio','Platform','true', 'ShowDifferentCourseLanguage','ShowDifferentCourseLanguageComment', NULL, NULL),
('split_users_upload_directory', NULL, 'radio','Tuning','false', 'SplitUsersUploadDirectory','SplitUsersUploadDirectoryComment', NULL, NULL),
('hide_dltt_markup', NULL, 'radio','Platform','true', 'HideDLTTMarkup','HideDLTTMarkupComment', NULL, NULL),
('display_categories_on_homepage',NULL,'radio','Platform','false','DisplayCategoriesOnHomepageTitle','DisplayCategoriesOnHomepageComment',NULL,NULL),
('permissions_for_new_directories', NULL, 'textfield', 'Security', '0777', 'PermissionsForNewDirs', 'PermissionsForNewDirsComment', NULL, NULL),
('permissions_for_new_files', NULL, 'textfield', 'Security', '0666', 'PermissionsForNewFiles', 'PermissionsForNewFilesComment', NULL, NULL),
('show_tabs', 'campus_homepage', 'checkbox', 'Platform', 'true', 'ShowTabsTitle','ShowTabsComment',NULL,'TabsCampusHomepage'),
('show_tabs', 'my_courses', 'checkbox', 'Platform', 'true', 'ShowTabsTitle','ShowTabsComment',NULL,'TabsMyCourses'),
('show_tabs', 'reporting', 'checkbox', 'Platform', 'true', 'ShowTabsTitle','ShowTabsComment',NULL,'TabsReporting'),
('show_tabs', 'platform_administration', 'checkbox', 'Platform', 'true', 'ShowTabsTitle','ShowTabsComment',NULL,'TabsPlatformAdministration'),
('show_tabs', 'my_agenda', 'checkbox', 'Platform', 'true', 'ShowTabsTitle','ShowTabsComment',NULL,'TabsMyAgenda'), 
('show_tabs', 'my_profile', 'checkbox', 'Platform', 'true', 'ShowTabsTitle','ShowTabsComment',NULL,'TabsMyProfile'),
('default_forum_view', NULL, 'radio', 'Course', 'flat', 'DefaultForumViewTitle','DefaultForumViewComment',NULL,NULL),
('platform_charset',NULL,'textfield','Platform','iso-8859-15','PlatformCharsetTitle','PlatformCharsetComment','platform',NULL),
('noreply_email_address', '', 'textfield', 'Platform', '', 'NoReplyEmailAddress', 'NoReplyEmailAddressComment', NULL, NULL),
('survey_email_sender_noreply', '', 'radio', 'Course', 'coach', 'SurveyEmailSenderNoReply', 'SurveyEmailSenderNoReplyComment', NULL, NULL),
('openid_authentication',NULL,'radio','Security','false','OpenIdAuthentication','OpenIdAuthenticationComment',NULL,NULL),
('profile','openid','checkbox','User','false','ProfileChangesTitle','ProfileChangesComment',NULL,'OpenIDURL'),
('gradebook_enable',NULL,'radio','Gradebook','false','GradebookActivation','GradebookActivationComment',NULL,NULL),
('show_tabs','my_gradebook','checkbox','Platform','true','ShowTabsTitle','ShowTabsComment',NULL,'TabsMyGradebook'),
('gradebook_score_display_coloring','my_display_coloring','checkbox','Gradebook','false','GradebookScoreDisplayColoring','GradebookScoreDisplayColoringComment',NULL,'TabsGradebookEnableColoring'),
('gradebook_score_display_custom','my_display_custom','checkbox','Gradebook','false','GradebookScoreDisplayCustom','GradebookScoreDisplayCustomComment',NULL,'TabsGradebookEnableCustom'),
('gradebook_score_display_colorsplit',NULL,'textfield','Gradebook','50','GradebookScoreDisplayColorSplit','GradebookScoreDisplayColorSplitComment',NULL,NULL),
('gradebook_score_display_upperlimit','my_display_upperlimit','checkbox','Gradebook','false','GradebookScoreDisplayUpperLimit','GradebookScoreDisplayUpperLimitComment',NULL,'TabsGradebookEnableUpperLimit'),
('user_selected_theme',NULL,'radio','Platform','false','UserThemeSelection','UserThemeSelectionComment',NULL,NULL),
('profile','theme','checkbox','User','false','ProfileChangesTitle','ProfileChangesComment',NULL,'UserTheme'),
('allow_course_theme',NULL,'radio','Course','true','AllowCourseThemeTitle','AllowCourseThemeComment',NULL,NULL),
('display_mini_month_calendar',NULL,'radio','Tools', 'true', 'DisplayMiniMonthCalendarTitle', 'DisplayMiniMonthCalendarComment', NULL, NULL),
('display_upcoming_events',NULL,'radio','Tools','true','DisplayUpcomingEventsTitle','DisplayUpcomingEventsComment',NULL,NULL),
('number_of_upcoming_events',NULL,'textfield','Tools','1','NumberOfUpcomingEventsTitle','NumberOfUpcomingEventsComment',NULL,NULL),
('show_closed_courses',NULL,'radio','Platform','false','ShowClosedCoursesTitle','ShowClosedCoursesComment',NULL,NULL),
('ldap_main_server_address', NULL, 'textfield', 'LDAP', 'localhost', 'LDAPMainServerAddressTitle', 'LDAPMainServerAddressComment', NULL, NULL),
('ldap_main_server_port', NULL, 'textfield', 'LDAP', '389', 'LDAPMainServerPortTitle', 'LDAPMainServerPortComment', NULL, NULL),
('ldap_domain', NULL, 'textfield', 'LDAP', 'dc=nodomain', 'LDAPDomainTitle', 'LDAPDomainComment', NULL, NULL),
('ldap_replicate_server_address', NULL, 'textfield', 'LDAP', 'localhost', 'LDAPReplicateServerAddressTitle', 'LDAPReplicateServerAddressComment', NULL, NULL),
('ldap_replicate_server_port', NULL, 'textfield', 'LDAP', '389', 'LDAPReplicateServerPortTitle', 'LDAPReplicateServerPortComment', NULL, NULL),
('ldap_search_term', NULL, 'textfield', 'LDAP', '', 'LDAPSearchTermTitle', 'LDAPSearchTermComment', NULL, NULL),
('ldap_version', NULL, 'radio', 'LDAP', '3', 'LDAPVersionTitle', 'LDAPVersionComment', NULL, ''),
('ldap_filled_tutor_field', NULL, 'textfield', 'LDAP', 'employeenumber', 'LDAPFilledTutorFieldTitle', 'LDAPFilledTutorFieldComment', NULL, ''),
('ldap_authentication_login', NULL, 'textfield', 'LDAP', '', 'LDAPAuthenticationLoginTitle', 'LDAPAuthenticationLoginComment', NULL, ''),
('ldap_authentication_password', NULL, 'textfield', 'LDAP', '', 'LDAPAuthenticationPasswordTitle', 'LDAPAuthenticationPasswordComment', NULL, ''),
('service_visio', 'visio_use_rtmpt', 'radio',null,'false', 'VisioUseRtmptTitle','VisioUseRtmptComment', NULL, NULL), 
('extendedprofile_registration', 'mycomptetences', 'checkbox','User','false', 'ExtendedProfileRegistrationTitle','ExtendedProfileRegistrationComment', NULL, 'MyCompetences'),
('extendedprofile_registration', 'mydiplomas', 'checkbox','User','false', 'ExtendedProfileRegistrationTitle','ExtendedProfileRegistrationComment', NULL, 'MyDiplomas'),
('extendedprofile_registration', 'myteach', 'checkbox','User','false', 'ExtendedProfileRegistrationTitle','ExtendedProfileRegistrationComment', NULL, 'MyTeach'),
('extendedprofile_registration', 'mypersonalopenarea', 'checkbox','User','false', 'ExtendedProfileRegistrationTitle','ExtendedProfileRegistrationComment', NULL, 'MyPersonalOpenArea'),
('extendedprofile_registrationrequired', 'mycomptetences', 'checkbox','User','false', 'ExtendedProfileRegistrationRequiredTitle','ExtendedProfileRegistrationRequiredComment', NULL, 'MyCompetences'),
('extendedprofile_registrationrequired', 'mydiplomas', 'checkbox','User','false', 'ExtendedProfileRegistrationRequiredTitle','ExtendedProfileRegistrationRequiredComment', NULL, 'MyDiplomas'),
('extendedprofile_registrationrequired', 'myteach', 'checkbox','User','false', 'ExtendedProfileRegistrationRequiredTitle','ExtendedProfileRegistrationRequiredComment', NULL, 'MyTeach'),
('extendedprofile_registrationrequired', 'mypersonalopenarea', 'checkbox','User','false', 'ExtendedProfileRegistrationRequiredTitle','ExtendedProfileRegistrationRequiredComment', NULL, 'MyPersonalOpenArea'),
('ldap_filled_tutor_field_value', NULL, 'textfield', 'LDAP', '', 'LDAPFilledTutorFieldValueTitle', 'LDAPFilledTutorFieldValueComment', NULL, ''),
('registration','phone','textfield','User','false','RegistrationRequiredFormsTitle','RegistrationRequiredFormsComment',NULL,'Phone'),
('add_users_by_coach',NULL,'radio','Security','false','AddUsersByCoachTitle','AddUsersByCoachComment',NULL,NULL),
('extend_rights_for_coach',NULL,'radio','Security','false','ExtendRightsForCoachTitle','ExtendRightsForCoachComment',NULL,NULL),
('extend_rights_for_coach_on_survey',NULL,'radio','Security','true','ExtendRightsForCoachOnSurveyTitle','ExtendRightsForCoachOnSurveyComment',NULL,NULL),
('course_create_active_tools','wiki','checkbox','Tools','true','CourseCreateActiveToolsTitle','CourseCreateActiveToolsComment',NULL,'Wiki'),
('show_session_coach', NULL, 'radio','Platform','false', 'ShowSessionCoachTitle','ShowSessionCoachComment', NULL, NULL),
('course_create_active_tools','gradebook','checkbox','Tools','true','CourseCreateActiveToolsTitle','CourseCreateActiveToolsComment',NULL,'Gradebook'),
('allow_users_to_create_courses',NULL,'radio','Platform','true','AllowUsersToCreateCoursesTitle','AllowUsersToCreateCoursesComment',NULL,NULL),
('course_create_active_tools','survey','checkbox','Tools','true','CourseCreateActiveToolsTitle','CourseCreateActiveToolsComment',NULL,'Survey'),
('course_create_active_tools','glossary','checkbox','Tools','true','CourseCreateActiveToolsTitle','CourseCreateActiveToolsComment',NULL,'Glossary'),
('course_create_active_tools','notebook','checkbox','Tools','true','CourseCreateActiveToolsTitle','CourseCreateActiveToolsComment',NULL,'Notebook'),
('advanced_filemanager',NULL,'radio','Platform','true','AdvancedFileManagerTitle','AdvancedFileManagerComment',NULL,NULL),
('allow_reservation', NULL, 'radio', 'Tools', 'false', 'AllowReservationTitle', 'AllowReservationComment', NULL, NULL);
UNLOCK TABLES;
/*!40000 ALTER TABLE settings_current ENABLE KEYS */;

--
-- Table structure for table settings_options
--

DROP TABLE IF EXISTS settings_options;
CREATE TABLE settings_options (
  id int unsigned NOT NULL auto_increment,
  variable varchar(255) default NULL,
  value varchar(255) default NULL,
  display_text varchar(255) NOT NULL default '',
  PRIMARY KEY  (id),
  UNIQUE KEY id (id)
);

ALTER TABLE settings_options ADD UNIQUE unique_setting_option ( variable , value) ;

--
-- Dumping data for table settings_options
--


/*!40000 ALTER TABLE settings_options DISABLE KEYS */;
LOCK TABLES settings_options WRITE;
INSERT INTO settings_options 
(variable, value, display_text)
VALUES
('show_administrator_data','true','Yes'),
('show_administrator_data','false','No'),
('homepage_view','activity','HomepageViewActivity'),
('homepage_view','2column','HomepageView2column'),
('homepage_view','3column','HomepageView3columngradebook_result_log'),
('show_toolshortcuts','true','Yes'),
('show_toolshortcuts','false','No'),
('allow_group_categories','true','Yes'),
('allow_group_categories','false','No'),
('server_type','production','ProductionServer'),
('server_type','test','TestServer'),
('allow_name_change','true','Yes'),
('allow_name_change','false','No'),
('allow_officialcode_change','true','Yes'),
('allow_officialcode_change','false','No'),
('allow_registration','true','Yes'),
('allow_registration','false','No'),
('allow_registration','approval','AfterApproval'),
('allow_registration_as_teacher','true','Yes'),
('allow_registration_as_teacher','false','No'),
('allow_lostpassword','true','Yes'),
('allow_lostpassword','false','No'),
('allow_user_headings','true','Yes'),
('allow_user_headings','false','No'),
('allow_personal_agenda','true','Yes'),
('allow_personal_agenda','false','No'),
('display_coursecode_in_courselist','true','Yes'),
('display_coursecode_in_courselist','false','No'),
('display_teacher_in_courselist','true','Yes'),
('display_teacher_in_courselist','false','No'),
('use_document_title','true','Yes'),
('use_document_title','false','No'),
('permanently_remove_deleted_files','true','YesWillDeletePermanently'),
('permanently_remove_deleted_files','false','NoWillDeletePermanently'),
('dropbox_allow_overwrite','true','Yes'),
('dropbox_allow_overwrite','false','No'),
('dropbox_allow_just_upload','true','Yes'),
('dropbox_allow_just_upload','false','No'),
('dropbox_allow_student_to_student','true','Yes'),
('dropbox_allow_student_to_student','false','No'),
('dropbox_allow_group','true','Yes'),
('dropbox_allow_group','false','No'),
('dropbox_allow_mailing','true','Yes'),
('dropbox_allow_mailing','false','No'),
('extended_profile','true','Yes'),
('extended_profile','false','No'),
('student_view_enabled','true','Yes'),
('student_view_enabled','false','No'),
('show_navigation_menu','false','No'),
('show_navigation_menu','icons','IconsOnly'),
('show_navigation_menu','text','TextOnly'),
('show_navigation_menu','iconstext','IconsText'),
('enable_tool_introduction','true','Yes'),
('enable_tool_introduction','false','No'),
('page_after_login', 'index.php', 'CampusHomepage'),
('page_after_login', 'user_portal.php', 'MyCourses'),
('breadcrumbs_course_homepage', 'get_lang', 'CourseHomepage'),
('breadcrumbs_course_homepage', 'course_code', 'CourseCode'),
('breadcrumbs_course_homepage', 'course_title', 'CourseTitle'),
('example_material_course_creation', 'true', 'Yes'),
('example_material_course_creation', 'false', 'No'),
('use_session_mode', 'true', 'Yes'),
('use_session_mode', 'false', 'No'),
('allow_email_editor', 'true' ,'Yes'),
('allow_email_editor', 'false', 'No'),
('show_email_addresses','true','Yes'),
('show_email_addresses','false','No'),
('wcag_anysurfer_public_pages', 'true', 'Yes'),
('wcag_anysurfer_public_pages', 'false', 'No'),
('upload_extensions_list_type', 'blacklist', 'Blacklist'),
('upload_extensions_list_type', 'whitelist', 'Whitelist'),
('upload_extensions_skip', 'true', 'Remove'),
('upload_extensions_skip', 'false', 'Rename'),
('show_number_of_courses', 'true', 'Yes'),
('show_number_of_courses', 'false', 'No'),
('show_empty_course_categories', 'true', 'Yes'),
('show_empty_course_categories', 'false', 'No'),
('show_back_link_on_top_of_tree', 'true', 'Yes'),
('show_back_link_on_top_of_tree', 'false', 'No'),
('show_different_course_language', 'true', 'Yes'),
('show_different_course_language', 'false', 'No'), 
('split_users_upload_directory', 'true', 'Yes'),
('split_users_upload_directory', 'false', 'No'),
('hide_dltt_markup', 'false', 'No'),
('hide_dltt_markup', 'true', 'Yes'),
('display_categories_on_homepage','true','Yes'),
('display_categories_on_homepage','false','No'),
('default_forum_view', 'flat', 'Flat'),
('default_forum_view', 'threaded', 'Threaded'),
('default_forum_view', 'nested', 'Nested'),
('survey_email_sender_noreply', 'coach', 'CourseCoachEmailSender'),
('survey_email_sender_noreply', 'noreply', 'NoReplyEmailSender'),
('openid_authentication','true','Yes'),
('openid_authentication','false','No'),
('gradebook_enable','true','Yes'),
('gradebook_enable','false','No'),
('user_selected_theme','true','Yes'),
('user_selected_theme','false','No'),
('allow_course_theme','true','Yes'),
('allow_course_theme','false','No'),
('display_mini_month_calendar', 'true', 'Yes'),
('display_mini_month_calendar', 'false', 'No'),
('display_upcoming_events', 'true', 'Yes'),
('display_upcoming_events', 'false', 'No'),
('show_closed_courses', 'true', 'Yes'),
('show_closed_courses', 'false', 'No'),
('ldap_version', '2', 'LDAPVersion2'),
('ldap_version', '3', 'LDAPVersion3'),
('visio_use_rtmpt','true','Yes'),
('visio_use_rtmpt','false','No'),
('add_users_by_coach', 'true', 'Yes'),
('add_users_by_coach', 'false', 'No'),
('extend_rights_for_coach', 'true', 'Yes'),
('extend_rights_for_coach', 'false', 'No'),
('extend_rights_for_coach_on_survey', 'true', 'Yes'),
('extend_rights_for_coach_on_survey', 'false', 'No'),
('show_session_coach', 'true', 'Yes'),
('show_session_coach', 'false', 'No'),
('allow_users_to_create_courses','true','Yes'),
('allow_users_to_create_courses','false','No'),
('breadcrumbs_course_homepage', 'session_name_and_course_title', 'SessionNameAndCourseTitle'),
('advanced_filemanager','true','Yes'),
('advanced_filemanager','false','No'),
('allow_reservation', 'true', 'Yes'),
('allow_reservation', 'false', 'No');
UNLOCK TABLES;

/*!40000 ALTER TABLE settings_options ENABLE KEYS */;

--
-- Table structure for table sys_announcement
--

DROP TABLE IF EXISTS sys_announcement;
CREATE TABLE sys_announcement (
  id int unsigned NOT NULL auto_increment,
  date_start datetime NOT NULL default '0000-00-00 00:00:00',
  date_end datetime NOT NULL default '0000-00-00 00:00:00',
  visible_teacher tinyint NOT NULL default 0,
  visible_student tinyint NOT NULL default 0,
  visible_guest tinyint NOT NULL default 0,
  title varchar(250) NOT NULL default '',
  content text NOT NULL,
  lang varchar(70) NULL default NULL,
  PRIMARY KEY  (id)
);

--
-- Dumping data for table sys_announcement
--


/*!40000 ALTER TABLE sys_announcement DISABLE KEYS */;
LOCK TABLES sys_announcement WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE sys_announcement ENABLE KEYS */;

-- 
-- Table structure for shared_survey
-- 

DROP TABLE IF EXISTS shared_survey;
CREATE TABLE shared_survey (
  survey_id int unsigned NOT NULL auto_increment,
  code varchar(20) default NULL,
  title text default NULL,
  subtitle text default NULL,
  author varchar(250) default NULL,
  lang varchar(20) default NULL,
  template varchar(20) default NULL,
  intro text,
  surveythanks text,
  creation_date datetime NOT NULL default '0000-00-00 00:00:00',
  course_code varchar(40) NOT NULL default '',
  PRIMARY KEY  (survey_id),
  UNIQUE KEY id (survey_id)
);

-- --------------------------------------------------------

-- 
-- Table structure for shared_survey_question
-- 

DROP TABLE IF EXISTS shared_survey_question;
CREATE TABLE shared_survey_question (
  question_id int NOT NULL auto_increment,
  survey_id int NOT NULL default '0',
  survey_question text NOT NULL,
  survey_question_comment text NOT NULL,
  type varchar(250) NOT NULL default '',
  display varchar(10) NOT NULL default '',
  sort int NOT NULL default '0',
  code varchar(40) NOT NULL default '',
  max_value int NOT NULL,
  PRIMARY KEY  (question_id)
);

-- --------------------------------------------------------

-- 
-- Table structure for shared_survey_question_option
-- 

DROP TABLE IF EXISTS shared_survey_question_option;
CREATE TABLE shared_survey_question_option (
  question_option_id int NOT NULL auto_increment,
  question_id int NOT NULL default '0',
  survey_id int NOT NULL default '0',
  option_text text NOT NULL,
  sort int NOT NULL default '0',
  PRIMARY KEY  (question_option_id)
);


-- --------------------------------------------------------

-- 
-- Table structure for templates (User's FCKEditor templates)
-- 

DROP TABLE IF EXISTS templates;
CREATE TABLE templates (
  id int NOT NULL auto_increment,
  title varchar(100) NOT NULL,
  description varchar(250) NOT NULL,
  course_code varchar(40) NOT NULL,
  user_id int NOT NULL,
  ref_doc int NOT NULL,
  PRIMARY KEY  (id)
);

-- 

-- --------------------------------------------------------

-- 
-- Table structure of openid_association (keep info on openid servers)
-- 

DROP TABLE IF EXISTS openid_association;
CREATE TABLE IF NOT EXISTS openid_association (
  id int NOT NULL auto_increment,
  idp_endpoint_uri text NOT NULL,
  session_type varchar(30) NOT NULL,
  assoc_handle text NOT NULL,
  assoc_type text NOT NULL,
  expires_in bigint NOT NULL,
  mac_key text NOT NULL,
  created bigint NOT NULL,
  PRIMARY KEY  (id)
);
--
-- --------------------------------------------------------
--
-- Tables for gradebook
--
DROP TABLE IF EXISTS gradebook_category;
CREATE TABLE gradebook_category (
  id int NOT NULL auto_increment,
  name text NOT NULL,
  description text,
  user_id int NOT NULL,
  course_code varchar(40) default NULL,
  parent_id int default NULL,
  weight smallint NOT NULL,
  visible tinyint NOT NULL,
  certif_min_score int DEFAULT NULL,
  PRIMARY KEY  (id)
);
DROP TABLE IF EXISTS gradebook_evaluation;
CREATE TABLE gradebook_evaluation (
  id int unsigned NOT NULL auto_increment,
  name text NOT NULL,
  description text,
  user_id int NOT NULL,
  course_code varchar(40) default NULL,
  category_id int default NULL,
  date int default 0,
  weight smallint NOT NULL,
  max float unsigned NOT NULL,
  visible tinyint NOT NULL,
  PRIMARY KEY  (id)
);
DROP TABLE IF EXISTS gradebook_link;
CREATE TABLE gradebook_link (
  id int NOT NULL auto_increment,
  type int NOT NULL,
  ref_id int NOT NULL,
  user_id int NOT NULL,
  course_code varchar(40) NOT NULL,
  category_id int NOT NULL,
  date int default NULL,
  weight smallint NOT NULL,
  visible tinyint NOT NULL,
  PRIMARY KEY  (id)
);
DROP TABLE IF EXISTS gradebook_result;
CREATE TABLE gradebook_result (
  id int NOT NULL auto_increment,
  user_id int NOT NULL,
  evaluation_id int NOT NULL,
  date int NOT NULL,
  score float unsigned default NULL,
  PRIMARY KEY  (id)
);
DROP TABLE IF EXISTS gradebook_score_display;
CREATE TABLE gradebook_score_display (
  id int NOT NULL auto_increment,
  score float unsigned NOT NULL,
  display varchar(40) NOT NULL,
  PRIMARY KEY (id)
);
DROP TABLE IF EXISTS user_field;
CREATE TABLE user_field (
	id	INT NOT NULL auto_increment,
	field_type int NOT NULL DEFAULT 1,
	field_variable	varchar(64) NOT NULL,
	field_display_text	varchar(64),
	field_default_value text,
	field_order int,
	field_visible tinyint default 0,
	field_changeable tinyint default 0,
	tms	TIMESTAMP,	
	PRIMARY KEY(id)
);
DROP TABLE IF EXISTS user_field_options;
CREATE TABLE user_field_options (
	id	int NOT NULL auto_increment,
	field_id int	NOT NULL,
	option_value	text,
	option_display_text varchar(64),
	option_order int,
	tms	TIMESTAMP,
	PRIMARY KEY (id)
);
DROP TABLE IF EXISTS user_field_values;
CREATE TABLE user_field_values(
	id	int	NOT NULL auto_increment,
	user_id	int	unsigned NOT NULL,
	field_id int NOT NULL,
	field_value	text,
	tms TIMESTAMP,
	PRIMARY KEY(id)
);

DROP TABLE IF EXISTS access_url;
CREATE TABLE access_url(
	id	int	unsigned NOT NULL auto_increment,
	url	varchar(255) NOT NULL,
	description text,
	active	int unsigned not null default 0,
	created_by	int	not null,
	tms TIMESTAMP,
	PRIMARY KEY (id)
);

INSERT INTO access_url(url, description, active, created_by) VALUES ('http://localhost/',' ',1,1);

DROP TABLE IF EXISTS access_url_rel_user;
CREATE TABLE access_url_rel_user (
  access_url_id int unsigned NOT NULL,
  user_id int unsigned NOT NULL,  
  PRIMARY KEY (access_url_id, user_id)
);

ALTER TABLE access_url_rel_user ADD INDEX idx_access_url_rel_user_user (user_id);
ALTER TABLE access_url_rel_user ADD INDEX idx_access_url_rel_user_access_url(access_url_id);
ALTER TABLE access_url_rel_user ADD INDEX idx_access_url_rel_user_access_url_user (user_id,access_url_id);

INSERT INTO access_url_rel_user(access_url_id, user_id) VALUES (1,1);
INSERT INTO access_url_rel_user(access_url_id, user_id) VALUES (1,2);

ALTER TABLE gradebook_category ADD session_id int DEFAULT NULL;

DROP TABLE IF EXISTS gradebook_result_log;
CREATE TABLE gradebook_result_log (
	id int NOT NULL auto_increment,
	id_result int NOT NULL,
	user_id int NOT NULL,
	evaluation_id int NOT NULL,
	date_log datetime default '0000-00-00 00:00:00',
	score float unsigned default NULL,
	PRIMARY KEY(id)
);

DROP TABLE IF EXISTS gradebook_linkeval_log;
CREATE TABLE gradebook_linkeval_log (
	id int NOT NULL auto_increment,
	id_linkeval_log int NOT NULL,
	name text,
	description text,
	date_log int,
	weight smallint default NULL,
	visible tinyint default NULL,
	type varchar(20) NOT NULL,
	user_id_log int NOT NULL,
	PRIMARY KEY  (id)
);

CREATE TABLE  sys_calendar (
  id int unsigned NOT NULL auto_increment,
  title varchar(200) NOT NULL,
  content text,
  start_date datetime NOT NULL default '0000-00-00 00:00:00',
  end_date datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (id)
);

CREATE TABLE IF NOT EXISTS system_template (
  id int UNSIGNED NOT NULL auto_increment,
  title varchar(250) NOT NULL,
  comment text NOT NULL,
  image varchar(250) NOT NULL,
  content text NOT NULL,
  PRIMARY KEY  (id)
);

--
-- --------------------------------------------------------
--
-- Tables for reservation
--


-- 
-- Table structure for table reservation category 
-- 

CREATE TABLE reservation_category (
   id  int unsigned NOT NULL auto_increment,
   parent_id  int NOT NULL default 0,
   name  varchar(128) NOT NULL default '',
  PRIMARY KEY  ( id )
);

-- --------------------------------------------------------

-- 
-- Table structure for table reservation category_rights 
-- 

CREATE TABLE  reservation_category_rights  (
   category_id  int NOT NULL default 0,
   class_id  int NOT NULL default 0,
   m_items  tinyint NOT NULL default 0
);

-- --------------------------------------------------------

-- 
-- Table structure for table  item reservation
-- 

CREATE TABLE  reservation_item  (
   id  int unsigned NOT NULL auto_increment,
   category_id  int unsigned NOT NULL default 0,
   course_code  varchar(40) NOT NULL default '',
   name  varchar(128) NOT NULL default '',
   description  text NOT NULL,
   blackout  tinyint NOT NULL default 0,
   creator  int unsigned NOT NULL default 0,
  PRIMARY KEY  ( id )
);

-- --------------------------------------------------------

-- 
-- Table structure for table reservation item_rights 
-- 

CREATE TABLE  reservation_item_rights  (
   item_id  int unsigned NOT NULL default 0,
   class_id  int unsigned NOT NULL default 0,
   edit_right  tinyint unsigned NOT NULL default 0,
   delete_right  tinyint unsigned NOT NULL default 0,
   m_reservation  tinyint unsigned NOT NULL default 0,
   view_right  tinyint NOT NULL default 0,
  PRIMARY KEY  ( item_id , class_id )
);

-- --------------------------------------------------------

-- 
-- Table structure for main reservation table
-- 

CREATE TABLE  reservation_main  (
   id  int unsigned NOT NULL auto_increment,
   subid  int unsigned NOT NULL default 0,
   item_id  int unsigned NOT NULL default 0,
   auto_accept  tinyint unsigned NOT NULL default 0,
   max_users  int unsigned NOT NULL default 1,
   start_at  datetime NOT NULL default '0000-00-00 00:00:00',
   end_at  datetime NOT NULL default '0000-00-00 00:00:00',
   subscribe_from  datetime NOT NULL default '0000-00-00 00:00:00',
   subscribe_until  datetime NOT NULL default '0000-00-00 00:00:00',
   subscribers  int unsigned NOT NULL default 0,
   notes  text NOT NULL,
   timepicker  tinyint NOT NULL default 0,
   timepicker_min  int NOT NULL default 0,
   timepicker_max  int NOT NULL default 0,
  PRIMARY KEY  ( id )
);

-- --------------------------------------------------------

-- 
-- Table structure for reservation subscription table
-- 

CREATE TABLE  reservation_subscription  (
   dummy  int unsigned NOT NULL auto_increment,
   user_id  int unsigned NOT NULL default 0,
   reservation_id  int unsigned NOT NULL default 0,
   accepted  tinyint unsigned NOT NULL default 0,
   start_at  datetime NOT NULL default '0000-00-00 00:00:00',
   end_at  datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  ( dummy )
);

