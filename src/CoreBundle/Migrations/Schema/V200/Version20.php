<?php

/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Migrations\Schema\V200;

use Chamilo\CoreBundle\Migrations\AbstractMigrationChamilo;
use Doctrine\DBAL\Schema\Schema;

/**
 * Class Version20
 * Migrate file to updated to Chamilo 2.0.
 */
class Version20 extends AbstractMigrationChamilo
{
    public function up(Schema $schema): void
    {
        // Use $schema->createTable
        $this->addSql('set sql_mode=""');

        // Global tool.
        if (false === $schema->hasTable('tool')) {
            $this->addSql(
                'CREATE TABLE IF NOT EXISTS tool (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;'
            );
            $this->addSql('CREATE UNIQUE INDEX UNIQ_20F33ED15E237E06 ON tool (name)');
        }

        $table = $schema->getTable('language');
        if ($table->hasIndex('idx_language_dokeos_folder')) {
            $this->addSql('DROP INDEX idx_language_dokeos_folder ON language;');
            $this->addSql('ALTER TABLE language DROP dokeos_folder;');
        }

        // Update language to ISO
        $this->addSql('UPDATE course SET course_language = (SELECT isocode FROM language WHERE english_name = course_language)');
        $this->addSql('UPDATE sys_announcement SET lang = (SELECT isocode FROM language WHERE english_name = lang);');
        $this->addSql('UPDATE user SET language = (SELECT isocode FROM language WHERE english_name = language)');
        $this->addSql("UPDATE settings_current SET selected_value = (SELECT isocode FROM language WHERE english_name = selected_value) WHERE variable = 'platformLanguage'");

        $table = $schema->getTable('fos_group');
        if (false === $table->hasColumn('name')) {
            $this->addSql(
                'ALTER TABLE fos_group ADD name VARCHAR(180) NOT NULL, ADD roles LONGTEXT NOT NULL COMMENT "(DC2Type:array)";'
            );
            $this->addSql('CREATE UNIQUE INDEX UNIQ_4B019DDB5E237E06 ON fos_group (name);');
        }
        $this->addSql('ALTER TABLE fos_group CHANGE name name VARCHAR(255) NOT NULL');

        $table = $schema->getTable('fos_user_user_group');
        if ($table->hasForeignKey('FK_B3C77447A76ED395')) {
            $this->addSql('ALTER TABLE fos_user_user_group DROP FOREIGN KEY FK_B3C77447A76ED395');
            $this->addSql('ALTER TABLE fos_user_user_group ADD CONSTRAINT FK_B3C77447A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        } else {
            $this->addSql('ALTER TABLE fos_user_user_group ADD CONSTRAINT FK_B3C77447A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        }

        $this->addSql('ALTER TABLE course_request CHANGE user_id user_id INT DEFAULT NULL;');
        $table = $schema->getTable('course_request');
        if (false === $table->hasForeignKey('FK_33548A73A76ED395')) {
            $this->addSql(
                'ALTER TABLE course_request ADD CONSTRAINT FK_33548A73A76ED395 FOREIGN KEY (user_id) REFERENCES user (id);'
            );
            $this->addSql('CREATE INDEX IDX_33548A73A76ED395 ON course_request (user_id);');
        }

        $table = $schema->getTable('search_engine_ref');
        if (false === $table->hasColumn('c_id')) {
            $this->addSql('ALTER TABLE search_engine_ref ADD c_id INT DEFAULT NULL');
            $this->addSql('UPDATE search_engine_ref SET c_id = (SELECT id FROM course WHERE code = course_code)');
            $this->addSql('ALTER TABLE search_engine_ref DROP course_code');
            $this->addSql(
                'ALTER TABLE search_engine_ref ADD CONSTRAINT FK_473F037891D79BD3 FOREIGN KEY (c_id) REFERENCES course (id);'
            );
            $this->addSql('CREATE INDEX IDX_473F037891D79BD3 ON search_engine_ref (c_id);');
        }

        $this->addSql('ALTER TABLE hook_observer CHANGE class_name class_name VARCHAR(190) DEFAULT NULL');
        $this->addSql('ALTER TABLE hook_event CHANGE class_name class_name VARCHAR(190) DEFAULT NULL;');
        $this->addSql('ALTER TABLE sequence_value CHANGE user_id user_id INT DEFAULT NULL');

        $table = $schema->getTable('sequence_value');
        if (false === $table->hasForeignKey('FK_66FBF12DA76ED395')) {
            $this->addSql(
                'ALTER TABLE sequence_value ADD CONSTRAINT FK_66FBF12DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE'
            );
        }

        if (false === $table->hasIndex('IDX_66FBF12DA76ED395')) {
            $this->addSql('CREATE INDEX IDX_66FBF12DA76ED395 ON sequence_value (user_id)');
        }

        $table = $schema->getTable('branch_sync');
        $this->addSql('ALTER TABLE branch_sync CHANGE access_url_id access_url_id INT DEFAULT NULL');
        if (false === $table->hasForeignKey('FK_F62F45ED73444FD5')) {
            $this->addSql('ALTER TABLE branch_sync ADD CONSTRAINT FK_F62F45ED73444FD5 FOREIGN KEY (access_url_id) REFERENCES access_url (id)');
        }
        if (false === $table->hasIndex('IDX_F62F45ED73444FD5')) {
            $this->addSql('CREATE INDEX IDX_F62F45ED73444FD5 ON branch_sync (access_url_id)');
        }

        $table = $schema->getTable('personal_agenda');
        if ($table->hasColumn('course')) {
            $this->addSql('ALTER TABLE personal_agenda DROP course');
        }
        if (false === $table->hasForeignKey('FK_D86124608D93D649')) {
            $this->addSql('ALTER TABLE personal_agenda ADD CONSTRAINT FK_D86124608D93D649 FOREIGN KEY (user) REFERENCES user (id) ON DELETE CASCADE');
        }

        //$this->addSql('ALTER TABLE c_tool_intro CHANGE id tool VARCHAR(255) NOT NULL');

        /*$table = $schema->getTable('course_rel_class');
        if (!$table->hasColumn('c_id')) {
            $this->addSql("ALTER TABLE course_rel_class ADD c_id int NOT NULL");
        }
        if ($table->hasColumn('course_code')) {
            $this->addSql("
                UPDATE course_rel_class cc
                SET cc.c_id = (SELECT id FROM course WHERE code = cc.course_code)
            ");

            $this->addSql("ALTER TABLE course_rel_class DROP course_code");
            $this->addSql("ALTER TABLE course_rel_class DROP PRIMARY KEY");
            $this->addSql("ALTER TABLE course_rel_class MODIFY COLUMN class_id INT DEFAULT NULL");
            $this->addSql("ALTER TABLE course_rel_class ADD PRIMARY KEY (class_id, c_id)");
            $this->addSql("ALTER TABLE course_rel_class ADD FOREIGN KEY (c_id) REFERENCES course (id) ON DELETE RESTRICT");
        }*/

        $tables = [
            'shared_survey',
            'specific_field_values',
            'templates',
        ];

        foreach ($tables as $table) {
            //$tableObj = $schema->getTable($table);
            /*if (!$tableObj->hasColumn('c_id')) {
                $this->addSql("ALTER TABLE $table ADD c_id int NOT NULL");

                if ($tableObj->hasColumn('course_code')) {
                    $this->addSql("
                      UPDATE $table t
                      SET t.c_id = (SELECT id FROM course WHERE code = t.course_code)
                    ");
                    $this->addSql("ALTER TABLE $table DROP course_code");
                }
            }*/
            /*$this->addSql("
                ALTER TABLE $table ADD FOREIGN KEY (c_id) REFERENCES course (id) ON DELETE RESTRICT
            ");*/
        }
        /*
                $this->addSql("ALTER TABLE personal_agenda DROP course");

                $this->addSql("
                    ALTER TABLE specific_field_values
                    ADD c_id int(11) NOT NULL,
                    ADD FOREIGN KEY (c_id) REFERENCES course (id) ON DELETE RESTRICT;
                ");
        */

        // Create tables.
        $this->addSql(
            'CREATE TABLE IF NOT EXISTS scheduled_announcements (id INT AUTO_INCREMENT NOT NULL, subject VARCHAR(255) NOT NULL, message LONGTEXT NOT NULL, date DATETIME DEFAULT NULL, sent TINYINT(1) NOT NULL, session_id INT NOT NULL, c_id INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;'
        );
        $this->addSql(
            'CREATE TABLE IF NOT EXISTS ext_translations (id INT AUTO_INCREMENT NOT NULL, locale VARCHAR(8) NOT NULL, object_class VARCHAR(190) NOT NULL, field VARCHAR(32) NOT NULL, foreign_key VARCHAR(64) NOT NULL, content LONGTEXT DEFAULT NULL, INDEX translations_lookup_idx (locale, object_class, foreign_key), UNIQUE INDEX lookup_unique_idx (locale, object_class, field, foreign_key), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;'
        );
        $this->addSql(
            'CREATE TABLE IF NOT EXISTS ext_log_entries (id INT AUTO_INCREMENT NOT NULL, action VARCHAR(8) NOT NULL, logged_at DATETIME NOT NULL, object_id VARCHAR(64) DEFAULT NULL, object_class VARCHAR(191) NOT NULL, version INT NOT NULL, data LONGTEXT DEFAULT NULL COMMENT "(DC2Type:array)", username VARCHAR(191) DEFAULT NULL, INDEX log_class_lookup_idx (object_class), INDEX log_date_lookup_idx (logged_at), INDEX log_user_lookup_idx (username), INDEX log_version_lookup_idx (object_id, object_class, version), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;'
        );
        $this->addSql(
            'CREATE TABLE IF NOT EXISTS mail_template (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, template LONGTEXT DEFAULT NULL, type VARCHAR(255) NOT NULL, score DOUBLE PRECISION DEFAULT NULL, result_id INT NOT NULL, default_template TINYINT(1) NOT NULL, `system` INT DEFAULT 0 NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB ROW_FORMAT = DYNAMIC'
        );

        $table = $schema->getTable('usergroup');
        if (!$table->hasColumn('author_id')) {
            $this->addSql('ALTER TABLE usergroup ADD author_id INT DEFAULT NULL');
        }

        // Update template.
        $table = $schema->getTable('templates');
        if ($table->hasColumn('course_code')) {
            $this->addSql('DELETE FROM templates WHERE course_code NOT IN (SELECT code FROM course)');
        }
        if (false === $table->hasColumn('c_id')) {
            $this->addSql('ALTER TABLE templates ADD c_id INT DEFAULT NULL');
            $this->addSql('CREATE INDEX IDX_6F287D8E91D79BD3 ON templates (c_id)');
            $this->addSql(
                'ALTER TABLE templates ADD CONSTRAINT FK_6F287D8E91D79BD3 FOREIGN KEY (c_id) REFERENCES course (id)'
            );
            $this->addSql('UPDATE templates SET c_id = (SELECT id FROM course WHERE code = course_code)');
        }
        $this->addSql('ALTER TABLE templates CHANGE user_id user_id INT DEFAULT NULL');

        if (false === $table->hasIndex('IDX_6F287D8EA76ED395')) {
            $this->addSql('CREATE INDEX IDX_6F287D8EA76ED395 ON templates (user_id)');
        }

        if (false === $table->hasForeignKey('FK_6F287D8EA76ED395')) {
            $this->addSql('ALTER TABLE templates ADD CONSTRAINT FK_6F287D8EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        }

        // Drop unused columns
        $dropColumnsAndIndex = [
            'track_e_uploads' => ['columns' => ['upload_cours_id'], 'index' => ['upload_cours_id']],
            'track_e_hotspot' => ['columns' => ['hotspot_course_code'], 'index' => ['hotspot_course_code']],
            'templates' => ['columns' => ['course_code'], 'index' => []],
            'personal_agenda' => ['columns' => ['hotspot_course_code'], 'index' => []],
        ];

        foreach ($dropColumnsAndIndex as $tableName => $data) {
            if ($schema->hasTable($tableName)) {
                $indexList = $data['index'];
                foreach ($indexList as $index) {
                    if ($table->hasIndex($index)) {
                        $table->dropIndex($index);
                    }
                }

                $columns = $data['columns'];
                $table = $schema->getTable($tableName);
                foreach ($columns as $column) {
                    if ($table->hasColumn($column)) {
                        $table->dropColumn($column);
                    }
                }
            }
        }

        // Drop tables
        $dropTables = [
            'event_email_template',
            'event_sent',
            'user_rel_event_type',
            'openid_association',
            'track_stored_values',
            'track_stored_values_stack',
            'course_module',
        ];
        foreach ($dropTables as $table) {
            if ($schema->hasTable($table)) {
                $schema->dropTable($table);
            }
        }
        $this->addSql('DROP TABLE c_resource');
        $this->addSql('DROP TABLE track_e_item_property');
    }

    public function down(Schema $schema): void
    {
    }
}
