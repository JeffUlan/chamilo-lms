<?php

declare(strict_types=1);

/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuestionScoreNameType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('name', 'text');
        $builder->add('description', 'textarea');
        $builder->add('score', 'text');
        $builder->add(
            'questionScore',
            'entity',
            [
                'class' => 'Entity\QuestionScore',
                'query_builder' => function ($repository) {
                    return $repository->createQueryBuilder('p')
                        ->orderBy('p.id', 'ASC');
                },
                'property' => 'name',
            ]
        );
        $builder->add('submit', 'submit');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => 'Chamilo\CoreBundle\Entity\QuestionScoreName',
            ]
        );
    }

    public function getName()
    {
        return 'questionScoreName';
    }
}
