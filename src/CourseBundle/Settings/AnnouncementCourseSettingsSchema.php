<?php

/* For licensing terms, see /license.txt */

namespace Chamilo\CourseBundle\Settings;

use Chamilo\CoreBundle\Form\Type\YesNoType;
use Chamilo\CoreBundle\Settings\AbstractSettingsSchema;
use Sylius\Bundle\SettingsBundle\Schema\AbstractSettingsBuilder;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class AnnouncementCourseSettingsSchema.
 */
class AnnouncementCourseSettingsSchema extends AbstractSettingsSchema
{
    public function buildSettings(AbstractSettingsBuilder $builder)
    {
        $builder
            ->setDefaults([
                'enabled' => '',
                'allow_user_edit_announcement' => '',
            ])
        ;
        $allowedTypes = [
            'enabled' => ['string'],
            'allow_user_edit_announcement' => ['string'],
        ];
        $this->setMultipleAllowedTypes($allowedTypes, $builder);
    }

    public function buildForm(FormBuilderInterface $builder)
    {
        $builder
            ->add('enabled', YesNoType::class)
            ->add('allow_user_edit_announcement', YesNoType::class)
        ;
    }
}
