-- This script updates the databases structure before migrating the data from
-- version 1.8.5 to version 1.8.6
-- it is intended as a standalone script, however, because of the multiple
-- databases related difficulties, it should be parsed by a PHP script in
-- order to connect to and update the right databases.
-- There is one line per query, allowing the PHP function file() to read
-- all lines separately into an array. The xxMAINxx-type markers are there
-- to tell the PHP script which database we're talking about.
-- By always using the keyword "TABLE" in the queries, we should be able
-- to retrieve and modify the table name from the PHP script if needed, which
-- will allow us to deal with the unique-database-type installations
--
-- This first part is for the main database
-- xxMAINxx
ALTER TABLE settings_current ADD UNIQUE unique_setting (variable,subkey,category);
ALTER TABLE settings_options ADD UNIQUE unique_setting_option (variable,value);
INSERT INTO settings_current (variable, subkey,type,category,selected_value,title,comment,scope,subkeytext)VALUES ('registration', 'phone', 'textfield', 'User', 'false', 'RegistrationRequiredFormsTitle','RegistrationRequiredFormsComment', NULL, 'Phone');
ALTER TABLE php_session CHANGE session_value session_value MEDIUMTEXT NOT NULL;
INSERT INTO settings_current (variable, subkey,type,category,selected_value,title,comment,scope,subkeytext)VALUES ('add_users_by_coach',NULL,'radio','Security','false','AddUsersByCoachTitle','AddUsersByCoachComment',NULL,NULL);
INSERT INTO settings_options (variable, value, display_text) VALUES ('add_users_by_coach', 'true', 'Yes');
INSERT INTO settings_options (variable, value, display_text) VALUES ('add_users_by_coach', 'false', 'No');
ALTER TABLE session ADD nb_days_access_before_beginning TINYINT NULL DEFAULT '0' AFTER date_end , ADD nb_days_access_after_end TINYINT NULL DEFAULT '0' AFTER nb_days_access_before_beginning ;
ALTER TABLE course_rel_user ADD INDEX (user_id);
INSERT INTO settings_current (variable, subkey, type, category, selected_value, title, comment, scope, subkeytext, access_url, access_url_changeable) VALUES ('course_create_active_tools', 'wiki', 'checkbox', 'Tools', 'true', 'CourseCreateActiveToolsTitle', 'CourseCreateActiveToolsComment', NULL, 'Wiki', 1, 0);
INSERT INTO settings_current (variable, subkey,type,category,selected_value,title,comment,scope,subkeytext)VALUES ('extend_rights_for_coach',NULL,'radio','Security','false','ExtendRightsForCoachTitle','ExtendRightsForCoachComment',NULL,NULL);
INSERT INTO settings_options (variable, value, display_text) VALUES ('extend_rights_for_coach', 'true', 'Yes');
INSERT INTO settings_options (variable, value, display_text) VALUES ('extend_rights_for_coach', 'false', 'No');
INSERT INTO settings_current (variable, subkey,type,category,selected_value,title,comment,scope,subkeytext)VALUES ('extend_rights_for_coach_on_surveys',NULL,'radio','Security','false','ExtendRightsForCoachOnSurveyTitle','ExtendRightsForCoachOnSurveyComment',NULL,NULL);
INSERT INTO settings_options (variable, value, display_text) VALUES ('extend_rights_for_coach_on_surveys', 'true', 'Yes');
INSERT INTO settings_options (variable, value, display_text) VALUES ('extend_rights_for_coach_on_surveys', 'false', 'No');
INSERT INTO settings_current (variable, subkey,type,category,selected_value,title,comment,scope,subkeytext)VALUES ('show_session_coach',NULL,'radio','Platform','false','ShowSessionCoachTitle','ShowSessionCoachComment',NULL,NULL);
INSERT INTO settings_options (variable, value, display_text) VALUES ('show_session_coach', 'true', 'Yes');
INSERT INTO settings_options (variable, value, display_text) VALUES ('show_session_coach', 'false', 'No');
INSERT INTO settings_current (variable, subkey, type, category, selected_value, title, comment, scope, subkeytext, access_url, access_url_changeable) VALUES ('course_create_active_tools','gradebook','checkbox','Tools','true','CourseCreateActiveToolsTitle','CourseCreateActiveToolsComment',NULL,'Gradebook',1,0);
INSERT INTO course_module (name, link, image, `row`, `column`, position) VALUES ('wiki','wiki/index.php','wiki.gif',2,3,'basic');
INSERT INTO course_module (name, link, image, `row`, `column`, position) VALUES ('gradebook','gradebook/index.php','gradebook.gif',2,2,'basic');
ALTER TABLE gradebook_category ADD session_id int DEFAULT NULL;
CREATE TABLE gradebook_result_log (id int NOT NULL auto_increment,id_result int NOT NULL,user_id int NOT NULL,evaluation_id int NOT NULL,date_log datetime default '0000-00-00 00:00:00',score float unsigned default NULL,PRIMARY KEY(id));
CREATE TABLE gradebook_linkeval_log (id int NOT NULL auto_increment,id_linkeval_log int NOT NULL,name text,description text,date_log int,weight smallint default NULL,visible tinyint default NULL,type varchar(20) NOT NULL,user_id_log int NOT NULL,PRIMARY KEY  (id));
INSERT INTO course_module (name, link, image, `row`, `column`, position) VALUES ('glossary','glossary/index.php','glossary.gif',2,1,'basic');
INSERT INTO settings_current (variable, subkey, type, category, selected_value, title, comment, scope, subkeytext, access_url, access_url_changeable) VALUES ('course_create_active_tools','glossary','checkbox','Tools','true','CourseCreateActiveToolsTitle','CourseCreateActiveToolsComment',NULL,'Glossary',1,0);
INSERT INTO settings_current (variable, subkey, type, category, selected_value, title, comment, scope, subkeytext, access_url, access_url_changeable) VALUES ('course_create_active_tools','notebook','checkbox','Tools','true','CourseCreateActiveToolsTitle','CourseCreateActiveToolsComment',NULL,'Notebook',1,0);
INSERT INTO settings_current (variable, subkey, type, category, selected_value, title, comment, scope, subkeytext, access_url, access_url_changeable) VALUES ('allow_users_to_create_courses',NULL,'radio','Course','true','AllowUsersToCreateCoursesTitle','AllowUsersToCreateCoursesComment',NULL,NULL,1,0);
INSERT INTO settings_options (variable, value, display_text) VALUES ('allow_users_to_create_courses','true','Yes'),('allow_users_to_create_courses','false','No');
INSERT INTO settings_current (variable, subkey, type, category, selected_value, title, comment, scope, subkeytext, access_url, access_url_changeable) VALUES ('course_create_active_tools','survey','checkbox','Tools','true','CourseCreateActiveToolsTitle','CourseCreateActiveToolsComment',NULL,'Survey',1,0),
ALTER TABLE user_field_values CHANGE user_id user_id int unsigned not null;
UPDATE TABLE settings_options SET display_text = 'YesWillDeletePermanently' WHERE variable = 'permanently_remove_deleted_files' and value = 'true';
UPDATE TABLE settings_options SET display_text = 'NoWillDeletePermanently' WHERE variable = 'permanently_remove_deleted_files' and value = 'false';
INSERT INTO settings_options (variable, value, display_text) VALUES ('breadcrumbs_course_homepage','session_name_and_course_title','SessionNameAndCourseTitle');
INSERT INTO course_module (name, link, image, `row`, `column`, position) VALUES ('notebook','notebook/index.php','notebook.gif',2,1,'basic');
CREATE TABLE  sys_calendar (  id int unsigned NOT NULL auto_increment,  title varchar(200) NOT NULL,  content text,  start_date datetime NOT NULL default '0000-00-00 00:00:00',  end_date datetime NOT NULL default '0000-00-00 00:00:00',  PRIMARY KEY  (id));
CREATE TABLE IF NOT EXISTS system_template (id int UNSIGNED NOT NULL auto_increment,  title varchar(250) NOT NULL,  comment text NOT NULL,  image varchar(250) NOT NULL,  content text NOT NULL,  PRIMARY KEY  (id));
CREATE TABLE reservation_category (id int unsigned NOT NULL auto_increment,  parent_id  int NOT NULL default 0,   name  varchar(128) NOT NULL default '',  PRIMARY KEY  ( id ));
CREATE TABLE reservation_category_rights (category_id  int NOT NULL default 0,   class_id  int NOT NULL default 0,   m_items  tinyint NOT NULL default 0);
CREATE TABLE reservation_item  (id int unsigned NOT NULL auto_increment,   category_id  int unsigned NOT NULL default 0,   course_code  varchar(40) NOT NULL default '',   name  varchar(128) NOT NULL default '',   description  text NOT NULL,   blackout  tinyint NOT NULL default 0,   creator  int unsigned NOT NULL default 0,  PRIMARY KEY  ( id ));
CREATE TABLE reservation_item_rights (item_id  int unsigned NOT NULL default 0,   class_id  int unsigned NOT NULL default 0,   edit_right  tinyint unsigned NOT NULL default 0,   delete_right  tinyint unsigned NOT NULL default 0,   m_reservation  tinyint unsigned NOT NULL default 0,   view_right  tinyint NOT NULL default 0,  PRIMARY KEY  ( item_id , class_id ));
CREATE TABLE reservation_main  (id int unsigned NOT NULL auto_increment,   subid  int unsigned NOT NULL default 0,   item_id  int unsigned NOT NULL default 0,   auto_accept  tinyint unsigned NOT NULL default 0,   max_users  int unsigned NOT NULL default 1,   start_at  datetime NOT NULL default '0000-00-00 00:00:00',   end_at  datetime NOT NULL default '0000-00-00 00:00:00',   subscribe_from  datetime NOT NULL default '0000-00-00 00:00:00',   subscribe_until  datetime NOT NULL default '0000-00-00 00:00:00',   subscribers  int unsigned NOT NULL default 0,   notes  text NOT NULL,   timepicker  tinyint NOT NULL default 0,   timepicker_min  int NOT NULL default 0,   timepicker_max  int NOT NULL default 0,  PRIMARY KEY  ( id ));
CREATE TABLE reservation_subscription  (dummy  int unsigned NOT NULL auto_increment,   user_id  int unsigned NOT NULL default 0,   reservation_id  int unsigned NOT NULL default 0,   accepted  tinyint unsigned NOT NULL default 0,   start_at  datetime NOT NULL default '0000-00-00 00:00:00',   end_at  datetime NOT NULL default '0000-00-00 00:00:00',  PRIMARY KEY  ( dummy ));
INSERT INTO settings_current (variable, subkey, type, category, selected_value, title, comment, scope, subkeytext) VALUES ('allow_reservation', NULL, 'radio', 'Tools', 'false', 'AllowReservationTitle', 'AllowReservationComment', NULL, NULL);
INSERT INTO settings_options (variable, value, display_text) VALUES ('allow_reservation', 'true', 'Yes');
INSERT INTO settings_options (variable, value, display_text) VALUES ('allow_reservation', 'false', 'No');
CREATE TABLE access_url_rel_user (access_url_id int unsigned NOT NULL, user_id int unsigned NOT NULL, PRIMARY KEY (access_url_id, user_id));
ALTER TABLE access_url_rel_user ADD INDEX idx_access_url_rel_user_user (user_id);
ALTER TABLE access_url_rel_user ADD INDEX idx_access_url_rel_user_access_url(access_url_id);
ALTER TABLE access_url_rel_user ADD INDEX idx_access_url_rel_user_access_url_user (user_id,access_url_id);
CREATE TABLE user_friend(id bigint unsigned not null auto_increment,user_id int unsigned not null,friend_user_id int unsigned not null,relation_type int not null default 0,PRIMARY KEY(id));
ALTER TABLE user_friend ADD INDEX idx_user_friend_user(user_id);
ALTER TABLE user_friend ADD INDEX idx_user_friend_friend_user(friend_user_id);
ALTER TABLE user_friend ADD INDEX idx_user_friend_user_friend_user(user_id,friend_user_id);
CREATE TABLE user_friend_relation_type(id int unsigned not null auto_increment,title char(20),PRIMARY KEY(id));
CREATE TABLE user_api_key (id int unsigned NOT NULL auto_increment, user_id int unsigned NOT NULL, api_key char(32) NOT NULL, api_service char(10) NOT NULL default 'dokeos', PRIMARY KEY (id));
ALTER TABLE user_api_key ADD INDEX idx_user_api_keys_user (user_id);
CREATE TABLE message (id bigint unsigned not null auto_increment, user_sender_id int unsigned not null, user_receiver_id int unsigned not null,	msg_status tinyint unsigned not null default 0, send_date datetime not null default '0000-00-00 00:00:00', title varchar(255) not null, content text not null, PRIMARY KEY(id));
ALTER TABLE message ADD INDEX idx_message_user_sender(user_sender_id);
ALTER TABLE message ADD INDEX idx_message_user_receiver(user_receiver_id);
ALTER TABLE message ADD INDEX idx_message_user_sender_user_receiver(user_sender_id,user_receiver_id);
ALTER TABLE message ADD INDEX idx_message_msg_status(msg_status);

