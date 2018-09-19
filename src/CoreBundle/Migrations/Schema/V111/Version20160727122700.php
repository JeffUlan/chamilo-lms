<?php
/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Migrations\Schema\V111;

use Chamilo\CoreBundle\Migrations\AbstractMigrationChamilo;
use Doctrine\DBAL\Schema\Schema;

/**
 * Class Version20160727122700
 * Add missing index to c_lp.
 *
 * @package Chamilo\CoreBundle\Migrations\Schema\V111
 */
class Version20160727122700 extends AbstractMigrationChamilo
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $schema
            ->getTable('c_lp')
            ->addIndex(['session_id'], 'session');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema
            ->getTable('c_lp')
            ->dropIndex('session');
    }
}
