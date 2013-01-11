<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Routing\Loader;

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Config\Loader\FileLoader;
use Symfony\Component\Config\Util\XmlUtils;

/**
 * XmlFileLoader loads XML routing files.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Tobias Schultze <http://tobion.de>
 *
 * @api
 */
class XmlFileLoader extends FileLoader
{
    const NAMESPACE_URI = 'http://symfony.com/schema/routing';
    const SCHEME_PATH = '/schema/routing/routing-1.0.xsd';

    /**
     * Loads an XML file.
     *
     * @param string      $file An XML file path
     * @param string|null $type The resource type
     *
     * @return RouteCollection A RouteCollection instance
     *
     * @throws \InvalidArgumentException When the file cannot be loaded or when the XML cannot be
     *                                   parsed because it does not validate against the scheme.
     *
     * @api
     */
    public function load($file, $type = null)
    {
        $path = $this->locator->locate($file);

        $xml = $this->loadFile($path);

        $collection = new RouteCollection();
        $collection->addResource(new FileResource($path));

        // process routes and imports
        foreach ($xml->documentElement->childNodes as $node) {
            if (!$node instanceof \DOMElement) {
                continue;
            }

            $this->parseNode($collection, $node, $path, $file);
        }

        return $collection;
    }

    /**
     * Parses a node from a loaded XML file.
     *
     * @param RouteCollection $collection Collection to associate with the node
     * @param \DOMElement     $node       Element to parse
     * @param string          $path       Full path of the XML file being processed
     * @param string          $file       Loaded file name
     *
     * @throws \InvalidArgumentException When the XML is invalid
     */
    protected function parseNode(RouteCollection $collection, \DOMElement $node, $path, $file)
    {
        if (self::NAMESPACE_URI !== $node->namespaceURI) {
            return;
        }

        switch ($node->localName) {
            case 'route':
                $this->parseRoute($collection, $node, $path);
                break;
            case 'import':
                $this->parseImport($collection, $node, $path, $file);
                break;
            default:
                throw new \InvalidArgumentException(sprintf('Unknown tag "%s" used in file "%s". Expected "route" or "import".', $node->localName, $path));
        }
    }

    /**
     * {@inheritdoc}
     *
     * @api
     */
    public function supports($resource, $type = null)
    {
        return is_string($resource) && 'xml' === pathinfo($resource, PATHINFO_EXTENSION) && (!$type || 'xml' === $type);
    }

    /**
     * Parses a route and adds it to the RouteCollection.
     *
     * @param RouteCollection $collection RouteCollection instance
     * @param \DOMElement     $node       Element to parse that represents a Route
     * @param string          $path       Full path of the XML file being processed
     *
     * @throws \InvalidArgumentException When the XML is invalid
     */
    protected function parseRoute(RouteCollection $collection, \DOMElement $node, $path)
    {
        if ('' === ($id = $node->getAttribute('id')) || !$node->hasAttribute('pattern')) {
            throw new \InvalidArgumentException(sprintf('The <route> element in file "%s" must have an "id" and a "pattern" attribute.', $path));
        }

        list($defaults, $requirements, $options) = $this->parseConfigs($node, $path);

        $route = new Route($node->getAttribute('pattern'), $defaults, $requirements, $options, $node->getAttribute('hostname-pattern'));
        $collection->add($id, $route);
    }

    /**
     * Parses an import and adds the routes in the resource to the RouteCollection.
     *
     * @param RouteCollection $collection RouteCollection instance
     * @param \DOMElement     $node       Element to parse that represents a Route
     * @param string          $path       Full path of the XML file being processed
     * @param string          $file       Loaded file name
     *
     * @throws \InvalidArgumentException When the XML is invalid
     */
    protected function parseImport(RouteCollection $collection, \DOMElement $node, $path, $file)
    {
        if ('' === $resource = $node->getAttribute('resource')) {
            throw new \InvalidArgumentException(sprintf('The <import> element in file "%s" must have a "resource" attribute.', $path));
        }

        $type = $node->getAttribute('type');
        $prefix = $node->getAttribute('prefix');
        $hostnamePattern = $node->hasAttribute('hostname-pattern') ? $node->getAttribute('hostname-pattern') : null;

        list($defaults, $requirements, $options) = $this->parseConfigs($node, $path);

        $this->setCurrentDir(dirname($path));

        $subCollection = $this->import($resource, ('' !== $type ? $type : null), false, $file);
        /* @var $subCollection RouteCollection */
        $subCollection->addPrefix($prefix);
        if (null !== $hostnamePattern) {
            $subCollection->setHostnamePattern($hostnamePattern);
        }
        $subCollection->addDefaults($defaults);
        $subCollection->addRequirements($requirements);
        $subCollection->addOptions($options);

        $collection->addCollection($subCollection);
    }

    /**
     * Loads an XML file.
     *
     * @param string $file An XML file path
     *
     * @return \DOMDocument
     *
     * @throws \InvalidArgumentException When loading of XML file fails because of syntax errors
     *                                   or when the XML structure is not as expected by the scheme -
     *                                   see validate()
     */
    protected function loadFile($file)
    {
        return XmlUtils::loadFile($file, __DIR__ . static::SCHEME_PATH);
    }

    /**
     * Parses the config elements (default, requirement, option).
     *
     * @param \DOMElement $node Element to parse that contains the configs
     * @param string      $path Full path of the XML file being processed
     *
     * @return array An array with the defaults as first item, requirements as second and options as third.
     *
     * @throws \InvalidArgumentException When the XML is invalid
     */
    private function parseConfigs(\DOMElement $node, $path)
    {
        $defaults = array();
        $requirements = array();
        $options = array();

        foreach ($node->getElementsByTagNameNS(self::NAMESPACE_URI, '*') as $n) {
            switch ($n->localName) {
                case 'default':
                    $defaults[$n->getAttribute('key')] = trim($n->textContent);
                    break;
                case 'requirement':
                    $requirements[$n->getAttribute('key')] = trim($n->textContent);
                    break;
                case 'option':
                    $options[$n->getAttribute('key')] = trim($n->textContent);
                    break;
                default:
                    throw new \InvalidArgumentException(sprintf('Unknown tag "%s" used in file "%s". Expected "default", "requirement" or "option".', $n->localName, $path));
            }
        }

        return array($defaults, $requirements, $options);
    }
}
