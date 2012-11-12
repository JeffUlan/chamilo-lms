-- This script updates the databases structure before migrating the data from
-- version 1.9.0 (or version 1.9.2, 1.9.4) to version 1.10.0
-- it is intended as a standalone script, however, because of the multiple
-- databases related difficulties, it should be parsed by a PHP script in
-- order to connect to and update the right databases.
-- There is one line per query, allowing the PHP function file() to read
-- all lines separately into an array. The xxMAINxx-type markers are there
-- to tell the PHP script which database we're talking about.
-- By always using the keyword "TABLE" in the queries, we should be able
-- to retrieve and modify the table name from the PHP script if needed, which
-- will allow us to deal with the unique-database-type installations

-- xxMAINxx

-- Optimize tracking query very often queried on busy campuses
ALTER TABLE track_e_online ADD INDEX idx_trackonline_uat (login_user_id, access_url_id, login_date);
ALTER TABLE track_e_default ADD COLUMN session_id INT NOT NULL DEFAULT 0;

INSERT INTO settings_current (variable, subkey, type, category, selected_value, title, comment, scope, subkeytext, access_url_changeable) VALUES ('session_tutor_reports_visibility', NULL, 'radio', 'Session', 'true', 'SessionTutorsCanSeeExpiredSessionsResultsTitle', 'SessionTutorsCanSeeExpiredSessionsResultsComment', NULL, NULL, 1);
INSERT INTO settings_options (variable, value, display_text) VALUES ('session_tutor_reports_visibility', 'true', 'Yes');
INSERT INTO settings_options (variable, value, display_text) VALUES ('session_tutor_reports_visibility', 'false', 'No');

INSERT INTO settings_current (variable, subkey, type, category, selected_value, title, comment, scope, subkeytext, access_url_changeable) VALUES ('gradebook_show_percentage_in_reports', NULL,'radio','Gradebook','true','GradebookShowPercentageInReportsTitle','GradebookShowPercentageInReportsComment', NULL, NULL, 0),
INSERT INTO settings_options (variable, value, display_text) VALUES ('gradebook_show_percentage_in_reports', 'true', 'Yes');
INSERT INTO settings_options (variable, value, display_text) VALUES ('gradebook_show_percentage_in_reports', 'false', 'No');

ALTER TABLE notification ADD COLUMN sender_id INT NOT NULL DEFAULT 0;

ALTER TABLE session_rel_user ADD COLUMN moved_to INT NOT NULL DEFAULT 0;
ALTER TABLE session_rel_user ADD COLUMN moved_status INT NOT NULL DEFAULT 0;
ALTER TABLE session_rel_user ADD COLUMN moved_at datetime NOT NULL default '0000-00-00 00:00:00';

ALTER TABLE session ADD COLUMN display_start_date datetime NOT NULL default '0000-00-00 00:00:00';
ALTER TABLE session ADD COLUMN display_end_date datetime NOT NULL default '0000-00-00 00:00:00';
ALTER TABLE session ADD COLUMN access_start_date datetime NOT NULL default '0000-00-00 00:00:00';
ALTER TABLE session ADD COLUMN access_end_date datetime NOT NULL default '0000-00-00 00:00:00';
ALTER TABLE session ADD COLUMN coach_access_start_date datetime NOT NULL default '0000-00-00 00:00:00';
ALTER TABLE session ADD COLUMN coach_access_end_date datetime NOT NULL default '0000-00-00 00:00:00';

ALTER TABLE grade_components ADD COLUMN prefix VARCHAR(255) DEFAULT NULL;
ALTER TABLE grade_components ADD COLUMN exclusions INT DEFAULT 0;
ALTER TABLE grade_components ADD COLUMN count_elements INT DEFAULT 0;

CREATE TABLE IF NOT EXISTS session_field_options (id int NOT NULL auto_increment, field_id int NOT NULL, option_value text, option_display_text varchar(255), option_order int, tms DATETIME NOT NULL default '0000-00-00 00:00:00', PRIMARY KEY (id));
CREATE TABLE IF NOT EXISTS course_field_options (id int NOT NULL auto_increment, field_id int NOT NULL, option_value text, option_display_text varchar(255), option_order int, tms DATETIME NOT NULL default '0000-00-00 00:00:00', PRIMARY KEY (id));

ALTER TABLE session_field_options ADD INDEX idx_session_field_options_field_id(field_id);
ALTER TABLE session_field_values ADD INDEX idx_session_field_values_session_id(session_id);
ALTER TABLE session_field_values ADD INDEX idx_session_field_values_field_id(field_id);