UPDATE settings_current SET access_url_changeable = 1 WHERE variable='Institution';
UPDATE settings_current SET access_url_changeable = 1 WHERE variable='InstitutionUrl';
UPDATE settings_current SET access_url_changeable = 1 WHERE variable='siteName';
UPDATE settings_current SET access_url_changeable = 1 WHERE variable='emailAdministrator';
UPDATE settings_current SET access_url_changeable = 1 WHERE variable='administratorSurname';
UPDATE settings_current SET access_url_changeable = 1 WHERE variable='administratorName';
UPDATE settings_current SET access_url_changeable = 1 WHERE variable='show_administrator_data';
UPDATE settings_current SET access_url_changeable = 1 WHERE variable='stylesheets';
UPDATE settings_current SET access_url_changeable = 1 WHERE variable='show_tabs' AND subkey='campus_homepage';
UPDATE settings_current SET access_url_changeable = 1 WHERE variable='show_tabs' AND subkey='my_courses';
UPDATE settings_current SET access_url_changeable = 1 WHERE variable='show_tabs' AND subkey='reporting';
UPDATE settings_current SET access_url_changeable = 1 WHERE variable='show_tabs' AND subkey='platform_administration';
UPDATE settings_current SET access_url_changeable = 1 WHERE variable='show_tabs' AND subkey='my_agenda';
UPDATE settings_current SET access_url_changeable = 1 WHERE variable='show_tabs' AND subkey='my_profile';
UPDATE settings_current SET access_url_changeable = 1 WHERE variable='show_tabs' AND subkey='my_gradebook';
 
