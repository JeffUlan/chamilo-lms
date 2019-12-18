<?php

/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Admin;

use Chamilo\CoreBundle\Entity\Promotion;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

/**
 * Class PromotionAdmin.
 */
class PromotionAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name')
            ->add('description', 'ckeditor')
            //->add('status', 'choice', ['choices' => Promotion::getStatusList()])
            ->add('career')
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->addIdentifier('name')
        ;
    }
}
