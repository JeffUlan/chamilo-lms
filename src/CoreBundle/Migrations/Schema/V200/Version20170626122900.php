<?php

/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Migrations\Schema\V200;

use Chamilo\CoreBundle\Migrations\AbstractMigrationChamilo;
use Doctrine\DBAL\Schema\Schema;

/**
 * User.
 */
class Version20170626122900 extends AbstractMigrationChamilo
{
    public function up(Schema $schema): void
    {
        $table = $schema->getTable('user');

        if ($table->hasIndex('idx_user_uid')) {
            $this->addSql('DROP INDEX idx_user_uid ON user;');
        }

        if ($table->hasIndex('UNIQ_8D93D649C05FB297')) {
            $this->addSql('DROP INDEX UNIQ_8D93D649C05FB297 ON user;');
        }

        if ($table->hasColumn('user_id')) {
            $this->addSql('ALTER TABLE user DROP user_id');
        }

        if (false === $table->hasColumn('resource_node_id')) {
            $this->addSql('ALTER TABLE user ADD resource_node_id INT DEFAULT NULL;');
            $this->addSql(
                'ALTER TABLE user ADD CONSTRAINT FK_8D93D6491BAD783F FOREIGN KEY (resource_node_id) REFERENCES resource_node (id) ON DELETE CASCADE;'
            );
            $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D6491BAD783F ON user (resource_node_id);');
        }

        if ($table->hasColumn('salt')) {
            $this->addSql('ALTER TABLE user CHANGE salt salt VARCHAR(255) NOT NULL');
        }

        if ($table->hasColumn('created_at')) {
            $this->addSql(
                'UPDATE user SET created_at = registration_date WHERE CAST(created_at AS CHAR(20)) = "0000-00-00 00:00:00"'
            );
            $this->addSql('UPDATE user SET created_at = registration_date WHERE created_at IS NULL');
            //$this->addSql('UPDATE user SET created_at = NOW() WHERE created_at = NULL OR created_at = ""');
            $this->addSql('ALTER TABLE user CHANGE created_at created_at DATETIME NOT NULL');
        }

        if ($table->hasColumn('updated_at')) {
            $this->addSql(
                'UPDATE user SET updated_at = registration_date WHERE CAST(updated_at AS CHAR(20)) = "0000-00-00 00:00:00"'
            );
            $this->addSql('UPDATE user SET updated_at = registration_date WHERE updated_at IS NULL');
            //$this->addSql('UPDATE user SET updated_at = NOW() WHERE updated_at = NULL OR updated_at = ""');
            $this->addSql('ALTER TABLE user CHANGE updated_at updated_at DATETIME NOT NULL');
        }

        if ($table->hasColumn('confirmation_token')) {
            $this->addSql('ALTER TABLE user CHANGE confirmation_token confirmation_token VARCHAR(255) DEFAULT NULL');
        }

        if ($table->hasColumn('website')) {
            $this->addSql('ALTER TABLE user CHANGE website website VARCHAR(255) DEFAULT NULL');
        } else {
            $this->addSql('ALTER TABLE user ADD website VARCHAR(255) DEFAULT NULL');
        }

        if (false === $table->hasColumn('api_token')) {
            $this->addSql('ALTER TABLE user ADD api_token VARCHAR(255) DEFAULT NULL');
            $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D6497BA2F5EB ON user (api_token);');
        }

        if (false === $table->hasColumn('date_of_birth')) {
            $this->addSql('ALTER TABLE user ADD date_of_birth DATETIME DEFAULT NULL');
        }
        if (false === $table->hasColumn('biography')) {
            $this->addSql('ALTER TABLE user ADD biography LONGTEXT DEFAULT NULL');
        }
        if (false === $table->hasColumn('gender')) {
            $this->addSql('ALTER TABLE user ADD gender VARCHAR(1) DEFAULT NULL');
        }
        if (false === $table->hasColumn('locale')) {
            $this->addSql('ALTER TABLE user ADD locale VARCHAR(8) DEFAULT NULL');
            $this->addSql('UPDATE user SET locale = (SELECT isocode FROM language WHERE english_name = language)');
        }
        if (false === $table->hasColumn('timezone')) {
            $this->addSql('ALTER TABLE user ADD timezone VARCHAR(64) NOT NULL');
        }

        if (false === $table->hasColumn('confirmation_token')) {
            $this->addSql('ALTER TABLE user ADD confirmation_token VARCHAR(255) DEFAULT NULL');
            $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649C05FB297 ON user (confirmation_token)');
        } else {
            $this->addSql('ALTER TABLE user CHANGE confirmation_token confirmation_token VARCHAR(255) DEFAULT NULL');
        }

        $this->addSql('ALTER TABLE user CHANGE username_canonical username_canonical VARCHAR(180) NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE lastname lastname VARCHAR(64) DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE firstname firstname VARCHAR(64) DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE phone phone VARCHAR(64) DEFAULT NULL');

        $table = $schema->getTable('admin');
        $this->addSql('ALTER TABLE admin CHANGE user_id user_id INT DEFAULT NULL');
        if (false === $table->hasForeignKey('FK_880E0D76A76ED395')) {
            $this->addSql(
                'ALTER TABLE admin ADD CONSTRAINT FK_880E0D76A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE'
            );
        }

        if ($table->hasIndex('user_id')) {
            //$this->addSql('DROP INDEX user_id ON admin');
        }

        if (false === $table->hasIndex('UNIQ_880E0D76A76ED395')) {
            $this->addSql('CREATE UNIQUE INDEX UNIQ_880E0D76A76ED395 ON admin (user_id)');
        }

        $table = $schema->getTable('user_course_category');
        if (false === $table->hasColumn('collapsed')) {
            $this->addSql('ALTER TABLE user_course_category ADD collapsed TINYINT(1) DEFAULT NULL');
        }
        $this->addSql('ALTER TABLE user_course_category CHANGE user_id user_id INT DEFAULT NULL');

        if (false === $table->hasForeignKey('FK_BD241818A76ED395')) {
            $this->addSql(
                'ALTER TABLE user_course_category ADD CONSTRAINT FK_BD241818A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE'
            );
        }

        $table = $schema->getTable('user_rel_course_vote');
        $this->addSql('ALTER TABLE user_rel_course_vote CHANGE user_id user_id INT DEFAULT NULL');
        if (false === $table->hasForeignKey('FK_4038AA47A76ED395')) {
            $this->addSql(
                'ALTER TABLE user_rel_course_vote ADD CONSTRAINT FK_4038AA47A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE'
            );
        }

        $table = $schema->getTable('user_rel_tag');
        $this->addSql('ALTER TABLE user_rel_tag CHANGE user_id user_id INT DEFAULT NULL');
        if (false === $table->hasForeignKey('FK_D5CB75B6A76ED395')) {
            $this->addSql(
                'ALTER TABLE user_rel_tag ADD CONSTRAINT FK_D5CB75B6A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE'
            );
        }

        $table = $schema->getTable('user_rel_user');
        $this->addSql('ALTER TABLE user_rel_user CHANGE user_id user_id INT DEFAULT NULL');

        if (false === $table->hasForeignKey('FK_DBF650A8A76ED395')) {
            $this->addSql(
                'ALTER TABLE user_rel_user ADD CONSTRAINT FK_DBF650A8A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE'
            );
        }
    }

    public function down(Schema $schema): void
    {
    }

    public function getDescription(): string
    {
        return 'User changes';
    }
}