-- xxSTATSxx
ALTER TABLE track_e_exercices ADD status varchar(20) NOT NULL default '';
ALTER TABLE track_e_exercices ADD data_tracking text NOT NULL default '';
ALTER TABLE track_e_exercices ADD steps_counter SMALLINT UNSIGNED NOT NULL default 0;
ALTER TABLE track_e_exercices ADD start_date datetime NOT NULL default '0000-00-00 00:00:00';
ALTER TABLE track_e_exercices ADD session_id SMALLINT UNSIGNED NOT NULL default 0;
ALTER TABLE track_e_exercices ADD INDEX ( session_id ) ;
CREATE TABLE track_e_attempt_recording (exe_id int unsigned NOT NULL, question_id int unsigned NOT NULL,  marks int NOT NULL,  insert_date datetime NOT NULL default '0000-00-00 00:00:00',  author int unsigned NOT NULL,  teacher_comment text NOT NULL);
ALTER TABLE track_e_attempt_recording ADD INDEX (exe_id);
ALTER TABLE track_e_hotspot CHANGE hotspot_coordinate hotspot_coordinate text NOT NULL;
ALTER TABLE track_e_exercices ADD orig_lp_id int  NOT NULL default 0;
ALTER TABLE track_e_exercices ADD orig_lp_item_id int  NOT NULL default 0;
-- xxUSERxx

