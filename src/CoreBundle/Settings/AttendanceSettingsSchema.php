<?php
/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Settings;

use Chamilo\CoreBundle\Form\Type\YesNoType;
use Sylius\Bundle\SettingsBundle\Schema\SchemaInterface;
use Sylius\Bundle\SettingsBundle\Schema\SettingsBuilderInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class AttendanceSettingsSchema
 * @package Chamilo\CoreBundle\Settings
 */
class AttendanceSettingsSchema extends AbstractSettingsSchema
{
    /**
     * {@inheritdoc}
     */
    public function buildSettings(SettingsBuilderInterface $builder)
    {
        $builder
            ->setDefaults(
                array(
                    'allow_delete_attendance' => 'true',
                )
            )
//            ->setAllowedTypes(
//                array()
//            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder)
    {
        $builder
            ->add('allow_delete_attendance', YesNoType::class)
        ;
    }
}
