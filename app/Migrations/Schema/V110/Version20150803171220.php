<?php
/* For licensing terms, see /license.txt */

namespace Application\Migrations\Schema\V110;

use Application\Migrations\AbstractMigrationChamilo;
use Doctrine\DBAL\Schema\Schema;

/**
 * Class Version20150803171220
 *
 * @package Application\Migrations\Schema\V110
 */
class Version20150803171220 extends AbstractMigrationChamilo
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('UPDATE user SET username_canonical = username');
        $this->addSql('ALTER TABLE user ADD confirmation_token VARCHAR(255) NULL');
        $this->addSql('ALTER TABLE user ADD password_requested_at DATETIME DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql('ALTER TABLE user DROP confirmation_token');
        $this->addSql('ALTER TABLE user DROP password_requested_at');
    }
}
