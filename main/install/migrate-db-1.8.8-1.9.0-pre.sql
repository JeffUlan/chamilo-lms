-- This script updates the databases structure before migrating the data from
-- version 1.8.8 (or version 1.8.8.2, 1.8.8.4) to version 1.9.0
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
UPDATE settings_current SET selected_value = '1.9.0.15858' WHERE variable = 'chamilo_database_version';

INSERT INTO settings_current (variable, subkey, type, category, selected_value, title, comment, scope, subkeytext, access_url_changeable) VALUES ('filter_terms', NULL, 'textarea', 'Security', '', 'FilterTermsTitle', 'FilterTermsComment', NULL, NULL, 0);

INSERT INTO settings_current (variable, subkey, type, category, selected_value, title, comment, scope, subkeytext, access_url_changeable) VALUES ('header_extra_content', NULL, 'textarea', 'Tracking', '', 'HeaderExtraContentTitle', 'HeaderExtraContentComment', NULL, NULL, 1),('footer_extra_content', NULL, 'textarea', 'Tracking', '', 'FooterExtraContentTitle', 'FooterExtraContentComment', NULL, NULL,1);

ALTER TABLE personal_agenda ADD COLUMN all_day INTEGER NOT NULL DEFAULT 0;
ALTER TABLE sys_calendar ADD COLUMN all_day INTEGER NOT NULL DEFAULT 0;

INSERT INTO settings_current (variable, subkey, type, category, selected_value, title, comment, scope, subkeytext, access_url_changeable) VALUES ('show_documents_preview', NULL, 'radio', 'Tools', 'false', 'ShowDocumentPreviewTitle', 'ShowDocumentPreviewComment', NULL, NULL, 1);
INSERT INTO settings_options (variable, value, display_text) VALUES ('show_documents_preview', 'true', 'Yes');
INSERT INTO settings_options (variable, value, display_text) VALUES ('show_documents_preview', 'false', 'No');

INSERT INTO settings_current (variable, subkey, type, category, selected_value, title, comment, scope, subkeytext, access_url_changeable) VALUES ('htmlpurifier_wiki',NULL,'radio','Editor','false','HtmlPurifierWikiTitle','HtmlPurifierWikiComment',NULL,NULL, 0);

