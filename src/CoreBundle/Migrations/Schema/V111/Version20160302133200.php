<?php

/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Migrations\Schema\V111;

use Chamilo\CoreBundle\Migrations\AbstractMigrationChamilo;
use Doctrine\DBAL\Schema\Schema;

/**
 * Class Version20160302133200.
 */
class Version20160302133200 extends AbstractMigrationChamilo
{
    /**
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    public function up(Schema $schema)
    {
        $allowSendPushNotification = $this->getConfigurationValue('messaging_allow_send_push_notification');

        $this->addSettingCurrent(
            'messaging_allow_send_push_notification',
            null,
            'radio',
            'WebServices',
            ($allowSendPushNotification ? 'true' : 'false'),
            'MessagingAllowSendPushNotificationTitle',
            'MessagingAllowSendPushNotificationComment',
            null,
            '',
            1,
            true,
            false,
            [
                ['value' => 'true', 'text' => 'Yes'],
                ['value' => 'false', 'text' => 'No'],
            ]
        );

        $gdcProjectNumber = $this->getConfigurationValue('messaging_gdc_project_number');
        $this->addSettingCurrent(
            'messaging_gdc_project_number',
            null,
            'textfield',
            'WebServices',
            !empty($gdcProjectNumber) ? $gdcProjectNumber : '',
            'MessagingGDCProjectNumberTitle',
            'MessagingGDCProjectNumberComment',
            null,
            '',
            1,
            true,
            false
        );

        $gdcApiKey = $this->getConfigurationValue('messaging_gdc_api_key');
        $this->addSettingCurrent(
            'messaging_gdc_api_key',
            null,
            'textfield',
            'WebServices',
            !empty($gdcApiKey) ? $gdcApiKey : '',
            'MessagingGDCApiKeyTitle',
            'MessagingGDCApiKeyComment',
            null,
            '',
            1,
            true,
            false
        );
    }

    public function down(Schema $schema)
    {
    }
}
