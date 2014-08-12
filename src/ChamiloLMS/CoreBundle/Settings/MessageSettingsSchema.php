<?php

namespace ChamiloLMS\CoreBundle\Settings;

use Sylius\Bundle\SettingsBundle\Schema\SchemaInterface;
use Sylius\Bundle\SettingsBundle\Schema\SettingsBuilderInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class MessageSettingsSchema
 * @package ChamiloLMS\CoreBundle\Settings
 */
class MessageSettingsSchema implements SchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildSettings(SettingsBuilderInterface $builder)
    {
        $builder
            ->setDefaults(array(
                'allow_message_tool' => '',
                'allow_send_message_to_all_platform_users' => '',
                'message_max_upload_filesize' => '',

            ))
            ->setAllowedTypes(array(
                'allow_message_tool' => array('string'),
                'message_max_upload_filesize' => array('integer')
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder)
    {
        $builder
            ->add('allow_message_tool', 'yes_no')
            ->add('allow_send_message_to_all_platform_users', 'yes_no')
            ->add('message_max_upload_filesize')
        ;
    }
}
