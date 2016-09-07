<?php
/* For licensing terms, see /license.txt */

namespace Application\Migrations\Schema\V111;

use Application\Migrations\AbstractMigrationChamilo;
use Doctrine\DBAL\Schema\Schema;

/**
 * Class Version111
 * Migrate file to updated to Chamilo 1.11
 *
 */
class Version111 extends AbstractMigrationChamilo
{
    /**
     * @param Schema $schema
     *
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    public function up(Schema $schema)
    {
        $this->addSql('CREATE TABLE extra_field_saved_search (id INT AUTO_INCREMENT NOT NULL, field_id INT DEFAULT NULL, user_id INT DEFAULT NULL, value LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_16ABE32A443707B0 (field_id), INDEX IDX_16ABE32AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE extra_field_saved_search ADD CONSTRAINT FK_16ABE32A443707B0 FOREIGN KEY (field_id) REFERENCES extra_field (id)');
        $this->addSql('ALTER TABLE extra_field_saved_search ADD CONSTRAINT FK_16ABE32AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');

        $this->addSql('CREATE TABLE c_lp_category_user (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, INDEX IDX_61F042712469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE c_lp_category_user ADD CONSTRAINT FK_61F042712469DE2 FOREIGN KEY (category_id) REFERENCES c_lp_category (iid)');

        $this->addSql('ALTER TABLE c_lp_category_user ADD user_id INT DEFAULT NULL;');
        $this->addSql('ALTER TABLE c_lp_category_user ADD CONSTRAINT FK_61F0427A76ED395 FOREIGN KEY (user_id) REFERENCES user (id);');
        $this->addSql('CREATE INDEX IDX_61F0427A76ED395 ON c_lp_category_user (user_id);');

        $this->addSql("INSERT INTO settings_current (variable, subkey, type, category, selected_value, title, comment, scope, subkeytext, access_url_changeable) VALUES ('allow_my_files',NULL,'radio','Platform','true','AllowMyFilesTitle','AllowMyFilesComment','',NULL, 1)");
        $this->addSql("INSERT INTO settings_options (variable, value, display_text) VALUES ('allow_my_files','true','Yes') ");
        $this->addSql("INSERT INTO settings_options (variable, value, display_text) VALUES ('allow_my_files','false','No') ");

        $this->addSql("INSERT INTO settings_current (variable, subkey, type, category, selected_value, title, comment, scope, subkeytext, access_url_changeable) VALUES ('exercise_invisible_in_session',NULL,'radio','Session','false','ExerciseInvisibleInSessionTitle','ExerciseInvisibleInSessionComment','',NULL, 1)");
        $this->addSql("INSERT INTO settings_options (variable, value, display_text) VALUES ('exercise_invisible_in_session','true','Yes') ");
        $this->addSql("INSERT INTO settings_options (variable, value, display_text) VALUES ('exercise_invisible_in_session','false','No') ");

        $this->addSql("INSERT INTO settings_current (variable, subkey, type, category, selected_value, title, comment, scope, subkeytext, access_url_changeable) VALUES ('configure_exercise_visibility_in_course',NULL,'radio','Session','false','ConfigureExerciseVisibilityInCourseTitle','ConfigureExerciseVisibilityInCourseComment','',NULL, 1)");
        $this->addSql("INSERT INTO settings_options (variable, value, display_text) VALUES ('configure_exercise_visibility_in_course','true','Yes') ");
        $this->addSql("INSERT INTO settings_options (variable, value, display_text) VALUES ('configure_exercise_visibility_in_course','false','No') ");
        $this->addSql("ALTER TABLE c_forum_forum ADD moderated TINYINT(1) DEFAULT NULL");
        $this->addSql("ALTER TABLE c_forum_post ADD status INT DEFAULT NULL");
        $this->addSql("CREATE TABLE c_quiz_rel_category (iid BIGINT AUTO_INCREMENT NOT NULL, c_id INT NOT NULL, category_id INT NOT NULL, exercise_id INT NOT NULL, count_questions INT NOT NULL, PRIMARY KEY(iid))");
        $this->addSql("ALTER TABLE c_quiz ADD COLUMN question_selection_type INT");

        $this->addSql("CREATE TABLE faq_question_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT DEFAULT NULL, headline VARCHAR(255) NOT NULL, body LONGTEXT DEFAULT NULL, slug VARCHAR(50) NOT NULL, locale VARCHAR(255) NOT NULL, INDEX IDX_C2D1A2C2AC5D3 (translatable_id), UNIQUE INDEX faq_question_translation_unique_translation (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;");
        $this->addSql("CREATE TABLE faq_category_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT DEFAULT NULL, headline VARCHAR(255) NOT NULL, body LONGTEXT DEFAULT NULL, slug VARCHAR(50) NOT NULL, locale VARCHAR(255) NOT NULL, INDEX IDX_5493B0FC2C2AC5D3 (translatable_id), UNIQUE INDEX faq_category_translation_unique_translation (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;");
        $this->addSql("CREATE TABLE faq_category (id INT AUTO_INCREMENT NOT NULL, rank INT NOT NULL, is_active TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX is_active_idx (is_active), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;");
        $this->addSql("CREATE TABLE faq_question (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, is_active TINYINT(1) NOT NULL, rank INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, only_auth_users TINYINT(1) NOT NULL, INDEX IDX_4A55B05912469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;");
        $this->addSql("ALTER TABLE faq_question_translation ADD CONSTRAINT FK_C2D1A2C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES faq_question (id) ON DELETE CASCADE;");
        $this->addSql("ALTER TABLE faq_category_translation ADD CONSTRAINT FK_5493B0FC2C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES faq_category (id) ON DELETE CASCADE;");
        $this->addSql("ALTER TABLE faq_question ADD CONSTRAINT FK_4A55B05912469DE2 FOREIGN KEY (category_id) REFERENCES faq_category (id);");

        $this->addSql("CREATE TABLE contact_category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;");
        $table = $schema->getTable('session_rel_user');
        if (!$table->hasColumn('duration')) {
            $this->addSql("ALTER TABLE session_rel_user ADD duration INT DEFAULT NULL");
        }

        $this->addSql('CREATE TABLE access_url_rel_course_category (id INT AUTO_INCREMENT NOT NULL, access_url_id INT NOT NULL, course_category_id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql("INSERT INTO access_url_rel_course_category (access_url_id, course_category_id) VALUES (1, 1) ");
        $this->addSql("INSERT INTO access_url_rel_course_category (access_url_id, course_category_id) VALUES (1, 2) ");
        $this->addSql("INSERT INTO access_url_rel_course_category (access_url_id, course_category_id) VALUES (1, 3) ");

        $this->addSql('ALTER TABLE notification CHANGE content content TEXT');

        // Needed to update 0000-00-00 00:00:00 values
        $this->addSql('SET sql_mode = ""');

        $this->addSql('ALTER TABLE c_lp CHANGE publicated_on publicated_on DATETIME');
        $this->addSql('ALTER TABLE c_lp CHANGE expired_on expired_on DATETIME');

        $this->addSql('UPDATE c_lp SET publicated_on = NULL WHERE publicated_on = "0000-00-00 00:00:00"');
        $this->addSql('UPDATE c_lp SET expired_on = NULL WHERE expired_on = "0000-00-00 00:00:00"');

        $this->addSql('ALTER TABLE c_quiz CHANGE start_time start_time DATETIME');
        $this->addSql('ALTER TABLE c_quiz CHANGE end_time end_time DATETIME');

        $this->addSql('UPDATE c_quiz SET start_time = NULL WHERE start_time = "0000-00-00 00:00:00"');
        $this->addSql('UPDATE c_quiz SET end_time = NULL WHERE end_time = "0000-00-00 00:00:00"');

        $this->addSql('ALTER TABLE c_calendar_event CHANGE start_date start_date DATETIME');
        $this->addSql('ALTER TABLE c_calendar_event CHANGE end_date end_date DATETIME');

        $this->addSql('UPDATE c_calendar_event SET start_date = NULL WHERE start_date = "0000-00-00 00:00:00"');
        $this->addSql('UPDATE c_calendar_event SET end_date = NULL WHERE end_date = "0000-00-00 00:00:00"');

        $this->addSql('ALTER TABLE personal_agenda CHANGE date date DATETIME');
        $this->addSql('ALTER TABLE personal_agenda CHANGE enddate enddate DATETIME');

        $this->addSql('UPDATE personal_agenda SET date = NULL WHERE date = "0000-00-00 00:00:00"');
        $this->addSql('UPDATE personal_agenda SET enddate = NULL WHERE enddate = "0000-00-00 00:00:00"');

        $this->addSql('ALTER TABLE c_forum_forum CHANGE start_time start_time DATETIME');
        $this->addSql('ALTER TABLE c_forum_forum CHANGE end_time end_time DATETIME');

        $this->addSql('UPDATE c_forum_forum SET start_time = NULL WHERE start_time = "0000-00-00 00:00:00"');
        $this->addSql('UPDATE c_forum_forum SET end_time = NULL WHERE end_time = "0000-00-00 00:00:00"');

        $this->addSql('ALTER TABLE sys_calendar CHANGE start_date start_date DATETIME');
        $this->addSql('ALTER TABLE sys_calendar CHANGE end_date end_date DATETIME');

        $this->addSql('UPDATE sys_calendar SET start_date = NULL WHERE start_date = "0000-00-00 00:00:00"');
        $this->addSql('UPDATE sys_calendar SET end_date = NULL WHERE end_date = "0000-00-00 00:00:00"');

        $this->addSql('ALTER TABLE message CHANGE update_date update_date DATETIME');
        $this->addSql('UPDATE message SET update_date = NULL WHERE update_date = "0000-00-00 00:00:00"');

        $this->addSql('ALTER TABLE c_wiki_conf CHANGE startdate_assig startdate_assig DATETIME');
        $this->addSql('ALTER TABLE c_wiki_conf CHANGE enddate_assig enddate_assig DATETIME');

        $this->addSql('UPDATE c_wiki_conf SET startdate_assig = NULL WHERE startdate_assig = "0000-00-00 00:00:00"');
        $this->addSql('UPDATE c_wiki_conf SET enddate_assig = NULL WHERE enddate_assig = "0000-00-00 00:00:00"');

        $this->addSql('ALTER TABLE c_wiki CHANGE time_edit time_edit DATETIME');
        $this->addSql('UPDATE c_wiki SET time_edit = NULL WHERE time_edit = "0000-00-00 00:00:00"');

        $this->addSql('ALTER TABLE c_wiki CHANGE dtime dtime DATETIME');
        $this->addSql('UPDATE c_wiki SET dtime = NULL WHERE dtime = "0000-00-00 00:00:00"');

        $this->addSql('ALTER TABLE access_url CHANGE tms tms DATETIME');
        $this->addSql('UPDATE access_url SET tms = NULL WHERE tms = "0000-00-00 00:00:00"');

        $this->addSql('ALTER TABLE track_e_attempt CHANGE tms tms DATETIME');
        $this->addSql('UPDATE track_e_attempt SET tms = NULL WHERE tms = "0000-00-00 00:00:00"');

        $this->addSql('ALTER TABLE track_e_default CHANGE default_date default_date DATETIME');
        $this->addSql('UPDATE track_e_default SET default_date = NULL WHERE default_date = "0000-00-00 00:00:00"');

        $this->addSql('ALTER TABLE track_e_exercises CHANGE expired_time_control expired_time_control DATETIME');

        $this->addSql('DROP TABLE group_rel_user');
        $this->addSql('DROP TABLE group_rel_tag');
        $this->addSql('DROP TABLE group_rel_group');
        $this->addSql('DROP TABLE groups');

        if ($schema->hasTable('plugin_ticket_ticket')) {
            $this->addSql('ALTER TABLE plugin_ticket_ticket ADD COLUMN subject varchar(255) DEFAULT NULL;');
            $this->addSql('ALTER TABLE plugin_ticket_ticket ADD COLUMN message text NOT NULL;');
            $this->addSql('UPDATE plugin_ticket_ticket t INNER JOIN plugin_ticket_message as m  ON(t.ticket_id = m.ticket_id and message_id =1)  SET t.subject = m.subject');
            $this->addSql('UPDATE plugin_ticket_ticket t INNER JOIN plugin_ticket_message as m  ON(t.ticket_id = m.ticket_id and message_id =1)  SET t.message = m.message');
            $this->addSql('DELETE FROM plugin_ticket_message WHERE message_id = 1');

            $this->addSql('RENAME TABLE plugin_ticket_assigned_log TO ticket_assigned_log');
            $this->addSql('RENAME TABLE plugin_ticket_category TO ticket_category');
            $this->addSql('RENAME TABLE plugin_ticket_category_rel_user TO ticket_category_rel_user');
            $this->addSql('RENAME TABLE plugin_ticket_message TO ticket_message');
            $this->addSql('RENAME TABLE plugin_ticket_message_attachments TO ticket_message_attachments');
            $this->addSql('RENAME TABLE plugin_ticket_priority TO ticket_priority');
            $this->addSql('RENAME TABLE plugin_ticket_project TO ticket_project');
            $this->addSql('RENAME TABLE plugin_ticket_status TO ticket_status');
            $this->addSql('RENAME TABLE plugin_ticket_ticket TO ticket_ticket');

            $this->addSql('UPDATE ticket_project SET sys_insert_user_id = 1 WHERE sys_insert_user_id IS NULL');
            $this->addSql('UPDATE ticket_project SET sys_insert_datetime = NOW() WHERE sys_insert_datetime IS NULL');
            $this->addSql('ALTER TABLE ticket_project DROP project_id, CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE name name VARCHAR(255) NOT NULL, CHANGE description description LONGTEXT DEFAULT NULL, CHANGE email email VARCHAR(255) DEFAULT NULL, CHANGE other_area other_area INT DEFAULT NULL, CHANGE sys_insert_user_id sys_insert_user_id INT NOT NULL, CHANGE sys_insert_datetime sys_insert_datetime DATETIME NOT NULL, CHANGE sys_lastedit_user_id sys_lastedit_user_id INT DEFAULT NULL;');

            $this->addSql('ALTER TABLE ticket_status ADD code VARCHAR(255) NOT NULL, CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE name name VARCHAR(255) NOT NULL, CHANGE description description LONGTEXT DEFAULT NULL;');
            $this->addSql('UPDATE ticket_status SET code = status_id ');
            $this->addSql('ALTER TABLE ticket_status DROP status_id');
            $this->addSql('ALTER TABLE ticket_category_rel_user CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE category_id category_id INT DEFAULT NULL, CHANGE user_id user_id INT DEFAULT NULL;');


            $this->addSql('UPDATE ticket_category SET sys_insert_user_id = 1 WHERE sys_insert_user_id IS NULL');
            $this->addSql('UPDATE ticket_category SET sys_insert_datetime = NOW() WHERE sys_insert_datetime IS NULL');
            $this->addSql('UPDATE ticket_category SET course_required = 0 WHERE course_required IS NULL OR course_required = ""');

            $this->addSql('UPDATE ticket_category SET project_id = 1 WHERE project_id IS NULL OR project_id = ""');

            $this->addSql('ALTER TABLE ticket_category DROP category_id, CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE project_id project_id INT DEFAULT NULL, CHANGE name name VARCHAR(255) NOT NULL, CHANGE description description LONGTEXT DEFAULT NULL, CHANGE total_tickets total_tickets INT NOT NULL, CHANGE course_required course_required TINYINT(1) NOT NULL, CHANGE sys_insert_user_id sys_insert_user_id INT NOT NULL, CHANGE sys_insert_datetime sys_insert_datetime DATETIME NOT NULL, CHANGE sys_lastedit_user_id sys_lastedit_user_id INT DEFAULT NULL;');
            $this->addSql('ALTER TABLE ticket_category ADD CONSTRAINT FK_8325E540166D1F9C FOREIGN KEY (project_id) REFERENCES ticket_project (id);');
            $this->addSql('CREATE INDEX IDX_8325E540166D1F9C ON ticket_category (project_id);');

            $this->addSql('ALTER TABLE ticket_category_rel_user ADD CONSTRAINT FK_5B8A98712469DE2 FOREIGN KEY (category_id) REFERENCES ticket_category (id);');
            $this->addSql('ALTER TABLE ticket_category_rel_user ADD CONSTRAINT FK_5B8A987A76ED395 FOREIGN KEY (user_id) REFERENCES user (id);');

            $this->addSql('CREATE INDEX IDX_5B8A98712469DE2 ON ticket_category_rel_user (category_id);');
            $this->addSql('CREATE INDEX IDX_5B8A987A76ED395 ON ticket_category_rel_user (user_id);');

            $this->addSql('ALTER TABLE ticket_message_attachments DROP message_attch_id, CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE message_id message_id INT DEFAULT NULL, CHANGE ticket_id ticket_id INT DEFAULT NULL, CHANGE filename filename LONGTEXT NOT NULL, CHANGE size size INT NOT NULL, CHANGE sys_insert_user_id sys_insert_user_id INT NOT NULL, CHANGE sys_insert_datetime sys_insert_datetime DATETIME NOT NULL, CHANGE sys_lastedit_user_id sys_lastedit_user_id INT DEFAULT NULL;');

            // missing
            $this->addSql('ALTER TABLE ticket_message_attachments ADD CONSTRAINT FK_70BF9E26700047D2 FOREIGN KEY (ticket_id) REFERENCES ticket_ticket (id);');
            $this->addSql('CREATE INDEX IDX_70BF9E26700047D2 ON ticket_message_attachments (ticket_id);');
            $this->addSql('ALTER TABLE ticket_message_attachments RENAME INDEX ticket_message_id_fk TO IDX_70BF9E26537A1329;');


            $this->addSql('UPDATE ticket_priority SET sys_insert_user_id = 1 WHERE sys_insert_user_id IS NULL');
            $this->addSql('UPDATE ticket_priority SET sys_insert_datetime = NOW() WHERE sys_insert_datetime IS NULL');

            $this->addSql('ALTER TABLE ticket_priority ADD code VARCHAR(255) NOT NULL, ADD name VARCHAR(255) NOT NULL, ADD description LONGTEXT DEFAULT NULL, ADD color VARCHAR(255) NOT NULL, ADD urgency VARCHAR(255) NOT NULL, CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE sys_insert_user_id sys_insert_user_id INT NOT NULL, CHANGE sys_insert_datetime sys_insert_datetime DATETIME NOT NULL, CHANGE sys_lastedit_user_id sys_lastedit_user_id INT DEFAULT NULL;');

            $this->addSql('UPDATE ticket_priority SET code = priority_id');
            $this->addSql('UPDATE ticket_priority SET name = priority');
            $this->addSql('UPDATE ticket_priority SET description = priority_desc');
            $this->addSql('UPDATE ticket_priority SET color = priority_color');
            $this->addSql('UPDATE ticket_priority SET urgency = priority_urgency');

            $this->addSql('ALTER TABLE ticket_priority DROP priority_id, DROP priority, DROP priority_desc, DROP priority_color, DROP priority_urgency');


            $this->addSql('ALTER TABLE ticket_ticket MODIFY ticket_id INT UNSIGNED NOT NULL;');
            $this->addSql('DROP INDEX UN_ticket_code ON ticket_ticket;');
            $this->addSql('DROP INDEX FK_ticket_category ON ticket_ticket;');
            $this->addSql('ALTER TABLE ticket_ticket DROP PRIMARY KEY;');

            $this->addSql('UPDATE ticket_ticket t SET priority_id = (SELECT id FROM ticket_priority t2 WHERE t2.code = t.priority_id)');
            $this->addSql('UPDATE ticket_ticket t SET status_id = (SELECT id FROM ticket_status t2 WHERE t2.code = t.status_id)');

            $this->addSql('ALTER TABLE ticket_ticket ADD id INT NOT NULL, ADD code VARCHAR(255) NOT NULL, CHANGE project_id project_id INT DEFAULT NULL, CHANGE priority_id priority_id INT DEFAULT NULL, CHANGE course_id course_id INT DEFAULT NULL, CHANGE session_id session_id INT DEFAULT NULL, CHANGE personal_email personal_email VARCHAR(255) NOT NULL, CHANGE assigned_last_user assigned_last_user INT DEFAULT NULL, CHANGE status_id status_id INT DEFAULT NULL, CHANGE total_messages total_messages INT NOT NULL, CHANGE keyword keyword VARCHAR(255) DEFAULT NULL, CHANGE source source VARCHAR(255) DEFAULT NULL, CHANGE start_date start_date DATETIME DEFAULT NULL, CHANGE sys_insert_user_id sys_insert_user_id INT NOT NULL, CHANGE sys_insert_datetime sys_insert_datetime DATETIME NOT NULL, CHANGE sys_lastedit_user_id sys_lastedit_user_id INT DEFAULT NULL, CHANGE subject subject VARCHAR(255) NOT NULL, CHANGE message message LONGTEXT DEFAULT NULL;');

            $this->addSql('UPDATE ticket_ticket SET code = ticket_code');
            $this->addSql('UPDATE ticket_ticket SET id = ticket_id');
            $this->addSql('ALTER TABLE ticket_ticket DROP ticket_id, DROP ticket_code, DROP request_user');
            $this->addSql('ALTER TABLE ticket_ticket MODIFY COLUMN id INT NOT NULL PRIMARY KEY AUTO_INCREMENT');
            $this->addSql('ALTER TABLE ticket_ticket ADD CONSTRAINT FK_EDE2C768497B19F9 FOREIGN KEY (priority_id) REFERENCES ticket_priority (id);');

            $this->addSql('UPDATE ticket_ticket SET project_id = 1 WHERE project_id is NULL or project_id = 0');
            $this->addSql('ALTER TABLE ticket_ticket ADD CONSTRAINT FK_EDE2C768166D1F9C FOREIGN KEY (project_id) REFERENCES ticket_project (id);');

            $this->addSql('UPDATE ticket_ticket SET course_id = NULL WHERE course_id = 0');
            $this->addSql('ALTER TABLE ticket_ticket ADD CONSTRAINT FK_EDE2C768591CC992 FOREIGN KEY (course_id) REFERENCES course (id);');

            $this->addSql('UPDATE ticket_ticket SET session_id = NULL WHERE session_id = 0');
            $this->addSql('ALTER TABLE ticket_ticket ADD CONSTRAINT FK_EDE2C768613FECDF FOREIGN KEY (session_id) REFERENCES session (id);');

            $this->addSql('ALTER TABLE ticket_ticket ADD CONSTRAINT FK_EDE2C7686BF700BD FOREIGN KEY (status_id) REFERENCES ticket_status (id);');
            $this->addSql('CREATE INDEX IDX_EDE2C768166D1F9C ON ticket_ticket (project_id);');
            $this->addSql('CREATE INDEX IDX_EDE2C768591CC992 ON ticket_ticket (course_id);');
            $this->addSql('CREATE INDEX IDX_EDE2C768613FECDF ON ticket_ticket (session_id);');
            $this->addSql('CREATE INDEX IDX_EDE2C7686BF700BD ON ticket_ticket (status_id);');

            $this->addSql('ALTER TABLE ticket_ticket RENAME INDEX fk_ticket_priority TO IDX_EDE2C768497B19F9;');
            $this->addSql('ALTER TABLE ticket_assigned_log CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE ticket_id ticket_id INT DEFAULT NULL, CHANGE user_id user_id INT DEFAULT NULL, CHANGE assigned_date assigned_date DATETIME NOT NULL, CHANGE sys_insert_user_id sys_insert_user_id INT NOT NULL;');
            $this->addSql('ALTER TABLE ticket_assigned_log ADD CONSTRAINT FK_54B65868700047D2 FOREIGN KEY (ticket_id) REFERENCES ticket_ticket (id);');

            $this->addSql('DELETE FROM ticket_assigned_log WHERE user_id = 0 OR user_id IS NULL');

            $this->addSql('ALTER TABLE ticket_assigned_log ADD CONSTRAINT FK_54B65868A76ED395 FOREIGN KEY (user_id) REFERENCES user (id);');
            $this->addSql('CREATE INDEX IDX_54B65868A76ED395 ON ticket_assigned_log (user_id);');
            $this->addSql('ALTER TABLE ticket_assigned_log RENAME INDEX fk_ticket_assigned_log TO IDX_54B65868700047D2;');

            $this->addSql('ALTER TABLE ticket_category_rel_user RENAME INDEX fk_5b8a98712469de2 TO IDX_5B8A98712469DE2;');
            $this->addSql('ALTER TABLE ticket_category_rel_user RENAME INDEX fk_5b8a987a76ed395 TO IDX_5B8A987A76ED395;');

            $this->addSql('ALTER TABLE ticket_message DROP message_id, CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE ticket_id ticket_id INT DEFAULT NULL, CHANGE subject subject VARCHAR(255) DEFAULT NULL, CHANGE message message LONGTEXT DEFAULT NULL, CHANGE status status VARCHAR(255) NOT NULL, CHANGE ip_address ip_address VARCHAR(255) NOT NULL, CHANGE sys_insert_user_id sys_insert_user_id INT NOT NULL, CHANGE sys_insert_datetime sys_insert_datetime DATETIME NOT NULL, CHANGE sys_lastedit_user_id sys_lastedit_user_id INT DEFAULT NULL;');

            $this->addSql('ALTER TABLE ticket_message ADD CONSTRAINT FK_BA71692D700047D2 FOREIGN KEY (ticket_id) REFERENCES ticket_ticket (id);');
            $this->addSql('ALTER TABLE ticket_message RENAME INDEX fk_tick_message TO IDX_BA71692D700047D2;');
            $this->addSql('ALTER TABLE ticket_ticket CHANGE category_id category_id INT DEFAULT NULL;');
            $this->addSql('ALTER TABLE ticket_ticket ADD CONSTRAINT FK_EDE2C76812469DE2 FOREIGN KEY (category_id) REFERENCES ticket_category (id);');
            $this->addSql('CREATE INDEX IDX_EDE2C76812469DE2 ON ticket_ticket (category_id);');

            $this->addSql('ALTER TABLE ticket_message_attachments ADD CONSTRAINT FK_70BF9E26537A1329 FOREIGN KEY (message_id) REFERENCES ticket_message (id);');
            $this->addSql('DELETE FROM settings_current WHERE title = "Ticket"');
        }

        $this->addSql("INSERT INTO settings_current (variable, subkey, type, category, selected_value, title, comment, scope, subkeytext, access_url_changeable) VALUES ('ticket_allow_student_add', NULL, 'radio','Ticket', 'false','TicketAllowStudentAddTitle','TicketAllowStudentAddComment',NULL,NULL, 0)");
        $this->addSql("INSERT INTO settings_current (variable, subkey, type, category, selected_value, title, comment, scope, subkeytext, access_url_changeable) VALUES ('ticket_allow_category_edition', NULL, 'radio','Ticket', 'false','TicketAllowCategoryEditionTitle','TicketAllowCategoryEditionComment',NULL,NULL, 0)");
        $this->addSql("INSERT INTO settings_current (variable, subkey, type, category, selected_value, title, comment, scope, subkeytext, access_url_changeable) VALUES ('ticket_send_warning_to_all_admins', NULL, 'radio','Ticket', 'false','TicketSendWarningToAllAdminsTitle','TicketSendWarningToAllAdminsComment',NULL,NULL, 0)");
        $this->addSql("INSERT INTO settings_current (variable, subkey, type, category, selected_value, title, comment, scope, subkeytext, access_url_changeable) VALUES ('ticket_warn_admin_no_user_in_category', NULL, 'radio','Ticket', 'false','TicketWarnAdminNoUserInCategoryTitle','TicketWarnAdminNoUserInCategoryComment',NULL,NULL, 0)");
        $this->addSql("INSERT INTO settings_current (variable, subkey, type, category, selected_value, title, comment, scope, subkeytext, access_url_changeable) VALUES ('load_term_conditions_section', NULL, 'radio','Platform', 'login','LoadTermConditionsSectionTitle','LoadTermConditionsSectionDescription',NULL,NULL, 0)");
        $this->addSql("INSERT INTO settings_current (variable, subkey, type, category, selected_value, title, comment, scope, subkeytext, access_url_changeable) VALUES ('show_terms_if_profile_completed', NULL, 'radio','Ticket', 'false','ShowTermsIfProfileCompletedTitle','ShowTermsIfProfileCompletedComment',NULL,NULL, 0)");
        $this->addSql("INSERT INTO settings_current (variable, subkey, type, category, selected_value, title, comment, scope, subkeytext, access_url_changeable) VALUES ('show_link_ticket_notification', NULL, 'radio', 'Platform', 'false', 'ShowLinkTicketNotificationTitle', 'ShowLinkTicketNotificationComment', NULL, NULL, 0)");
        $this->addSql("INSERT INTO settings_current (variable, subkey, type, category, selected_value, title, comment, access_url) VALUES ('sso_authentication_subclass', NULL, 'textfield', 'Security', '', 'SSOSubclassTitle', 'SSOSubclassComment', 1)");


        $this->addSql("INSERT INTO settings_options (variable, value, display_text) VALUES ('ticket_allow_student_add', 'true', 'Yes'), ('ticket_allow_student_add', 'false', 'No')");
        $this->addSql("INSERT INTO settings_options (variable, value, display_text) VALUES ('ticket_allow_category_edition', 'true', 'Yes'), ('ticket_allow_category_edition', 'false', 'No')");
        $this->addSql("INSERT INTO settings_options (variable, value, display_text) VALUES ('ticket_send_warning_to_all_admins', 'true', 'Yes'), ('ticket_send_warning_to_all_admins', 'false', 'No')");
        $this->addSql("INSERT INTO settings_options (variable, value, display_text) VALUES ('ticket_warn_admin_no_user_in_category', 'true', 'Yes'), ('ticket_warn_admin_no_user_in_category', 'false', 'No')");
        $this->addSql("INSERT INTO settings_options (variable, value, display_text) VALUES ('load_term_conditions_section', 'login', 'Login'), ('load_term_conditions_section', 'course', 'Course')");
        $this->addSql("INSERT INTO settings_options (variable, value, display_text) VALUES ('show_terms_if_profile_completed', 'true', 'Yes'), ('show_terms_if_profile_completed', 'false', 'No')");
        $this->addSql("INSERT INTO settings_options (variable, value, display_text) VALUES ('show_link_ticket_notification', 'true', 'Yes'), ('show_link_ticket_notification', 'false', 'No')");

        $this->addSql("ALTER TABLE c_quiz_question_rel_category ADD INDEX idx_qqrc_qid (question_id)");
        $table = $schema->getTable('c_quiz_answer');
        $hasIndex = $table->hasIndex('idx_cqa_q');
        if (!$hasIndex) {
            $this->addSql("ALTER TABLE c_quiz_answer ADD INDEX idx_cqa_q (question_id)");
        }
        $this->addSql("ALTER TABLE c_student_publication ADD INDEX idx_csp_u (user_id)");

        $this->addSql('ALTER TABLE legal MODIFY COLUMN language_id INT NOT NULL');
        $this->addSql('ALTER TABLE legal MODIFY COLUMN legal_id INT NOT NULL');
        $this->addSql('ALTER TABLE legal DROP PRIMARY KEY;');
        $this->addSql('ALTER TABLE legal ADD id INT');
        $this->addSql('UPDATE legal SET id = legal_id');
        $this->addSql('UPDATE legal SET id = 1 WHERE id = 0');
        $this->addSql('ALTER TABLE legal DROP legal_id');
        $this->addSql('ALTER TABLE legal CHANGE id id INT AUTO_INCREMENT NOT NULL PRIMARY KEY;');

        $this->addSql('ALTER TABLE user ADD profile_completed TINYINT(1) DEFAULT NULL;');
        $this->addSql('ALTER TABLE extra_field_options CHANGE display_text display_text VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE extra_field CHANGE variable variable VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE c_course_setting MODIFY COLUMN value TEXT');
        $this->addSql("ALTER TABLE session MODIFY COLUMN name VARCHAR(150) NOT NULL DEFAULT ''");

        if (!$schema->hasTable('version')) {
            $this->addSql('CREATE TABLE version (id int unsigned NOT NULL AUTO_INCREMENT, version varchar(255), PRIMARY KEY(id), UNIQUE(version));');
        }

        if ($schema->hasTable('resource')) {
            $schema->dropTable('resource');
        }

        $this->addSql('DELETE FROM settings_current WHERE variable = "service_visio"');
        $this->addSql('DELETE FROM settings_current WHERE variable = "course_create_active_tools" AND subkey = "online_conference"');
        $this->addSql('DELETE FROM settings_options WHERE variable = "visio_use_rtmpt"');
        $this->addSql('DELETE FROM course_module WHERE name = "conference"');
    }

    /**
     * @param Schema $schema
     */
    public function postUp(Schema $schema)
    {
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql('DROP TABLE c_lp_category_user');
        $this->addSql('DROP TABLE access_url_rel_course_category');
    }
}
