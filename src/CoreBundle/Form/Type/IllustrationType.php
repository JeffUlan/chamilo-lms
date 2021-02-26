<?php

declare(strict_types=1);

/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IllustrationType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        /*$resolver->setDefaults(
            [
                'choices' => [
                    'Yes' => 'true',
                    'No' => 'false',
                ],
            ]
        );*/
    }

    public function getParent()
    {
        return FileType::class;
    }

    public function getName(): string
    {
        return 'illustration';
    }
}
