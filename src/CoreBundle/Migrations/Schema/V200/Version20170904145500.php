<?php

/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Migrations\Schema\V200;

use Chamilo\CoreBundle\Migrations\AbstractMigrationChamilo;
use Doctrine\DBAL\Schema\Schema;

/**
 * Quiz changes.
 */
class Version20170904145500 extends AbstractMigrationChamilo
{
    public function up(Schema $schema): void
    {
        if (false === $schema->hasTable('c_exercise_category')) {
            $this->addSql(
                'CREATE TABLE c_exercise_category (id BIGINT AUTO_INCREMENT NOT NULL, c_id INT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, position INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB'
            );
            $this->addSql('ALTER TABLE c_exercise_category ADD resource_node_id INT DEFAULT NULL');
            $this->addSql(
                'ALTER TABLE c_exercise_category ADD CONSTRAINT FK_B94C157E91D79BD3 FOREIGN KEY (c_id) REFERENCES course (id)'
            );
            $this->addSql(
                'ALTER TABLE c_exercise_category ADD CONSTRAINT FK_B94C157E1BAD783F FOREIGN KEY (resource_node_id) REFERENCES resource_node (id) ON DELETE CASCADE'
            );
            $this->addSql('CREATE INDEX IDX_B94C157E91D79BD3 ON c_exercise_category (c_id)');
            $this->addSql('CREATE UNIQUE INDEX UNIQ_B94C157E1BAD783F ON c_exercise_category (resource_node_id)');
        }

        // c_quiz.
        $table = $schema->getTable('c_quiz');
        if ($table->hasColumn('exercise_category_id')) {
            $this->addSql('ALTER TABLE c_quiz CHANGE exercise_category_id exercise_category_id BIGINT DEFAULT NULL;');
        } else {
            $this->addSql('ALTER TABLE c_quiz ADD COLUMN exercise_category_id BIGINT DEFAULT NULL;');
        }
        if (!$table->hasColumn('autolaunch')) {
            $this->addSql('ALTER TABLE c_quiz ADD autolaunch TINYINT(1) DEFAULT 0');
        }
        if (false === $table->hasForeignKey('FK_B7A1C35FB48D66')) {
            $this->addSql(
                'ALTER TABLE c_quiz ADD CONSTRAINT FK_B7A1C35FB48D66 FOREIGN KEY (exercise_category_id) REFERENCES c_exercise_category (id);'
            );
        }
        if (false === $table->hasIndex('IDX_B7A1C35FB48D66')) {
            $this->addSql('CREATE INDEX IDX_B7A1C35FB48D66 ON c_quiz (exercise_category_id);');
        }

        if (false === $table->hasColumn('show_previous_button')) {
            $this->addSql(
                'ALTER TABLE c_quiz ADD COLUMN show_previous_button TINYINT(1) DEFAULT 1;'
            );
        }

        if (false === $table->hasColumn('notifications')) {
            $this->addSql(
                'ALTER TABLE c_quiz ADD COLUMN notifications VARCHAR(255) NULL DEFAULT NULL;'
            );
        }

        if (false === $table->hasColumn('page_result_configuration')) {
            $this->addSql(
                "ALTER TABLE c_quiz ADD page_result_configuration LONGTEXT DEFAULT NULL COMMENT '(DC2Type:array)'"
            );
        }

        $this->addSql('ALTER TABLE c_quiz MODIFY COLUMN save_correct_answers INT NULL DEFAULT NULL');
        if ($table->hasForeignKey('FK_B7A1C35FB48D66')) {
            $this->addSql('ALTER TABLE c_quiz DROP FOREIGN KEY FK_B7A1C35FB48D66');
        }

        $this->addSql('ALTER TABLE c_quiz CHANGE type type INT NOT NULL');

        if (false === $table->hasColumn('resource_node_id')) {
            $this->addSql('ALTER TABLE c_quiz ADD COLUMN resource_node_id INT DEFAULT NULL');
            $this->addSql(
                'ALTER TABLE c_quiz ADD CONSTRAINT FK_B7A1C31BAD783F FOREIGN KEY (resource_node_id) REFERENCES resource_node (id) ON DELETE CASCADE'
            );
            $this->addSql('CREATE UNIQUE INDEX UNIQ_B7A1C31BAD783F ON c_quiz (resource_node_id)');
        }

        if (false === $table->hasColumn('prevent_backwards')) {
            $this->addSql(
                'ALTER TABLE c_quiz ADD prevent_backwards INT DEFAULT 0 NOT NULL, CHANGE type type INT NOT NULL, CHANGE id resource_node_id INT DEFAULT NULL'
            );
        }

        if ($table->hasForeignKey('FK_B7A1C35FB48D66')) {
            $this->addSql(
                'ALTER TABLE c_quiz ADD CONSTRAINT FK_B7A1C35FB48D66 FOREIGN KEY (exercise_category_id) REFERENCES c_exercise_category (id) ON DELETE SET NULL'
            );
        }

        // answer
        $table = $schema->getTable('c_quiz_answer');
        if ($table->hasColumn('id_auto')) {
            $this->addSql('ALTER TABLE c_quiz_answer DROP id_auto, DROP id');
        }
        if ($table->hasColumn('id')) {
            $this->addSql('ALTER TABLE c_quiz_answer DROP id');
        }

        // c_quiz_question.
        $table = $schema->getTable('c_quiz_question');
        if (false === $table->hasColumn('resource_node_id')) {
            $this->addSql('ALTER TABLE c_quiz_question ADD resource_node_id INT DEFAULT NULL;');
            $this->addSql(
                'ALTER TABLE c_quiz_question ADD CONSTRAINT FK_9A48A59F1BAD783F FOREIGN KEY (resource_node_id) REFERENCES resource_node (id) ON DELETE CASCADE;'
            );
            $this->addSql('CREATE UNIQUE INDEX UNIQ_9A48A59F1BAD783F ON c_quiz_question (resource_node_id);');
        }

        if (false === $table->hasColumn('mandatory')) {
            $this->addSql('ALTER TABLE c_quiz_question ADD mandatory INT NOT NULL, DROP id');
        }
        if ($table->hasColumn('id')) {
            $this->addSql('ALTER TABLE c_quiz_question DROP id');
        }

        if (false === $table->hasColumn('feedback')) {
            $this->addSql('ALTER TABLE c_quiz_question ADD feedback LONGTEXT DEFAULT NULL;');
        }

        // c_quiz_question_category.
        $table = $schema->getTable('c_quiz_question_category');
        if (false === $table->hasColumn('session_id')) {
            $this->addSql('ALTER TABLE c_quiz_question_category ADD session_id INT DEFAULT NULL');
            if (false === $table->hasIndex('IDX_1414369D613FECDF')) {
                $this->addSql('CREATE INDEX IDX_1414369D613FECDF ON c_quiz_question_category (session_id)');
            }
            if (false === $table->hasForeignKey('FK_1414369D613FECDF')) {
                $this->addSql(
                    'ALTER TABLE c_quiz_question_category ADD CONSTRAINT FK_1414369D613FECDF FOREIGN KEY (session_id) REFERENCES session (id)'
                );
            }
        }
        $this->addSql('ALTER TABLE c_quiz_question_category CHANGE description description LONGTEXT DEFAULT NULL;');

        if (false === $table->hasForeignKey('FK_1414369D91D79BD3')) {
            $this->addSql(
                'ALTER TABLE c_quiz_question_category ADD CONSTRAINT FK_1414369D91D79BD3 FOREIGN KEY (c_id) REFERENCES course (id);'
            );
        }

        $table = $schema->getTable('c_quiz_question_option');
        if ($table->hasColumn('id')) {
            $this->addSql('ALTER TABLE c_quiz_question_option DROP id');
        }

        $table = $schema->getTable('c_quiz_rel_question');
        $this->addSql(
            'ALTER TABLE c_quiz_rel_question CHANGE question_id question_id INT DEFAULT NULL, CHANGE exercice_id exercice_id INT DEFAULT NULL'
        );
        if (false === $table->hasForeignKey('FK_485736AC1E27F6BF')) {
            $this->addSql(
                'ALTER TABLE c_quiz_rel_question ADD CONSTRAINT FK_485736AC1E27F6BF FOREIGN KEY (question_id) REFERENCES c_quiz_question (iid)'
            );
        }
        if (false === $table->hasForeignKey('FK_485736AC89D40298')) {
            $this->addSql(
                'ALTER TABLE c_quiz_rel_question ADD CONSTRAINT FK_485736AC89D40298 FOREIGN KEY (exercice_id) REFERENCES c_quiz (iid)'
            );
        }
    }

    public function down(Schema $schema): void
    {
    }
}
