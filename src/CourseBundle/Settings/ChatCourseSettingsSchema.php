<?php
/* For licensing terms, see /license.txt */

namespace Chamilo\CourseBundle\Settings;

use Chamilo\CoreBundle\Settings\AbstractSettingsSchema;
use Sylius\Bundle\SettingsBundle\Schema\SettingsBuilderInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ChatCourseSettingsSchema
 * @package Chamilo\CourseBundle\Settings
 */
class ChatCourseSettingsSchema extends AbstractSettingsSchema
{
    /**
     * {@inheritdoc}
     */
    public function buildSettings(SettingsBuilderInterface $builder)
    {
        $builder
            ->setDefaults(array(
                'enabled' => '',
                'allow_open_chat_window' => ''
            ))
        ;
        $allowedTypes = array(
            'enabled' => array('string'),
        );
        $this->setMultipleAllowedTypes($allowedTypes, $builder);
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder)
    {
        $builder
            ->add('enabled', 'yes_no')
            ->add('allow_open_chat_window', 'yes_no')

        ;
    }
}
