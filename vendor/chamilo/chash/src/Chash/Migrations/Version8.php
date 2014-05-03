<?php

namespace Chash\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Manages the migration to version 1.8.0
 * @package ChamiloLMS\Controller\Migrations
 */
class Version8 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $sql = 'UPDATE settings_current SET selected_value = "1.8.8.14911"
                WHERE variable = "chamilo_database_version"';
        $this->addSql($sql);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $sql = 'UPDATE settings_current SET selected_value = "1.8.7"
                WHERE variable = "chamilo_database_version"';
        $this->addSql($sql);
    }
}
