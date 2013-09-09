<?php

namespace Gedmo\SoftDeleteable\Mapping\Driver;

use Gedmo\Mapping\Driver\Xml as BaseXml,
    Gedmo\Exception\InvalidMappingException,
    Gedmo\SoftDeleteable\Mapping\Validator;

/**
 * This is a xml mapping driver for SoftDeleteable
 * behavioral extension. Used for extraction of extended
 * metadata from xml specificaly for SoftDeleteable
 * extension.
 *
 * @author Gustavo Falco <comfortablynumb84@gmail.com>
 * @author Gediminas Morkevicius <gediminas.morkevicius@gmail.com>
 * @author Miha Vrhovnik <miha.vrhovnik@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class Xml extends BaseXml
{
    /**
     * {@inheritDoc}
     */
    public function readExtendedMetadata($meta, array &$config)
    {
        /**
         * @var \SimpleXmlElement $xml
         */
        $xml = $this->_getMapping($meta->name);
        $xmlDoctrine = $xml;
        $xml = $xml->children(self::GEDMO_NAMESPACE_URI);

        if ($xmlDoctrine->getName() == 'entity' || $xmlDoctrine->getName() == 'mapped-superclass') {
            if (isset($xml->{'soft-deleteable'})) {
                $field = $this->_getAttribute($xml->{'soft-deleteable'}, 'field-name');

                if (!$field) {
                    throw new InvalidMappingException('Field name for SoftDeleteable class is mandatory.');
                }

                Validator::validateField($meta, $field);

                $config['softDeleteable'] = true;
                $config['fieldName'] = $field;

                $config['timeAware'] = false;
                if($this->_isAttributeSet($xml->{'soft-deleteable'}, 'time-aware')) {
                    if (!is_bool($this->_getAttribute($xml->{'soft-deleteable'}, 'time-aware'))) {
                        throw new InvalidMappingException("timeAware must be boolean. ".gettype($this->_getAttribute($xml->{'soft-deleteable'}, 'time-aware'))." provided.");
                    }
                    $config['timeAware'] = $this->_getAttribute($xml->{'soft-deleteable'}, 'time-aware');
                }
            }
        }
    }
}