-- xxCOURSExx
ALTER TABLE course_setting ADD INDEX unique_setting (variable,subkey,category);
ALTER TABLE lp ADD theme varchar(255) not null default '';
ALTER TABLE survey ADD mail_subject VARCHAR( 255 ) NOT NULL AFTER reminder_mail ;
ALTER TABLE quiz_rel_question ADD question_order mediumint unsigned NOT NULL default 1;
ALTER TABLE quiz ADD max_attempt int NOT NULL default 0;
ALTER TABLE survey ADD one_question_per_page bool NOT NULL default 0;
ALTER TABLE survey ADD shuffle bool NOT NULL default 0;
ALTER TABLE survey ADD survey_version varchar(255) NOT NULL default '';
ALTER TABLE survey ADD parent_id int NOT NULL default 0;
ALTER TABLE survey ADD survey_type int NOT NULL default 0;
ALTER TABLE survey_question ADD survey_group_pri int unsigned NOT NULL default 0;
ALTER TABLE survey_question ADD survey_group_sec1 int unsigned NOT NULL default 0;
ALTER TABLE survey_question ADD survey_group_sec2 int unsigned NOT NULL default 0;
CREATE TABLE survey_group (  id int unsigned NOT NULL auto_increment, name varchar(20) NOT NULL, description varchar(255) NOT NULL,  survey_id int unsigned NOT NULL, PRIMARY KEY  (id) );
ALTER TABLE survey_question_option ADD value int NOT NULL default 0;
UPDATE tool SET category = 'interaction' WHERE name = 'forum';
ALTER TABLE survey ADD show_form_profile int NOT NULL default 0;
ALTER TABLE survey ADD form_fields TEXT NOT NULL;
ALTER TABLE quiz_answer CHANGE hotspot_type hotspot_type ENUM( 'square', 'circle', 'poly', 'delineation' ) NULL DEFAULT NULL;
ALTER TABLE quiz ADD start_time datetime NOT NULL default '0000-00-00 00:00:00';
ALTER TABLE quiz ADD end_time datetime NOT NULL default '0000-00-00 00:00:00';
ALTER TABLE quiz ADD max_attempt int NOT NULL default 0;
ALTER TABLE forum_forum ADD forum_image varchar(255) NOT NULL default '';
ALTER TABLE lp ADD preview_image varchar(255) NOT NULL default '';
ALTER TABLE lp ADD author varchar(255) NOT NULL default '';
ALTER TABLE lp_item ADD terms TEXT NULL;
ALTER TABLE lp_item ADD search_did INT NULL;
CREATE TABLE wiki (id int NOT NULL auto_increment, page_id int NOT NULL default 0, reflink varchar(250) NOT NULL default 'index', title text NOT NULL, content mediumtext NOT NULL, user_id int NOT NULL default 0, group_id int default NULL, dtime datetime NOT NULL default '0000-00-00 00:00:00', addlock int NOT NULL default 1, editlock int NOT NULL default 0, visibility int NOT NULL default 1, addlock_disc int NOT NULL default 1, visibility_disc int NOT NULL default 1, ratinglock_disc int NOT NULL default 1, assignment int NOT NULL default 0, comment text NOT NULL, progress text NOT NULL, score int default 0, version int default NULL, is_editing int NOT NULL default 0, hits int default 0, linksto text NOT NULL, tag text NOT NULL, user_ip varchar(39) NOT NULL, PRIMARY KEY  (id) );
INSERT INTO tool(name,link,image,visibility,admin,address,added_tool,target,category) VALUES ('wiki','wiki/index.php','wiki.gif',0,'0','squaregrey.gif',0,'_self','interaction');
ALTER TABLE group_category ADD COLUMN wiki_state tinyint unsigned NOT NULL default 1;
ALTER TABLE group_info ADD COLUMN wiki_state enum('0','1','2') NOT NULL default '0';
ALTER TABLE announcement ADD session_id SMALLINT UNSIGNED NOT NULL;
ALTER TABLE announcement ADD INDEX ( session_id ) ;
ALTER TABLE forum_category ADD session_id SMALLINT UNSIGNED NOT NULL ;
ALTER TABLE forum_category ADD INDEX ( session_id ) ;
ALTER TABLE student_publication ADD session_id SMALLINT UNSIGNED NOT NULL default 0 ;
ALTER TABLE student_publication ADD INDEX ( session_id ) ;
ALTER TABLE calendar_event ADD session_id SMALLINT UNSIGNED NOT NULL default 0 ;
ALTER TABLE calendar_event ADD INDEX ( session_id ) ;
ALTER TABLE group_info ADD session_id SMALLINT UNSIGNED NOT NULL DEFAULT 0;
ALTER TABLE group_info ADD INDEX ( session_id ) ;
ALTER TABLE survey ADD session_id SMALLINT UNSIGNED NOT NULL DEFAULT 0;
ALTER TABLE survey ADD INDEX ( session_id ) ;
CREATE TABLE wiki_discuss (id int NOT NULL auto_increment, publication_id int NOT NULL default 0, userc_id int NOT NULL default 0, comment text NOT NULL, p_score varchar(255) default NULL, dtime datetime NOT NULL default '0000-00-00 00:00:00', PRIMARY KEY  (id) );
CREATE TABLE wiki_mailcue (id int NOT NULL, user_id int NOT NULL, type text NOT NULL, group_id int DEFAULT NULL, KEY  (id) );
ALTER TABLE lp_item ADD audio VARCHAR(250);
CREATE TABLE wiki_conf (id int NOT NULL auto_increment, page_id int NOT NULL default 0, feedback1 text NOT NULL, feedback2 text NOT NULL, feedback3 text NOT NULL, max_size int default NULL, max_text int default NULL, max_version int default NULL, startdate_assig datetime NOT NULL default '0000-00-00 00:00:00', enddate_assig datetime NOT NULL default '0000-00-00 00:00:00', delayedsubmit int NOT NULL default 0, PRIMARY KEY  (id) );
CREATE TABLE student_publication_assignment (id int NOT NULL auto_increment, expires_on datetime NOT NULL default '0000-00-00 00:00:00',  ends_on datetime NOT NULL default '0000-00-00 00:00:00',  add_to_calendar tinyint NOT NULL,  enable_qualification tinyint NOT NULL,  publication_id int NOT NULL,  PRIMARY KEY  (id));
ALTER TABLE student_publication ADD has_properties INT UNSIGNED NOT NULL DEFAULT 0;
ALTER TABLE student_publication ADD qualification INT UNSIGNED NOT NULL DEFAULT 0;
ALTER TABLE student_publication ADD date_of_qualification datetime NOT NULL default '0000-00-00 00:00:00';
ALTER TABLE student_publication ADD parent_id INT UNSIGNED NOT NULL DEFAULT 0;
ALTER TABLE student_publication ADD qualificator_id INT UNSIGNED NOT NULL DEFAULT 0;
CREATE TABLE forum_thread_qualify (id int unsigned PRIMARY KEY AUTO_INCREMENT, user_id int unsigned NOT NULL,thread_id int NOT NULL,qualify int default NULL,qualify_user_id int default NULL,qualify_time datetime NOT NULL default '0000-00-00 00:00:00', session_id int default NULL);
ALTER TABLE forum_thread_qualify ADD INDEX (user_id, thread_id);
ALTER TABLE forum_thread ADD session_id int unsigned default NULL;
ALTER TABLE forum_thread ADD thread_title_qualify varchar(255) default '';
ALTER TABLE forum_thread ADD thread_qualify_max int unsigned default 0;
CREATE TABLE forum_thread_qualify_log (id int unsigned PRIMARY KEY AUTO_INCREMENT, user_id int unsigned NOT NULL,thread_id int NOT NULL,qualify int default NULL,qualify_user_id int default NULL,qualify_time datetime NOT NULL default '0000-00-00 00:00:00', session_id int default NULL);
ALTER TABLE forum_thread_qualify_log ADD INDEX (user_id, thread_id);
INSERT INTO tool(name,link,image,visibility,admin,address,added_tool,target,category) VALUES ('gradebook','gradebook/index.php','gradebook.gif',1,'0','squaregrey.gif',0,'_self','authoring');
ALTER TABLE forum_thread ADD thread_close_date datetime default '0000-00-00 00:00:00';
ALTER TABLE student_publication ADD view_properties tinyint NULL;
UPDATE forum_notification SET forum_id=NULL WHERE forum_id='';
ALTER TABLE forum_notification CHANGE forum_id forum_id INT NULL;
UPDATE forum_notification SET thread_id=NULL WHERE thread_id='';
ALTER TABLE forum_notification CHANGE thread_id thread_id INT NULL;
UPDATE forum_notification SET post_id=NULL WHERE post_id='';
ALTER TABLE forum_notification CHANGE post_id post_id INT NULL;
ALTER TABLE forum_thread ADD thread_weight smallint default 0;
ALTER TABLE forum_notification CHANGE post_id post_id INT NULL;
ALTER TABLE quiz_answer CHANGE hotspot_coordinates hotspot_coordinates text;
ALTER TABLE group_info ADD forum_state TINYINT NULL AFTER announcements_state;
CREATE TABLE calendar_event_attachment ( id int NOT NULL auto_increment, path varchar(255) NOT NULL, comment text, size int NOT NULL default 0, agenda_id int NOT NULL, filename varchar(255) NOT NULL, PRIMARY KEY (id) );
ALTER TABLE glossary ADD display_order int;
CREATE TABLE calendar_event_attachment ( id int NOT NULL auto_increment, path varchar(255) NOT NULL, comment text, size int NOT NULL default 0, agenda_id int NOT NULL, filename varchar(255) NOT NULL, PRIMARY KEY (id) );
CREATE TABLE notebook (notebook_id int unsigned NOT NULL auto_increment,user_id int unsigned NOT NULL,course varchar(40) not null,session_id int NOT NULL default 0,title varchar(255) NOT NULL,description text NOT NULL,creation_date datetime NOT NULL default '0000-00-00 00:00:00',update_date datetime NOT NULL default '0000-00-00 00:00:00',PRIMARY KEY (notebook_id));
INSERT INTO course_setting(variable,value,category) VALUES ('allow_open_chat_window',0,'chat');
INSERT INTO course_setting(variable,value,category) VALUES ('email_alert_to_teacher_on_new_user_in_course',0,'registration');
INSERT INTO tool(name,link,image,visibility,admin,address,added_tool,target,category) VALUES ('glossary','glossary/index.php','glossary.gif',0,'0','squaregrey.gif',0,'_self','authoring');
INSERT INTO tool(name,link,image,visibility,admin,address,added_tool,target,category) VALUES ('notebook','notebook/index.php','notebook.gif',0,'0','squaregrey.gif',0,'_self','interaction');
ALTER TABLE quiz ADD feedback_type int NOT NULL default 0;
ALTER TABLE quiz_answer ADD destination text NOT NULL;