INSERT INTO settings_options (variable, value, display_text) VALUES ('htmlpurifier_wiki', 'true', 'Yes');
INSERT INTO settings_options (variable, value, display_text) VALUES ('htmlpurifier_wiki', 'false', 'No');
-- CAS feature
INSERT INTO settings_current (variable, subkey, type, category, selected_value, title, comment, scope, subkeytext, access_url_changeable) VALUES('cas_activate', NULL, 'radio', 'CAS', 'false', 'CasMainActivateTitle', 'CasMainActivateComment', NULL, NULL, 0);
INSERT INTO settings_options (variable, value, display_text) values ('cas_activate', 'true', 'Yes');
INSERT INTO settings_options (variable, value, display_text) values ('cas_activate', 'false', 'No');
INSERT INTO settings_current (variable, subkey, type, category, selected_value, title, comment, scope, subkeytext, access_url_changeable) VALUES('cas_server', NULL, 'textfield', 'CAS', '', 'CasMainServerTitle', 'CasMainServerComment', NULL, NULL, 0);
INSERT INTO settings_current (variable, subkey, type, category, selected_value, title, comment, scope, subkeytext, access_url_changeable) VALUES('cas_server_uri', NULL, 'textfield', 'CAS', '', 'CasMainServerURITitle', 'CasMainServerURIComment', NULL, NULL, 0);
INSERT INTO settings_current (variable, subkey, type, category, selected_value, title, comment, scope, subkeytext, access_url_changeable) VALUES('cas_port', NULL, 'textfield', 'CAS', '', 'CasMainPortTitle', 'CasMainPortComment', NULL, NULL, 0);
INSERT INTO settings_current (variable, subkey, type, category, selected_value, title, comment, scope, subkeytext, access_url_changeable) VALUES('cas_protocol', NULL, 'radio', 'CAS', '', 'CasMainProtocolTitle', 'CasMainProtocolComment', NULL, NULL, 0);
INSERT INTO settings_options (variable, value, display_text) values ('cas_protocol', 'CAS1', 'CAS1Text');
INSERT INTO settings_options (variable, value, display_text) values ('cas_protocol', 'CAS2', 'CAS2Text');
INSERT INTO settings_options (variable, value, display_text) values ('cas_protocol', 'SAML', 'SAMLText');
INSERT INTO settings_current (variable, subkey, type, category, selected_value, title, comment, scope, subkeytext, access_url_changeable) VALUES('cas_add_user_activate', NULL, 'radio', 'CAS', '', 'CasUserAddActivateTitle', 'CasUserAddActivateComment', NULL, NULL, 0);
INSERT INTO settings_options (variable, value, display_text) values ('cas_add_user_activate', 'true', 'Yes');
INSERT INTO settings_options (variable, value, display_text) values ('cas_add_user_activate', 'false', 'No');
INSERT INTO settings_current (variable, subkey, type, category, selected_value, title, comment, scope, subkeytext, access_url_changeable) VALUES('cas_add_user_login_attr', NULL, 'textfield', 'CAS', '', 'CasUserAddLoginAttributeTitle', 'CasUserAddLoginAttributeComment', NULL, NULL, 0);
INSERT INTO settings_current (variable, subkey, type, category, selected_value, title, comment, scope, subkeytext, access_url_changeable) VALUES('cas_add_user_email_attr', NULL, 'textfield', 'CAS', '', 'CasUserAddEmailAttributeTitle', 'CasUserAddEmailAttributeComment', NULL, NULL, 0);
INSERT INTO settings_current (variable, subkey, type, category, selected_value, title, comment, scope, subkeytext, access_url_changeable) VALUES('cas_add_user_firstname_attr', NULL, 'textfield', 'CAS', '', 'CasUserAddFirstnameAttributeTitle', 'CasUserAddFirstnameAttributeComment', NULL, NULL, 0);
INSERT INTO settings_current (variable, subkey, type, category, selected_value, title, comment, scope, subkeytext, access_url_changeable) VALUES('cas_add_user_lastname_attr', NULL, 'textfield', 'CAS', '', 'CasUserAddLastnameAttributeTitle', 'CasUserAddLastnameAttributeComment', NULL, NULL, 0);
-- Custom Pages
INSERT INTO settings_current (variable, type, category, selected_value, title, comment, scope) VALUES ('use_custom_pages','radio','Platform','false','UseCustomPages','UseCustomPagesComment', 'platform');
INSERT INTO settings_options (variable, value, display_text) VALUES ('use_custom_pages', 'true', 'Yes'), ('use_custom_pages', 'false', 'No');
-- Pages after login by role
INSERT INTO settings_current (variable, subkey, type, category, selected_value, title, comment, scope, subkeytext, access_url_changeable) VALUES('student_page_after_login', NULL, 'textfield', 'Platform', '', 'StudentPageAfterLoginTitle', 'StudentPageAfterLoginComment', NULL, NULL, 0);
INSERT INTO settings_current (variable, subkey, type, category, selected_value, title, comment, scope, subkeytext, access_url_changeable) VALUES('teacher_page_after_login', NULL, 'textfield', 'Platform', '', 'TeacherPageAfterLoginTitle', 'TeacherPageAfterLoginComment', NULL, NULL, 0);
INSERT INTO settings_current (variable, subkey, type, category, selected_value, title, comment, scope, subkeytext, access_url_changeable) VALUES('drh_page_after_login', NULL, 'textfield', 'Platform', '', 'DRHPageAfterLoginTitle', 'DRHPageAfterLoginComment', NULL, NULL, 0);
INSERT INTO settings_current (variable, subkey, type, category, selected_value, title, comment, scope, subkeytext, access_url_changeable) VALUES('sessionadmin_page_after_login', NULL, 'textfield', 'Platform', '', 'SessionAdminPageAfterLoginTitle', 'SessionAdminPageAfterLoginComment', NULL, NULL, 0);
INSERT INTO settings_current (variable, subkey, type, category, selected_value, title, comment, scope, subkeytext, access_url_changeable) VALUES('student_autosubscribe', NULL, 'textfield', 'Platform', '', 'StudentAutosubscribeTitle', 'StudentAutosubscribeComment', NULL, NULL, 0);
INSERT INTO settings_current (variable, subkey, type, category, selected_value, title, comment, scope, subkeytext, access_url_changeable) VALUES('teacher_autosubscribe', NULL, 'textfield', 'Platform', '', 'TeacherAutosubscribeTitle', 'TeacherAutosubscribeComment', NULL, NULL, 0);
INSERT INTO settings_current (variable, subkey, type, category, selected_value, title, comment, scope, subkeytext, access_url_changeable) VALUES('drh_autosubscribe', NULL, 'textfield', 'Platform', '', 'DRHAutosubscribeTitle', 'DRHAutosubscribeComment', NULL, NULL, 0);
INSERT INTO settings_current (variable, subkey, type, category, selected_value, title, comment, scope, subkeytext, access_url_changeable) VALUES('sessionadmin_autosubscribe', NULL, 'textfield', 'Platform', '', 'SessionadminAutosubscribeTitle', 'SessionadminAutosubscribeComment', NULL, NULL, 0);
INSERT INTO settings_current (variable, subkey, type, category, selected_value, title, comment, scope, subkeytext, access_url_changeable) VALUES('show_tabs', 'custom_tab_1', 'checkbox', 'Platform', 'true', 'ShowTabsTitle', 'ShowTabsComment', NULL, 'TabsCustom1', 1);
INSERT INTO settings_current (variable, subkey, type, category, selected_value, title, comment, scope, subkeytext, access_url_changeable) VALUES('show_tabs', 'custom_tab_2', 'checkbox', 'Platform', 'false', 'ShowTabsTitle', 'ShowTabsComment', NULL, 'TabsCustom2', 1);
INSERT INTO settings_current (variable, subkey, type, category, selected_value, title, comment, scope, subkeytext, access_url_changeable) VALUES('show_tabs', 'custom_tab_3', 'checkbox', 'Platform', 'false', 'ShowTabsTitle', 'ShowTabsComment', NULL, 'TabsCustom3', 1);
INSERT INTO settings_current (variable, subkey, type, category, selected_value, title, comment, scope, subkeytext, access_url_changeable) VALUES('custom_tab_1_name', NULL, 'textfield', 'Platform', 'Reports', 'CustomTab1NameTitle', 'CustomTab1NameComment', NULL, NULL, 0);
INSERT INTO settings_current (variable, subkey, type, category, selected_value, title, comment, scope, subkeytext, access_url_changeable) VALUES('custom_tab_1_url', NULL, 'textfield', 'Platform', '/main/reports/', 'CustomTab1URLTitle', 'CustomTab1URLComment', NULL, NULL, 0);
INSERT INTO settings_current (variable, subkey, type, category, selected_value, title, comment, scope, subkeytext, access_url_changeable) VALUES('custom_tab_2_name', NULL, 'textfield', 'Platform', '', 'CustomTab2NameTitle', 'CustomTab2NameComment', NULL, NULL, 0);
INSERT INTO settings_current (variable, subkey, type, category, selected_value, title, comment, scope, subkeytext, access_url_changeable) VALUES('custom_tab_2_url', NULL, 'textfield', 'Platform', '', 'CustomTab2URLTitle', 'CustomTab2URLComment', NULL, NULL, 0);
INSERT INTO settings_current (variable, subkey, type, category, selected_value, title, comment, scope, subkeytext, access_url_changeable) VALUES('custom_tab_3_name', NULL, 'textfield', 'Platform', '', 'CustomTab3NameTitle', 'CustomTab3NameComment', NULL, NULL, 0);
INSERT INTO settings_current (variable, subkey, type, category, selected_value, title, comment, scope, subkeytext, access_url_changeable) VALUES('custom_tab_3_url', NULL, 'textfield', 'Platform', '', 'CustomTab3URLTitle', 'CustomTab3URLComment', NULL, NULL, 0);
-- INSERT INTO settings_current (variable, subkey, type, category, selected_value, title, comment, scope, subkeytext, access_url_changeable) VALUES  ('languagePriority1', NULL, 'radio', 'Languages', 'course_lang', 'LanguagePriority1Title', 'LanguagePriority1Comment', NULL, NULL, 0);
-- INSERT INTO settings_current (variable, subkey, type, category, selected_value, title, comment, scope, subkeytext, access_url_changeable) VALUES ('languagePriority2', NULL, 'radio', 'Languages', 'user_profil_lang', 'LanguagePriority2Title', 'LanguagePriority2Comment', NULL, NULL, 0);
-- INSERT INTO settings_current (variable, subkey, type, category, selected_value, title, comment, scope, subkeytext, access_url_changeable) VALUES ('languagePriority3', NULL, 'radio', 'Languages', 'user_selected_lang', 'LanguagePriority3Title', 'LanguagePriority3Comment', NULL, NULL, 0);
-- INSERT INTO settings_current (variable, subkey, type, category, selected_value, title, comment, scope, subkeytext, access_url_changeable) VALUES ('languagePriority4', NULL, 'radio', 'Languages', 'platform_lang', 'LanguagePriority4Title', 'LanguagePriority4Comment', NULL, NULL, 0);
-- INSERT INTO settings_options (variable, value, display_text) VALUES ('languagePriority1','platform_lang','PlatformLanguage'), ('languagePriority1','user_profil_lang','UserLanguage'), ('languagePriority1','user_selected_lang','UserSelectedLanguage'), ('languagePriority1','course_lang','CourseLanguage'), ('languagePriority2','platform_lang','PlatformLanguage'), ('languagePriority2','user_profil_lang','UserLanguage'), ('languagePriority2','user_selected_lang','UserSelectedLanguage'), ('languagePriority2','course_lang','CourseLanguage'), ('languagePriority3','platform_lang','PlatformLanguage'), ('languagePriority3','user_profil_lang','UserLanguage'), ('languagePriority3','user_selected_lang','UserSelectedLanguage'), ('languagePriority3','course_lang','CourseLanguage'), ('languagePriority4','platform_lang','PlatformLanguage'), ('languagePriority4','user_profil_lang','UserLanguage'), ('languagePriority4','user_selected_lang','UserSelectedLanguage'), ('languagePriority4','course_lang','CourseLanguage');
-- INSERT INTO settings_current (variable, subkey, type, category, selected_value, title, comment, scope, subkeytext, access_url_changeable) VALUES ('activate_send_event_by_mail', NULL, 'radio', 'Platform', 'false', 'ActivateSendEventByMailTitle', 'ActivateSendEventByMailComment', NULL, NULL, 0);
-- INSERT INTO settings_options (variable, value, display_text) VALUES ('activate_send_event_by_mail', 'true', 'Yes'),('activate_send_event_by_mail', 'false', 'No');
INSERT INTO settings_current (variable, subkey, type, category, selected_value, title, comment, scope, subkeytext, access_url_changeable) VALUES('scorm_cumulative_session_time', NULL, 'radio', 'Course', 'true', 'ScormCumulativeSessionTimeTitle', 'ScormCumulativeSessionTimeComment', NULL, NULL, 0);
INSERT INTO settings_options (variable, value, display_text) VALUES ('scorm_cumulative_session_time','true','Yes'), ('scorm_cumulative_session_time','false','No');
-- CREATE TABLE event_type ( id int unsigned NOT NULL AUTO_INCREMENT, name varchar(50) NOT NULL, name_lang_var varchar(40) NOT NULL, desc_lang_var varchar(50) NOT NULL, PRIMARY KEY (id));
-- CREATE TABLE event_type_message ( id int unsigned NOT NULL AUTO_INCREMENT, event_type_id int NOT NULL, language_id int NOT NULL, message varchar(200) NOT NULL, subject varchar(60) NOT NULL, PRIMARY KEY (id));
-- CREATE TABLE user_rel_event_type ( id int NOT NULL AUTO_INCREMENT, user_id int unsigned NOT NULL, event_type_id int unsigned NOT NULL, PRIMARY KEY (id));
-- INSERT INTO `event_type` VALUES (1, 'course_deleted','courseDeletedTitle','courseDeletedComment'),(2,'course_created','courseCreatedTitle','courseCreatedComment'),(3,'user_deleted','userDeletedTitle','userDeletedComment'),(4,'user_created','userCreatedTitle','userCreatedComment'), (5, 'session_created','sessionCreatedTitle','sessionCreatedComment'), (6,'session_deleted','sessionDeletedTitle','sessionDeletedComment'), (7,'session_category_created','sessionCategoryCreatedTitle','sessionCategoryCreatedComment'),(8,'session_category_deleted','sessionCategoryDeletedTitle','sessionCategoryDeletedComment'),(9,'settings_changed','settingsChangedTitle','settingsChangedComment'),(10,'user_subscribed','userSubscribedTitle','userSubscribedComment'), (11,'user_unsubscribed','userUnsubscribedTitle','userUnsubscribedComment');
-- INSERT INTO `event_type_message` (`id`,`event_type_id`, `language_id`, `message`,`subject`) VALUES (1,4,10,'Bonjour, \r\n\r\nL\'utilisateur %username% (%firstname% %lastname%) a été créé.\r\nEmail : %mail%\r\n\r\nBien à vous.',''),(2,1,10,'Delete formation',''),(3,2,10,'Create formation',''),(4,3,10,'Bonjour, \r\n\r\nL\'utilisateur %username% (%firstname% %lastname%) a été supprimé.\r\n\r\nBien à vous.',''),(6,5,10,'Create session test',''),(7,6,10,'Delete session',''),(8,7,10,'Create category session',''),(9,8,10,'Delete category session',''),(10,9,10,'Change setting',''),(11,10,10,'Subscribe',''),(12,11,10,'Unsubscribe','');
INSERT INTO user_field (field_type, field_variable, field_display_text, field_visible, field_changeable) values (1, 'already_logged_in','Already logged in',0,0);
INSERT INTO user_field (field_type, field_variable, field_display_text, field_visible, field_changeable) values (1, 'update_type','Update script type',0,0);
CREATE TABLE announcement_rel_group (group_id int NOT NULL, announcement_id int NOT NULL, PRIMARY KEY (group_id, announcement_id));
CREATE TABLE group_rel_group ( id int NOT NULL AUTO_INCREMENT, group_id int NOT NULL, subgroup_id int NOT NULL, relation_type int NOT NULL, PRIMARY KEY (id));
ALTER TABLE group_rel_group ADD INDEX ( group_id );
ALTER TABLE group_rel_group ADD INDEX ( subgroup_id );
ALTER TABLE group_rel_group ADD INDEX ( relation_type );


