<?php

namespace ChamiloLMS\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Entity;

class QuestionScoreNameType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text');
        $builder->add('description', 'text');
        $builder->add('score', 'text');
        $builder->add('questionScore', 'entity', array(
            'class' => 'Entity\QuestionScore',
            'query_builder' => function($repository) {
                return $repository->createQueryBuilder('p')
                    ->orderBy('p.id', 'ASC');
            },
            'property' => 'name',
        ));
        $builder->add('submit', 'submit');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Entity\QuestionScoreName'
            )
        );
    }

    public function getName()
    {
        return 'questionScoreName';
    }
}
