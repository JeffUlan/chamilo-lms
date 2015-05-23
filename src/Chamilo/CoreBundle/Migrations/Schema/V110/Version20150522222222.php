<?php
/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Migrations\Schema\V110;

use Chamilo\CoreBundle\Migrations\AbstractMigrationChamilo;
use Doctrine\DBAL\Schema\Schema;


/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150522222222 extends AbstractMigrationChamilo
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE user ADD COLUMN last_login datetime DEFAULT NULL');




        $this->addSql("
            UPDATE settings_current SET selected_value = '1.10.0.40' WHERE variable = 'chamilo_database_version'
        ");

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql('ALTER TABLE user DROP COLUMN last_login');
        $this->addSql("
            UPDATE settings_current SET selected_value = '1.10.0.39' WHERE variable = 'chamilo_database_version'
        ");

    }
}
