<?php

/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Migrations\Schema\V110;

use Chamilo\CoreBundle\Migrations\AbstractMigrationChamilo;
use Doctrine\DBAL\Schema\Schema;

/**
 * GradebookCategory changes.
 */
class Version20150706135000 extends AbstractMigrationChamilo
{
    public function up(Schema $schema)
    {
        $gradebookCategory = $schema->getTable('gradebook_category');

        $isRequirement = $gradebookCategory->addColumn(
            'is_requirement',
            \Doctrine\DBAL\Types\Type::BOOLEAN
        );
        $isRequirement->setNotnull(true)->setDefault(false);
    }

    public function down(Schema $schema)
    {
        $gradebookCategory = $schema->getTable('gradebook_category');
        $gradebookCategory->dropColumn('is_requirement');
    }
}