ALTER TABLE session MODIFY COLUMN name CHAR(150) NOT NULL DEFAULT '';
ALTER TABLE session MODIFY COLUMN id MEDIUMINT unsigned NOT NULL;

ALTER TABLE session_rel_course MODIFY COLUMN id_session MEDIUMINT unsigned NOT NULL;
ALTER TABLE session_rel_course ADD COLUMN course_id INT NOT NULL DEFAULT '0';
ALTER TABLE session_rel_course ADD INDEX idx_session_rel_course_course_id (course_id);
ALTER TABLE session_rel_course DROP PRIMARY KEY;
ALTER TABLE session_rel_course ADD PRIMARY KEY (id_session, course_id);

ALTER TABLE session_rel_course_rel_user MODIFY COLUMN id_session MEDIUMINT unsigned NOT NULL;
ALTER TABLE session_rel_course_rel_user ADD COLUMN course_id INT NOT NULL DEFAULT '0';
ALTER TABLE session_rel_course_rel_user DROP PRIMARY KEY;
ALTER TABLE session_rel_course_rel_user ADD PRIMARY KEY (id_session, course_id, id_user);

ALTER TABLE session_rel_course_rel_user ADD INDEX idx_session_rel_course_rel_user_id_user (id_user);
ALTER TABLE session_rel_course_rel_user ADD INDEX idx_session_rel_course_rel_user_course_id (course_id);

-- Courses changes c_XXX

ALTER TABLE c_lp_item ADD INDEX idx_c_lp_item_cid_lp_id (c_id, lp_id);
ALTER TABLE c_lp_item_view ADD INDEX idx_c_lp_item_view_cid_lp_view_id_lp_item_id(c_id, lp_view_id, lp_item_id);
ALTER TABLE c_item_property ADD INDEX idx_itemprop_id_tool (c_id, tool(8));
ALTER TABLE c_tool_intro MODIFY COLUMN intro_text MEDIUMTEXT NOT NULL;

ALTER TABLE c_quiz_question_rel_category ADD COLUMN id int unsigned NOT NULL;
ALTER TABLE c_quiz_question_rel_category DROP PRIMARY KEY;
ALTER TABLE c_quiz_question_rel_category ADD PRIMARY KEY (id, c_id, question_id);
ALTER TABLE c_quiz_question_rel_category MODIFY COLUMN id int unsigned AUTO_INCREMENT;

ALTER TABLE session ADD INDEX idx_id_coach (id_coach);
ALTER TABLE session ADD INDEX idx_id_session_admin_id (session_admin_id);

/*
CREATE TABLE IF NOT EXISTS transacciones (
    id  int unsigned PRIMARY KEY NOT NULL AUTO_INCREMENT,
    sede int unsigned NOT NULL,
    tipo_id int unsigned NOT NULL,
    system_transaction_id int unsigned NOT NULL,
    orig varchar(255),
    dest varchar(255),
    tms varchar(255)  
);

CREATE TABLE IF NOT EXISTS tipo_transaccion (
    id  INT unsigned PRIMARY KEY NOT NULL AUTO_INCREMENT,   
    titulo varchar(255)    
);*/

CREATE TABLE IF NOT EXISTS gradebook_evaluation_type (
    id  INT unsigned PRIMARY KEY NOT NULL AUTO_INCREMENT,   
    name varchar(255),
    external_id INT unsigned NOT NULL DEFAULT 0
);

ALTER TABLE gradebook_evaluation ADD COLUMN evaluation_type_id INT NOT NULL DEFAULT 0;
ALTER TABLE gradebook_link ADD COLUMN evaluation_type_id INT NOT NULL DEFAULT 0;

INSERT INTO settings_options(variable,value,display_text) VALUES ('last_transaction_id','0');

CREATE TABLE migration_transaction (id int not null AUTO_INCREMENT,  branch_id int not null default 0,  action char(20),  item_id char(36),  orig_id char(36),  dest_id char(36),  status_id tinyint not null default 0,  time_insert datetime NOT NULL DEFAULT '0000-00-00 00:00:00',  time_update datetime NOT NULL DEFAULT '0000-00-00 00:00:00', PRIMARY KEY (id, branch_id));
CREATE TABLE migration_transaction_status (  id tinyint not null PRIMARY KEY AUTO_INCREMENT,  title char(20));
INSERT INTO migration_transaction_status VALUES (1, 'To be executed'), (2, 'Executed successfully'), (3, 'Execution deprecated'), (4, 'Execution failed');




-- Do not move this 
UPDATE settings_current SET selected_value = '1.10.0.20306' WHERE variable = 'chamilo_database_version';