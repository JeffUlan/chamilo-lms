<?php

namespace Chamilo\CoreBundle\Migrations\Schema\V200;

use Chamilo\CoreBundle\Migrations\AbstractMigrationChamilo;
use Doctrine\DBAL\Schema\Schema;

/**
 * Messages.
 */
final class Version20200821224242 extends AbstractMigrationChamilo
{
    public function up(Schema $schema): void
    {
        $this->addSql('DELETE FROM message WHERE user_sender_id IS NULL OR user_sender_id = 0');
        $this->addSql('ALTER TABLE message CHANGE user_receiver_id user_receiver_id INT DEFAULT NULL');
        $this->addSql('UPDATE message SET user_receiver_id = NULL WHERE user_receiver_id = 0');
        $this->addSql('DELETE FROM message WHERE user_sender_id NOT IN (SELECT id FROM user)');
        $this->addSql(
            'DELETE FROM message WHERE user_receiver_id IS NOT NULL AND user_receiver_id NOT IN (SELECT id FROM user)'
        );

        $table = $schema->getTable('message');
        if (false === $table->hasForeignKey('FK_B6BD307FF6C43E79')) {
            $this->addSql(
                'ALTER TABLE message ADD CONSTRAINT FK_B6BD307FF6C43E79 FOREIGN KEY (user_sender_id) REFERENCES user (id)'
            );
        }
        if (false === $table->hasForeignKey('FK_B6BD307F64482423')) {
            $this->addSql(
                'ALTER TABLE message ADD CONSTRAINT FK_B6BD307F64482423 FOREIGN KEY (user_receiver_id) REFERENCES user (id)'
            );
        }
        if (!$table->hasIndex('idx_message_user_receiver_status')) {
            $this->addSql('CREATE INDEX idx_message_user_receiver_status ON message (user_receiver_id, msg_status)');
        }

        if (!$table->hasIndex('idx_message_status')) {
            $this->addSql('CREATE INDEX idx_message_status ON message (msg_status)');
        }

        if (!$table->hasIndex('idx_message_receiver_status_send_date')) {
            $this->addSql(
                'CREATE INDEX idx_message_receiver_status_send_date ON message (user_receiver_id, msg_status, send_date)'
            );
        }

        $this->addSql('ALTER TABLE message CHANGE msg_status msg_status SMALLINT NOT NULL;');

        $table = $schema->hasTable('message_feedback');
        if (false === $table) {
            $this->addSql(
                'CREATE TABLE message_feedback (id BIGINT AUTO_INCREMENT NOT NULL, message_id BIGINT NOT NULL, user_id INT NOT NULL, liked TINYINT(1) DEFAULT 0 NOT NULL, disliked TINYINT(1) DEFAULT 0 NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_DB0F8049537A1329 (message_id), INDEX IDX_DB0F8049A76ED395 (user_id), INDEX idx_message_feedback_uid_mid (message_id, user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB ROW_FORMAT = DYNAMIC;'
            );
            $this->addSql(
                'ALTER TABLE message_feedback ADD CONSTRAINT FK_DB0F8049537A1329 FOREIGN KEY (message_id) REFERENCES message (id) ON DELETE CASCADE'
            );
            $this->addSql(
                'ALTER TABLE message_feedback ADD CONSTRAINT FK_DB0F8049A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE;'
            );
        }

        $table = $schema->getTable('message_attachment');
        if (false === $table->hasIndex('IDX_B68FF524537A1329')) {
            $this->addSql('CREATE INDEX IDX_B68FF524537A1329 ON message_attachment (message_id)');
        }
        $this->addSql('ALTER TABLE message_attachment CHANGE message_id message_id BIGINT NOT NULL');

        if (false === $table->hasForeignKey('FK_B68FF524537A1329')) {
            $this->addSql('ALTER TABLE message_attachment ADD CONSTRAINT FK_B68FF524537A1329 FOREIGN KEY (message_id) REFERENCES message (id)');
        }

        if (false === $schema->hasTable('c_chat_conversation')) {
            $this->addSql('CREATE TABLE c_chat_conversation (id INT AUTO_INCREMENT NOT NULL, resource_node_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_CD09E33F1BAD783F (resource_node_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB ROW_FORMAT = DYNAMIC;');
            $this->addSql('ALTER TABLE c_chat_conversation ADD CONSTRAINT FK_CD09E33F1BAD783F FOREIGN KEY (resource_node_id) REFERENCES resource_node (id) ON DELETE CASCADE');
        }
    }

    public function down(Schema $schema): void
    {
    }
}
