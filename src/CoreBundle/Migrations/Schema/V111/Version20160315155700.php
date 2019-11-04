<?php
/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Migrations\Schema\V111;

use Chamilo\CoreBundle\Migrations\AbstractMigrationChamilo;
use Doctrine\DBAL\Schema\Schema;

/**
 * Class Version20160315155700
 * Change type of the course_creation_use_template setting.
 */
class Version20160315155700 extends AbstractMigrationChamilo
{
    /**
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    public function up(Schema $schema)
    {
        $this->addSql("UPDATE settings_current SET type = 'select_course' WHERE variable = 'course_creation_use_template'");
    }

    public function down(Schema $schema)
    {
    }
}
