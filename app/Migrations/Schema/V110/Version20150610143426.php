<?php
/* For licensing terms, see /license.txt */

namespace Application\Migrations\Schema\V110;

use Chamilo\CoreBundle\Migrations\AbstractMigrationChamilo;
use Doctrine\DBAL\Schema\Schema;

/**
 * Tool changes
 */
class Version20150610143426 extends AbstractMigrationChamilo
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE c_tool ADD description LONGTEXT DEFAULT NULL, ADD custom_icon VARCHAR(255) DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql('ALTER TABLE c_tool DROP description, DROP custom_icon');
    }
}
