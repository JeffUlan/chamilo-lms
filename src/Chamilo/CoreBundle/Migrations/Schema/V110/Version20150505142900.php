<?php
/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Migrations\Schema\V110;

use Chamilo\CoreBundle\Migrations\AbstractMigrationChamilo;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Type;

/**
 * Class Version20150505142900
 *
 * @package Chamilo\CoreBundle\Migrations\Schema\v1
 */
class Version20150505142900 extends AbstractMigrationChamilo
{
    /**
     * @param Schema $schema
     *
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    public function up(Schema $schema)
    {
        // Create table for video chat
        $chatVideoTable = $schema->createTable('chat_video');
        $chatVideoTable->addColumn(
            'id',
            Type::INTEGER,
            ['unsigned' => true, 'autoincrement' => true, 'notnull' => true]
        );
        $chatVideoTable->addColumn(
            'from_user',
            Type::INTEGER,
            ['unsigned' => true, 'notnull' => true]
        );
        $chatVideoTable->addColumn(
            'to_user',
            Type::INTEGER,
            ['unsigned' => true, 'notnull' => true]
        );
        $chatVideoTable->addColumn(
            'room_name',
            Type::STRING,
            ['length' => 255, 'notnull' => true]
        );
        $chatVideoTable->addColumn(
            'datetime',
            Type::DATETIME,
            ['notnull' => true]
        );
        $chatVideoTable->setPrimaryKey(['id']);
        $chatVideoTable->addIndex(['from_user'], 'idx_chat_video_from_user');
        $chatVideoTable->addIndex(['to_user'], 'idx_chat_video_to_user');
        $chatVideoTable->addIndex(['from_user', 'to_user'], 'idx_chat_video_users');
        $chatVideoTable->addIndex(['room_name'], 'idx_chat_video_room_name');

        $this->addSql("
            UPDATE settings_current SET selected_value = '1.10.0.38' WHERE variable = 'chamilo_database_version'
        ");
    }

    /**
     * We don't allow downgrades yet
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable('chat_video');
        $this->addSql("
            UPDATE settings_current SET selected_value = '1.10.0.37' WHERE variable = 'chamilo_database_version'
        ");
    }
}
