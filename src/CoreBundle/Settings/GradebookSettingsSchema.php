<?php

declare(strict_types=1);

/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Settings;

use Chamilo\CoreBundle\Form\Type\YesNoType;
use Sylius\Bundle\SettingsBundle\Schema\AbstractSettingsBuilder;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class GradebookSettingsSchema extends AbstractSettingsSchema
{
    public function buildSettings(AbstractSettingsBuilder $builder): void
    {
        $builder
            ->setDefaults(
                [
                    'gradebook_enable' => 'true',
                    'gradebook_score_display_custom' => 'false',
                    'gradebook_score_display_colorsplit' => '50',
                    'gradebook_score_display_upperlimit' => 'false',
                    'gradebook_number_decimals' => '0',
                    'teachers_can_change_score_settings' => 'true',
                    'teachers_can_change_grade_model_settings' => 'true',
                    'gradebook_enable_grade_model' => 'false',
                    'gradebook_default_weight' => '100',
                    'gradebook_locking_enabled' => 'false',
                    'gradebook_default_grade_model_id' => '',
                    'gradebook_show_percentage_in_reports' => '',
                    'my_display_coloring' => 'false',
                    'student_publication_to_take_in_gradebook' => 'first',
                    'gradebook_detailed_admin_view' => 'false',
                    'openbadges_backpack' => 'https://backpack.openbadges.org/',
                    'hide_certificate_export_link' => 'false',
                    'add_gradebook_certificates_cron_task_enabled' => 'false',
                    'certificate_filter_by_official_code' => 'false',
                    'hide_certificate_export_link_students' => 'false',
                    'gradebook_enable_best_score' => 'false',
                    'gradebook_hide_graph' => 'false',
                    'gradebook_hide_pdf_report_button' => 'false',
                    'hide_gradebook_percentage_user_result' => 'true',
                    'gradebook_use_exercise_score_settings_in_categories' => 'true',
                    'gradebook_use_apcu_cache' => 'true',
                    'gradebook_report_score_style' => '1',
                    'gradebook_score_display_custom_standalone' => 'false',
                    'gradebook_use_exercise_score_settings_in_total' => 'false',
                ]
            )
        ;
        $allowedTypes = [
            'gradebook_enable' => ['string'],
            'gradebook_number_decimals' => ['string'],
            'gradebook_default_weight' => ['string'],
            'student_publication_to_take_in_gradebook' => ['string'],
            'gradebook_detailed_admin_view' => ['string'],
            'certificate_filter_by_official_code' => ['string'],
        ];
        $this->setMultipleAllowedTypes($allowedTypes, $builder);
    }

    public function buildForm(FormBuilderInterface $builder): void
    {
        $builder
            ->add('gradebook_enable', YesNoType::class)
            ->add('gradebook_score_display_custom', YesNoType::class)
            ->add('gradebook_score_display_colorsplit')
            ->add('gradebook_score_display_upperlimit', YesNoType::class)
            ->add('gradebook_number_decimals')
            ->add('teachers_can_change_score_settings', YesNoType::class)
            ->add('gradebook_enable_grade_model', YesNoType::class)
            ->add('teachers_can_change_grade_model_settings', YesNoType::class)
            ->add('gradebook_default_weight')
            ->add('gradebook_locking_enabled', YesNoType::class)
            ->add('gradebook_default_grade_model_id')
            ->add('gradebook_show_percentage_in_reports')
            ->add('my_display_coloring', YesNoType::class)
            ->add(
                'student_publication_to_take_in_gradebook',
                ChoiceType::class,
                [
                    'choices' => [
                        'First' => 'first',
                        'Last' => 'last',
                    ],
                ]
            )
            ->add('gradebook_detailed_admin_view', YesNoType::class)
            ->add('openbadges_backpack')
            ->add('hide_certificate_export_link', YesNoType::class)
            ->add('add_gradebook_certificates_cron_task_enabled', YesNoType::class)
            ->add('certificate_filter_by_official_code', YesNoType::class)
            ->add('hide_certificate_export_link_students', YesNoType::class)
            ->add('gradebook_enable_best_score', YesNoType::class)
            ->add('gradebook_hide_graph', YesNoType::class)
            ->add('gradebook_hide_pdf_report_button', YesNoType::class)
            ->add('hide_gradebook_percentage_user_result', YesNoType::class)
            ->add('gradebook_use_exercise_score_settings_in_categories', YesNoType::class)
            ->add('gradebook_use_apcu_cache', YesNoType::class)
            ->add(
                'gradebook_report_score_style',
                TextType::class,
                [
                    'label' => 'GradebookReportScoreStyleTitle',
                    'help' => 'GradebookReportScoreStyleComment',
                ]
            )
            ->add('gradebook_score_display_custom_standalone', YesNoType::class)
            ->add('gradebook_use_exercise_score_settings_in_total', YesNoType::class)
        ;
    }
}
