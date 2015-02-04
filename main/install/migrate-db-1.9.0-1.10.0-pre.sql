-- This script updates the databases structure before migrating the data from
-- version 1.9.0 (or version 1.9.*) to version 1.10.0
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
CREATE TABLE course_field_options (id INT NOT NULL PRIMARY KEY AUTO_INCREMENT, field_id INT NOT NULL, option_value TEXT, option_display_text VARCHAR(64), option_order INT, tms DATETIME);

CREATE TABLE session_field_options (id INT NOT NULL PRIMARY KEY AUTO_INCREMENT, field_id INT NOT NULL, option_value TEXT, option_display_text VARCHAR(64), option_order INT, tms DATETIME);


-- Do not move this query
UPDATE settings_current SET selected_value = '1.9.0.18716' WHERE variable = 'chamilo_database_version';

-- xxCOURSExx

