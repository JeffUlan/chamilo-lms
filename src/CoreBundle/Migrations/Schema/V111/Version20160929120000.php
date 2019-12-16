<?php

/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Migrations\Schema\V111;

use Chamilo\CoreBundle\Migrations\AbstractMigrationChamilo;
use Doctrine\DBAL\Schema\Schema;

/**
 * Class Version20160929120000
 * Change tables engine to InnoDB.
 */
class Version20160929120000 extends AbstractMigrationChamilo
{
    /**
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    public function up(Schema $schema)
    {
        error_log('Version20160929120000');
        $this->addSql('ALTER TABLE c_tool ADD INDEX idx_ctool_name (name(20))');
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    public function down(Schema $schema)
    {
        foreach ($this->names as $name) {
            if (!$schema->hasTable($name)) {
                continue;
            }

            $this->addSql('ALTER TABLE c_tool DROP INDEX idx_ctool_name');
        }
    }
}
