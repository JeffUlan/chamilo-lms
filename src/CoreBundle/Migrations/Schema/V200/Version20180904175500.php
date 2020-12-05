<?php

/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Migrations\Schema\V200;

use Chamilo\CoreBundle\Migrations\AbstractMigrationChamilo;
use Doctrine\DBAL\Schema\Schema;

/**
 * Track changes.
 */
class Version20180904175500 extends AbstractMigrationChamilo
{
    public function up(Schema $schema): void
    {
        $this->addSql('DELETE FROM track_e_exercises WHERE exe_user_id = 0 OR exe_user_id IS NULL');
        $this->addSql('ALTER TABLE track_e_exercises CHANGE exe_user_id exe_user_id INT NOT NULL');

        $this->addSql('UPDATE track_e_exercises SET session_id = 0 WHERE session_id IS NULL');
        $this->addSql('ALTER TABLE track_e_exercises CHANGE session_id session_id INT NOT NULL');

        $table = $schema->getTable('track_e_login');
        if (!$table->hasIndex('idx_track_e_login_date')) {
            $this->addSql('CREATE INDEX idx_track_e_login_date ON track_e_login (login_date)');
        }

        $table = $schema->getTable('track_e_default');
        if (!$table->hasIndex('idx_default_user_id')) {
            $this->addSql('CREATE INDEX idx_default_user_id ON track_e_default (default_user_id)');
        }

        $table = $schema->getTable('track_e_course_access');
        if (!$table->hasIndex('user_course_session_date')) {
            $this->addSql(
                'CREATE INDEX user_course_session_date ON track_e_course_access (user_id, c_id, session_id, login_course_date)'
            );
        }

        $table = $schema->getTable('track_e_access');
        if (!$table->hasIndex('user_course_session_date')) {
            $this->addSql(
                'CREATE INDEX user_course_session_date ON track_e_access (access_user_id, c_id, access_session_id, access_date)'
            );
        }

        $table = $schema->hasTable('track_e_access_complete');
        if (false === $table) {
            $this->addSql(
                'CREATE TABLE track_e_access_complete (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, date_reg DATETIME NOT NULL, tool VARCHAR(255) NOT NULL, tool_id INT NOT NULL, tool_id_detail INT NOT NULL, action VARCHAR(255) NOT NULL, action_details VARCHAR(255) NOT NULL, current_id INT NOT NULL, ip_user VARCHAR(255) NOT NULL, user_agent VARCHAR(255) NOT NULL, session_id INT NOT NULL, c_id INT NOT NULL, ch_sid VARCHAR(255) NOT NULL, login_as INT NOT NULL, info LONGTEXT NOT NULL, url LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB ROW_FORMAT = DYNAMIC;'
            );
        }
        //$this->addSql('ALTER TABLE track_e_hotpotatoes CHANGE exe_result score SMALLINT NOT NULL');
        //$this->addSql('ALTER TABLE track_e_hotpotatoes CHANGE exe_weighting max_score SMALLINT NOT NULL');

        $table = $schema->getTable('track_e_exercises');
        if ($table->hasColumn('exe_weighting')) {
            $this->addSql('ALTER TABLE track_e_exercises CHANGE exe_weighting max_score DOUBLE PRECISION NOT NULL');
        }
        if ($table->hasColumn('exe_result')) {
            $this->addSql('ALTER TABLE track_e_exercises CHANGE exe_result score DOUBLE PRECISION NOT NULL');
        }

        $table = $schema->getTable('track_e_hotspot');
        if (false === $table->hasForeignKey('FK_A89CC3B691D79BD3')) {
            $this->addSql(
                'ALTER TABLE track_e_hotspot ADD CONSTRAINT FK_A89CC3B691D79BD3 FOREIGN KEY (c_id) REFERENCES course (id)'
            );
        }
        if (false === $table->hasIndex('IDX_A89CC3B691D79BD3')) {
            $this->addSql('CREATE INDEX IDX_A89CC3B691D79BD3 ON track_e_hotspot (c_id)');
        }

        $table = $schema->getTable('track_e_attempt');
        if (false === $table->hasColumn('c_id')) {
            $this->addSql('ALTER TABLE track_e_attempt CHANGE c_id c_id INT DEFAULT NULL');
            if (false === $table->hasForeignKey('FK_F8C342C391D79BD3')) {
                $this->addSql(
                    'ALTER TABLE track_e_attempt ADD CONSTRAINT FK_F8C342C391D79BD3 FOREIGN KEY (c_id) REFERENCES course (id)'
                );
            }
        }
        if (!$table->hasIndex('idx_track_e_attempt_tms')) {
            $this->addSql('CREATE INDEX idx_track_e_attempt_tms ON track_e_attempt (tms)');
        }


    }

    public function down(Schema $schema): void
    {
    }
}
