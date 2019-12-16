<?php

/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Migrations\Schema\V111;

use Chamilo\CoreBundle\Migrations\AbstractMigrationChamilo;
use Doctrine\DBAL\Schema\Schema;

/**
 * Class Version20160610142700
 * Integrate the Skype plugin and create new settings current to enable it.
 */
class Version20160610142700 extends AbstractMigrationChamilo
{
    /**
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    public function up(Schema $schema)
    {
        $connection = $this->connection;
        $sql = "SELECT id FROM extra_field WHERE variable = 'skype' AND extra_field_type = 1";
        $result = $connection->executeQuery($sql)->fetchAll();

        if (empty($result)) {
            $this->addSql("
                INSERT INTO extra_field (extra_field_type, field_type, variable, display_text, visible, changeable, created_at)
                VALUES (1, 1, 'skype', 'Skype', 1, 1, NOW())
            ");
        }

        $sql = "SELECT id FROM extra_field WHERE variable = 'skype' AND extra_field_type = 1";
        $result = $connection->executeQuery($sql)->fetchAll();
        if (empty($result)) {
            $this->addSql("
            INSERT INTO extra_field (extra_field_type, field_type, variable, display_text, visible, changeable, created_at)
            VALUES (1, 1, 'linkedin_url', 'LinkedInUrl', 1, 1, NOW())"
            );
        }

        $this->addSettingCurrent(
            'allow_show_skype_account',
            null,
            'radio',
            'Platform',
            'true',
            'AllowShowSkypeAccountTitle',
            'AllowShowSkypeAccountComment',
            null,
            null,
            1,
            true,
            false,
            [
                ['value' => 'false', 'text' => 'No'],
                ['value' => 'true', 'text' => 'Yes'],
            ]
        );

        $this->addSettingCurrent(
            'allow_show_linkedin_url',
            null,
            'radio',
            'Platform',
            'true',
            'AllowShowLinkedInUrlTitle',
            'AllowShowLinkedInUrlComment',
            null,
            null,
            1,
            true,
            false,
            [
                ['value' => 'false', 'text' => 'No'],
                ['value' => 'true', 'text' => 'Yes'],
            ]
        );
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    public function down(Schema $schema)
    {
    }
}
