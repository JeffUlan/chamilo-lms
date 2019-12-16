<?php

/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Migrations\Schema\V111;

use Chamilo\CoreBundle\Migrations\AbstractMigrationChamilo;
use Doctrine\DBAL\Schema\Schema;

/**
 * Class Version20160304151300.
 */
class Version20160304151300 extends AbstractMigrationChamilo
{
    /**
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    public function up(Schema $schema)
    {
        $this->addSql("UPDATE extra_field SET visible = 0 WHERE variable IN('mail_notify_invitation', 'mail_notify_message', 'mail_notify_group_message') AND extra_field_type = 1");
    }

    public function down(Schema $schema)
    {
    }
}