INSERT INTO settings_current (variable, subkey, type, category, selected_value, title, comment, scope, subkeytext, access_url_changeable) VALUES ('allow_teacher_change_gradebook_grading_model', NULL, 'radio', 'Gradebook', 'false', 'AllowTeacherChangeGradebookGradingModelTitle', 'AllowTeacherChangeGradebookGradingModelComment', NULL, NULL, 1);
INSERT INTO settings_options (variable, value, display_text) VALUES ('allow_teacher_change_gradebook_grading_model', 'true', 'Yes');
INSERT INTO settings_options (variable, value, display_text) VALUES ('allow_teacher_change_gradebook_grading_model', 'false', 'No');


INSERT INTO settings_current (variable, subkey, type, category, selected_value, title, comment, scope, subkeytext, access_url_changeable) VALUES ('grading_model', 'grading_model', 'custom', 'Gradebook', 'false', 'GradingModelTitle', 'GradingModelComment', NULL, NULL, 1);


INSERT INTO settings_options (variable, value, display_text) VALUES ('grading_model', '1*X+2*X+3*X/4', 'Model 1');
INSERT INTO settings_options (variable, value, display_text) VALUES ('grading_model', '1*X+1*X+1*X/3', 'Model 2');
INSERT INTO settings_options (variable, value, display_text) VALUES ('grading_model', '1*X+1*X+1*X+1*X/4', 'Model 3');



