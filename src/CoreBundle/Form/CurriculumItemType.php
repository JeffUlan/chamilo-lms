<?php

declare(strict_types=1);

/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CurriculumItemType.
 */
class CurriculumItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        //$builderData = $builder->getData();
        /*$parentIdDisabled = false;
        if (!empty($builderData)) {
            $parentIdDisabled = true;
        }*/

        $builder->add('title', 'textarea');
        $builder->add('score', 'text');
        $builder->add('max_repeat', 'text');

        $builder->add(
            'category',
            'entity',
            [
                'class' => 'Entity\CurriculumCategory',
                'query_builder' => function ($repository) {
                    return $repository->createQueryBuilder('p')
                        ->orderBy('p.title', 'ASC');
                },
                'property' => 'title',
                'required' => false,
            ]
        );
        $builder->add('submit', 'submit');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => 'Entity\CurriculumItem',
            ]
        );
    }

    public function getName(): string
    {
        return 'curriculumItem';
    }
}
