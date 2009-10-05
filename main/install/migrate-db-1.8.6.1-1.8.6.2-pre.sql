-- This script updates the databases structure before migrating the data from
-- version 1.8.6.1 to version 1.8.6.2
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
ALTER TABLE gradebook_evaluation ADD COLUMN type varchar(40) NOT NULL;
ALTER TABLE session ADD COLUMN visibility int NOT NULL default 1;
ALTER TABLE session_rel_course_rel_user ADD COLUMN visibility int NOT NULL default 1;
CREATE TABLE session_category (id int(11) NOT NULL auto_increment, name varchar(100) default NULL, date_start date default NULL, date_end date default NULL, PRIMARY KEY  (id));
ALTER TABLE session ADD COLUMN session_category_id INT NOT NULL;

INSERT INTO settings_current (variable, subkey, type, category, selected_value, title, comment, scope, subkeytext, access_url, access_url_changeable) VALUES ('show_courses_descriptions_in_catalog', NULL, 'radio', 'Course', 'true', 'ShowCoursesDescriptionsInCatalogTitle', 'ShowCoursesDescriptionsInCatalogComment', NULL, NULL, 1, 1);
INSERT INTO settings_options (variable, value, display_text) VALUES ('show_courses_descriptions_in_catalog', 'true', 'Yes');
INSERT INTO settings_options (variable, value, display_text) VALUES ('show_courses_descriptions_in_catalog', 'false', 'No');


-- xxSTATSxx

-- xxUSERxx

-- xxCOURSExx

ALTER TABLE quiz ADD COLUMN session_id smallint DEFAULT 0, ADD INDEX (session_id);
ALTER TABLE blog ADD COLUMN session_id smallint DEFAULT 0, ADD INDEX (session_id);
ALTER TABLE course_description ADD COLUMN session_id smallint DEFAULT 0, ADD INDEX (session_id);
ALTER TABLE glossary ADD COLUMN session_id smallint DEFAULT 0, ADD INDEX (session_id);
ALTER TABLE link ADD COLUMN session_id smallint DEFAULT 0, ADD INDEX (session_id);
ALTER TABLE wiki ADD COLUMN session_id smallint DEFAULT 0, ADD INDEX (session_id);
ALTER TABLE tool ADD COLUMN session_id smallint DEFAULT 0, ADD INDEX (session_id);
ALTER TABLE link_category ADD COLUMN session_id smallint DEFAULT 0, ADD INDEX (session_id);
