<?php
/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Settings;

use Chamilo\CoreBundle\Form\Type\YesNoType;
use Sylius\Bundle\SettingsBundle\Schema\SchemaInterface;
use Sylius\Bundle\SettingsBundle\Schema\SettingsBuilderInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class SearchSettingsSchema
 * @package Chamilo\CoreBundle\Settings
 */
class SearchSettingsSchema extends AbstractSettingsSchema
{
    /**
     * {@inheritdoc}
     */
    public function buildSettings(SettingsBuilderInterface $builder)
    {
        $builder
            ->setDefaults(
                [
                    'search_enabled' => 'false',
                    'search_prefilter_prefix' => '',
                    'search_show_unlinked_results' => 'true',
                    'number_of_upcoming_events' => '0',
                ]
            );
        $allowedTypes = [
            //'allow_personal_agenda' => array('string'),
            'number_of_upcoming_events' => ['string'],
        ];
        $this->setMultipleAllowedTypes($allowedTypes, $builder);
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder)
    {
        $builder
            ->add('search_enabled', YesNoType::class)
            ->add('search_prefilter_prefix', YesNoType::class)
            ->add(
                'search_show_unlinked_results',
                'choice',
                [
                    'choices' => [
                        'true' =>  'SearchShowUnlinkedResults',
                        'false' =>  'SearchHideUnlinkedResults',
                    ],
                ]
            )
            ->add('number_of_upcoming_events');
    }
}
