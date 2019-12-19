<?php

/* For licensing terms, see /license.txt */

namespace Chamilo\SettingsBundle\Transformer;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * Object to identifier transformer.
 *
 * @author Julio Montoya
 */
class ArrayToIdentifierTransformer implements DataTransformerInterface
{
    public function transform($value)
    {
        if (!is_array($value)) {
            return '';
        }

        return implode(',', $value);
    }

    public function reverseTransform($value)
    {
        if (empty($value)) {
            return [];
        }

        return explode(',', $value);
    }
}
