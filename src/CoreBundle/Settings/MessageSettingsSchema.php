<?php
/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Settings;

use Chamilo\CoreBundle\Form\Type\YesNoType;
use Sylius\Bundle\SettingsBundle\Schema\SchemaInterface;
use Sylius\Bundle\SettingsBundle\Schema\SettingsBuilderInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class MessageSettingsSchema
 * @package Chamilo\CoreBundle\Settings
 */
class MessageSettingsSchema extends AbstractSettingsSchema
{
    /**
     * {@inheritdoc}
     */
    public function buildSettings(SettingsBuilderInterface $builder)
    {
        $builder
            ->setDefaults(
                array(
                    'allow_message_tool' => 'true',
                    'allow_send_message_to_all_platform_users' => 'false',
                    'message_max_upload_filesize' => '20971520',

                )
            );
        $allowedTypes = array(
            'allow_message_tool' => array('string'),
            'message_max_upload_filesize' => array('string'),
        );
        $this->setMultipleAllowedTypes($allowedTypes, $builder);
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder)
    {
        $builder
            ->add('allow_message_tool', YesNoType::class)
            ->add('allow_send_message_to_all_platform_users', YesNoType::class)
            ->add('message_max_upload_filesize');
    }
}
