<?php
/* For licensing terms, see /license.txt */

namespace ChamiloLMS\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use ChamiloLMS\Entity\CurriculumCategory;

class CurriculumCategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var CurriculumCategory $entity */
        $entity = $builder->getData();

        $builder->add('title', 'text');
        $builder->add('max_score', 'text');
        $builder->add('min_chars', 'text');
        $builder->add('min_chars', 'text');

        $builder->add('c_id', 'hidden');
        $builder->add('session_id', 'hidden');

        $course = $entity->getCourse();
        $session = $entity->getSession();

        $builder->add('parent', 'entity', array(
            'class' => 'Entity\CurriculumCategory',
            'query_builder' => function($repository) use ($entity, $course, $session) {

                $qb = $repository->createQueryBuilder('c')
                    ->where('c.cId = :id')
                    ->orderBy('c.title', 'ASC');
                $parameters = array('id' => $course->getId());

                if (!empty($session)) {
                    $qb ->andWhere('c.sessionId = :session_id');
                    $parameters['session_id'] = $session->getId();
                }
                $qb->setParameters($parameters);
                return $qb;
            },
            'property' => 'title',
            'required' => false
        ));

        $builder->add('submit', 'submit');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'ChamiloLMS\Entity\CurriculumCategory'
            )
        );
    }

    public function getName()
    {
        return 'curriculumCategory';
    }
}
