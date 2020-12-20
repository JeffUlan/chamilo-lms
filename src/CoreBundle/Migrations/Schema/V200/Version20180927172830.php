<?php

/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Migrations\Schema\V200;

use Chamilo\CoreBundle\Migrations\AbstractMigrationChamilo;
use Doctrine\DBAL\Schema\Schema;

class Version20180927172830 extends AbstractMigrationChamilo
{
    public function getDescription(): string
    {
        return 'Migrate c_forum_forum';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->getTable('c_forum_post');
        if (!$table->hasIndex('c_id_visible_post_date')) {
            $this->addSql('CREATE INDEX c_id_visible_post_date ON c_forum_post (c_id, visible, post_date)');
        }

        if (false === $table->hasForeignKey('FK_B5BEF559E2904019')) {
            $this->addSql(
                'ALTER TABLE c_forum_post ADD CONSTRAINT FK_B5BEF559E2904019 FOREIGN KEY (thread_id) REFERENCES c_forum_thread (iid)'
            );
        }

        if ($table->hasColumn('poster_name')) {
            $this->addSql('ALTER TABLE c_forum_post DROP poster_name');
        }

        if ($table->hasIndex('poster_id')) {
            $this->addSql('DROP INDEX poster_id ON c_forum_post;');
        }

        if (false === $table->hasForeignKey('FK_B5BEF5595BB66C05')) {
            $this->addSql('ALTER TABLE c_forum_post ADD CONSTRAINT FK_B5BEF5595BB66C05 FOREIGN KEY (poster_id) REFERENCES user (id);');
            $this->addSql('CREATE INDEX IDX_B5BEF5595BB66C05 ON c_forum_post (poster_id)');
        }

        $this->addSql('UPDATE c_forum_post SET thread_id = NULL WHERE thread_id NOT IN (SELECT iid FROM c_forum_thread)');
        $this->addSql('UPDATE c_forum_thread SET forum_id = NULL WHERE forum_id NOT IN (SELECT iid FROM c_forum_forum)');
        $this->addSql('UPDATE c_forum_forum SET forum_category = NULL WHERE forum_category NOT IN (SELECT iid FROM c_forum_category)');

        $table = $schema->getTable('c_forum_forum');
        if (false === $table->hasForeignKey('FK_47A9C9921BF9426')) {
            $this->addSql(
                'ALTER TABLE c_forum_forum ADD CONSTRAINT FK_47A9C9921BF9426 FOREIGN KEY (forum_category) REFERENCES c_forum_category (iid) ON DELETE SET NULL'
            );
        }

        if (false === $table->hasIndex('IDX_47A9C9921BF9426')) {
            $this->addSql('CREATE INDEX IDX_47A9C9921BF9426 ON c_forum_forum (forum_category)');
        }

        if (false === $table->hasForeignKey('FK_47A9C99F2E82C87')) {
            $this->addSql('ALTER TABLE c_forum_forum ADD CONSTRAINT FK_47A9C99F2E82C87 FOREIGN KEY (forum_last_post) REFERENCES c_forum_post (iid)');
            $this->addSql('CREATE INDEX IDX_47A9C99F2E82C87 ON c_forum_forum (forum_last_post);');
        }



        $table = $schema->getTable('c_forum_thread');
        if (false === $table->hasForeignKey('FK_5DA7884C29CCBAD0')) {
            $this->addSql('ALTER TABLE c_forum_thread ADD CONSTRAINT FK_5DA7884C29CCBAD0 FOREIGN KEY (forum_id) REFERENCES c_forum_forum (iid)');
        }

        if ($table->hasColumn('thread_poster_name')) {
            $this->addSql('ALTER TABLE c_forum_thread DROP thread_poster_name');
        }

        if (false === $table->hasForeignKey('FK_5DA7884C43CB876D')) {
            $this->addSql('ALTER TABLE c_forum_thread ADD CONSTRAINT FK_5DA7884C43CB876D FOREIGN KEY (thread_last_post) REFERENCES c_forum_post (iid)');
            $this->addSql('CREATE INDEX IDX_5DA7884C43CB876D ON c_forum_thread (thread_last_post);');
        }

        if (false === $table->hasForeignKey('FK_5DA7884CD4DC43B9')) {
            $this->addSql('ALTER TABLE c_forum_thread ADD CONSTRAINT FK_5DA7884CD4DC43B9 FOREIGN KEY (thread_poster_id) REFERENCES user (id);');
            $this->addSql('CREATE INDEX IDX_5DA7884CD4DC43B9 ON c_forum_thread (thread_poster_id);');
        }
    }
}
