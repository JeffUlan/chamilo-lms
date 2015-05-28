<?php

namespace Chamilo\CoreBundle\Migrations\Schema\V110;

use Chamilo\CoreBundle\Migrations\AbstractMigrationChamilo;
use Doctrine\DBAL\Schema\Schema;

/**
 * Class Version20150527120703
 * LP autolunch -> autolaunch
 * @package Chamilo\CoreBundle\Migrations\Schema\V110
 */
class Version20150527101600 extends AbstractMigrationChamilo
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $value = api_get_configuration_value('gamification_mode');
        $this->addSettingCurrent(
            'gamification_mode',
            '',
            'radio',
            'Platform',
            $value == 0 ?  0: 1,
            'GamificationModeTitle',
            'GamificationModeComment',
            null,
            '',
            1,
            true,
            false,
            [
                [
                    'value' => 1,
                    'text' => 'Yes'
                ],
                [
                    'value' => 0,
                    'text' => 'No'
                ]
            ]
        );
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql("DELETE FROM settings_options WHERE variable = 'gamification_mode'");
        $this->addSql("DELETE FROM settings_current WHERE variable = 'gamification_mode'");
    }

}