-- xxSTATSxx
ALTER TABLE track_e_exercices ADD COLUMN questions_to_check TEXT  NOT NULL DEFAULT '';
--CREATE TABLE track_filtered_terms (id int, user_id int, course_id int, session_id int, tool_id char(12), filtered_term varchar(255), created_at datetime);
CREATE TABLE reports_keys ( id int unsigned NOT NULL AUTO_INCREMENT, course_id int unsigned DEFAULT NULL, tool_id int DEFAULT NULL, child_id int DEFAULT NULL, child_name varchar(64) DEFAULT NULL, subchild_id int DEFAULT NULL, subchild_name varchar(64) DEFAULT NULL, subsubchild_id int DEFAULT NULL, subsubchild_name varchar(64) DEFAULT NULL, link varchar(256) DEFAULT NULL, PRIMARY KEY (id), KEY course_id (course_id), KEY course_id_2 (course_id,tool_id,child_id,subchild_id,subsubchild_id));
CREATE TABLE reports_values ( key_id int unsigned NOT NULL, user_id int unsigned DEFAULT NULL, session_id int DEFAULT NULL, attempt int DEFAULT NULL, score decimal(5,3) DEFAULT NULL, progress int DEFAULT NULL, report_time int DEFAULT NULL, KEY user_id (user_id), PRIMARY KEY (key_id,user_id,session_id,attempt));
CREATE TABLE stored_values (user_id INT NOT NULL, sco_id INT NOT NULL, course_id CHAR(40) NOT NULL, sv_key CHAR(64) NOT NULL, sv_value TEXT NOT NULL );
ALTER TABLE stored_values ADD KEY (user_id, sco_id, course_id, sv_key);
ALTER TABLE stored_values ADD UNIQUE (user_id, sco_id, course_id, sv_key);
CREATE TABLE stored_values_stack (user_id INT NOT NULL, sco_id INT NOT NULL, stack_order INT NOT NULL, course_id CHAR(40) NOT NULL, sv_key CHAR(64) NOT NULL, sv_value TEXT NOT NULL );
ALTER TABLE stored_values_stack ADD KEY (user_id, sco_id, course_id, sv_key, stack_order);
ALTER TABLE stored_values_stack ADD UNIQUE (user_id, sco_id, course_id, sv_key, stack_order);

-- xxUSERxx

-- xxCOURSExx
ALTER TABLE lp ADD COLUMN hide_toc_frame TINYINT NOT NULL DEFAULT 0;
ALTER TABLE lp ADD COLUMN seriousgame_mode TINYINT NOT NULL DEFAULT 0;
ALTER TABLE lp_item_view modify column suspend_data longtext;
INSERT INTO course_setting(variable,value,category) VALUES ('course_grading_model','','gradebook');
ALTER TABLE quiz ADD COLUMN review_answers INT NOT NULL DEFAULT 0;
ALTER TABLE student_publication ADD COLUMN contains_file INTEGER NOT NULL DEFAULT 1;
ALTER TABLE student_publication ADD COLUMN allow_text_assignment INTEGER NOT NULL DEFAULT 0;

