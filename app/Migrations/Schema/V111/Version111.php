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

    }
}
