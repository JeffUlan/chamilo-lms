<?php

/*
 * This file is part of the FOSRestBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\RestBundle\Routing\Loader\Reader;

use Doctrine\Common\Annotations\Reader;
use Symfony\Component\Routing\Route;
use FOS\RestBundle\Util\Inflector\InflectorInterface;
use FOS\RestBundle\Routing\RestRouteCollection;
use FOS\RestBundle\Request\ParamReader;

/**
 * REST controller actions reader.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class RestActionReader
{
    const COLLECTION_ROUTE_PREFIX = 'c';

    private $annotationReader;
    private $paramReader;
    private $inflector;
    private $formats;
    private $includeFormat;
    private $routePrefix;
    private $namePrefix;
    private $parents = array();
    private $availableHTTPMethods = array('get', 'post', 'put', 'patch', 'delete', 'link', 'unlink', 'head', 'options');
    private $availableConventionalActions = array('new', 'edit', 'remove');

    /**
     * Initializes controller reader.
     *
     * @param Reader             $annotationReader
     * @param ParamReader        $paramReader
     * @param InflectorInterface $inflector
     * @param bool               $includeFormat
     * @param array              $formats
     */
    public function __construct(Reader $annotationReader, ParamReader $paramReader, InflectorInterface $inflector, $includeFormat, array $formats = array())
    {
        $this->annotationReader = $annotationReader;
        $this->paramReader = $paramReader;
        $this->inflector = $inflector;
        $this->includeFormat = $includeFormat;
        $this->formats = $formats;
    }

    /**
     * Sets routes prefix.
     *
     * @param string $prefix Routes prefix
     */
    public function setRoutePrefix($prefix = null)
    {
        $this->routePrefix = $prefix;
    }

    /**
     * Returns route prefix.
     *
     * @return string
     */
    public function getRoutePrefix()
    {
        return $this->routePrefix;
    }

    /**
     * Sets route names prefix.
     *
     * @param string $prefix Route names prefix
     */
    public function setNamePrefix($prefix = null)
    {
        $this->namePrefix = $prefix;
    }

    /**
     * Returns name prefix.
     *
     * @return string
     */
    public function getNamePrefix()
    {
        return $this->namePrefix;
    }

    /**
     * Set parent routes.
     *
     * @param array $parents Array of parent resources names
     */
    public function setParents(array $parents)
    {
        $this->parents = $parents;
    }

    /**
     * Returns parents.
     *
     * @return array
     */
    public function getParents()
    {
        return $this->parents;
    }

    /**
     * Reads action route.
     *
     * @param RestRouteCollection $collection
     * @param \ReflectionMethod   $method
     * @param array               $resource
     *
     * @return Route
     *
     * @throws \InvalidArgumentException
     */
    public function read(RestRouteCollection $collection, \ReflectionMethod $method, $resource)
    {
        // check that every route parent has non-empty singular name
        foreach ($this->parents as $parent) {
            if (empty($parent) || '/' === substr($parent, -1)) {
                throw new \InvalidArgumentException(
                    "Every parent controller must have `get{SINGULAR}Action(\$id)` method\n".
                    "where {SINGULAR} is a singular form of associated object"
                );
            }
        }

        // if method is not readable - skip
        if (!$this->isMethodReadable($method)) {
            return;
        }

        // if we can't get http-method and resources from method name - skip
        $httpMethodAndResources = $this->getHttpMethodAndResourcesFromMethod($method, $resource);
        if (!$httpMethodAndResources) {
            return;
        }

        list($httpMethod, $resources, $isCollection, $isInflectable) = $httpMethodAndResources;
        $arguments                                                   = $this->getMethodArguments($method);

        // if we have only 1 resource & 1 argument passed, then it's object call, so
        // we can set collection singular name
        if (1 === count($resources) && 1 === count($arguments) - count($this->parents)) {
            $collection->setSingularName($resources[0]);
        }

        // if we have parents passed - merge them with own resource names
        if (count($this->parents)) {
            $resources = array_merge($this->parents, $resources);
        }

        if (empty($resources)) {
            $resources[] = null;
        }

        $routeName = $httpMethod.$this->generateRouteName($resources);
        $urlParts  = $this->generateUrlParts($resources, $arguments, $httpMethod);

        // if passed method is not valid HTTP method then it's either
        // a hypertext driver, a custom object (PUT) or collection (GET)
        // method
        if (!in_array($httpMethod, $this->availableHTTPMethods)) {
            $urlParts[] = $httpMethod;
            $httpMethod = $this->getCustomHttpMethod($httpMethod, $resources, $arguments);
        }

        // generated parameters
        $routeName    = $this->namePrefix.strtolower($routeName);
        $pattern      = implode('/', $urlParts);
        $defaults     = array('_controller' => $method->getName());
        $requirements = array('_method' => strtoupper($httpMethod));
        $options      = array();
        $host         = '';
        $schemes      = array();
        $condition    = null;



        $annotations = $this->readRouteAnnotation($method);
        if ($annotations) {
            foreach ($annotations as $annotation) {

                $pattern      = implode('/', $urlParts);
                $defaults     = array('_controller' => $method->getName());
                $requirements = array('_method' => strtoupper($httpMethod));
                $options      = array();
                $host         = '';
                $schemes      = array();
                $condition    = null;

                $annoRequirements = $annotation->getRequirements();

                if (!isset($annoRequirements['_method'])) {
                    $annoRequirements['_method'] = $requirements['_method'];
                }

                $pattern      = $annotation->getPattern() !== null ? $this->routePrefix . $annotation->getPattern() : $pattern;
                $requirements = array_merge($requirements, $annoRequirements);
                $options      = array_merge($options, $annotation->getOptions());
                $defaults     = array_merge($defaults, $annotation->getDefaults());
                //TODO remove checks after Symfony requirement is bumped to 2.2
                if (method_exists($annotation, 'getHost')) {
                    $host = $annotation->getHost();
                }
                if (method_exists($annotation, 'getSchemes')) {
                    $schemes = $annotation->getSchemes();
                }
               //TODO remove checks after Symfony requirement is bumped to 2.4
                if (method_exists($annotation, 'getCondition')) {
                    $condition = $annotation->getCondition();
                }

                if ($this->includeFormat === true) {
                    $pattern .= '.{_format}';

                    if (!isset($requirements['_format']) && !empty($this->formats)) {
                        $requirements['_format'] = implode('|', array_keys($this->formats));
                    }
                }
                // add route to collection
                $route = new Route(
                    $pattern, $defaults, $requirements, $options, $host, $schemes, null, $condition
                );
                $this->addRoute($collection, $routeName, $route, $isCollection, $isInflectable, $annotation);
            }

        } else {
            if ($this->includeFormat === true) {
                $pattern .= '.{_format}';

                if (!isset($requirements['_format']) && !empty($this->formats)) {
                    $requirements['_format'] = implode('|', array_keys($this->formats));
                }
            }
            // add route to collection
            $route = new Route(
                $pattern, $defaults, $requirements, $options, $host, $schemes, null, $condition
            );
            $this->addRoute($collection, $routeName, $route, $isCollection, $isInflectable);
        }
    }

    /**
     * Checks whether provided method is readable.
     *
     * @param \ReflectionMethod $method
     *
     * @return bool
     */
    private function isMethodReadable(\ReflectionMethod $method)
    {
        // if method starts with _ - skip
        if ('_' === substr($method->getName(), 0, 1)) {
            return false;
        }

        $hasNoRouteMethod = (bool) $this->readMethodAnnotation($method, 'NoRoute');
        $hasNoRouteClass = (bool) $this->readClassAnnotation($method->getDeclaringClass(), 'NoRoute');

        $hasNoRoute = $hasNoRoute = $hasNoRouteMethod || $hasNoRouteClass;
        // since NoRoute extends Route we need to exclude all the method NoRoute annotations
        $hasRoute = (bool) $this->readMethodAnnotation($method, 'Route') && !$hasNoRouteMethod;

        // if method has NoRoute annotation and does not have Route annotation - skip
        if ($hasNoRoute && !$hasRoute) {
            return false;
        }

        return true;
    }

    /**
     * Returns HTTP method and resources list from method signature.
     *
     * @param \ReflectionMethod $method
     * @param array             $resource
     *
     * @return bool|array
     */
    private function getHttpMethodAndResourcesFromMethod(\ReflectionMethod $method, $resource)
    {
        // if method doesn't match regex - skip
        if (!preg_match('/([a-z][_a-z0-9]+)(.*)Action/', $method->getName(), $matches)) {
            return false;
        }

        $httpMethod = strtolower($matches[1]);
        $resources  = preg_split(
            '/([A-Z][^A-Z]*)/', $matches[2], -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE
        );
        $isCollection = false;
        $isInflectable = true;

        if (0 === strpos($httpMethod, self::COLLECTION_ROUTE_PREFIX)
            && in_array(substr($httpMethod, 1), $this->availableHTTPMethods)
        ) {
            $isCollection = true;
            $httpMethod = substr($httpMethod, 1);
            if (!empty($resource)) {
                $resourcePluralized = $this->inflector->pluralize(end($resource));
                $isInflectable = ($resourcePluralized != $resource[count($resource) - 1]);
                $resource[count($resource)-1] = $resourcePluralized;
            }
        }

        $resources = array_merge($resource, $resources);

        return array($httpMethod, $resources, $isCollection, $isInflectable);
    }

    /**
     * Returns readable arguments from method.
     *
     * @param \ReflectionMethod $method
     *
     * @return array
     */
    private function getMethodArguments(\ReflectionMethod $method)
    {
        // ignore all query params
        $params = $this->paramReader->getParamsFromMethod($method);

        // ignore type hinted arguments that are or extend from:
        // * Symfony\Component\HttpFoundation\Request
        // * FOS\RestBundle\Request\QueryFetcher
        // * Symfony\Component\Validator\ConstraintViolationList
        $ignoreClasses = array(
            'Symfony\Component\HttpFoundation\Request',
            'FOS\RestBundle\Request\ParamFetcherInterface',
            'Symfony\Component\Validator\ConstraintViolationListInterface',
        );

        $arguments = array();
        foreach ($method->getParameters() as $argument) {
            if (isset($params[$argument->getName()])) {
                continue;
            }

            $argumentClass = $argument->getClass();
            if ($argumentClass) {
                foreach ($ignoreClasses as $class) {
                    if ($argumentClass->getName() === $class || $argumentClass->isSubclassOf($class)) {
                        continue 2;
                    }
                }
            }

            $arguments[] = $argument;
        }

        return $arguments;
    }

    /**
     * Generates route name from resources list.
     *
     * @param array $resources
     *
     * @return string
     */
    private function generateRouteName(array $resources)
    {
        $routeName = '';
        foreach ($resources as $resource) {
            if (null !== $resource) {
                $routeName .= '_' . basename($resource);
            }
        }

        return $routeName;
    }

    /**
     * Generates URL parts for route from resources list.
     *
     * @param array  $resources
     * @param array  $arguments
     * @param string $httpMethod
     *
     * @return array
     */
    private function generateUrlParts(array $resources, array $arguments, $httpMethod)
    {
        $urlParts = array();
        foreach ($resources as $i => $resource) {
            // if we already added all parent routes paths to URL & we have
            // prefix - add it
            if (!empty($this->routePrefix) && $i === count($this->parents)) {
                $urlParts[] = $this->routePrefix;
            }

            // if we have argument for current resource, then it's object.
            // otherwise - it's collection
            if (isset($arguments[$i])) {
                if (null !== $resource) {
                    $urlParts[] =
                        strtolower($this->inflector->pluralize($resource))
                        .'/{'.$arguments[$i]->getName().'}';
                } else {
                    $urlParts[] = '{'.$arguments[$i]->getName().'}';
                }
            } elseif (null !== $resource) {
                if ((0 === count($arguments) && !in_array($httpMethod, $this->availableHTTPMethods))
                    || 'new' === $httpMethod
                    || 'post' === $httpMethod
                ) {
                    $urlParts[] = $this->inflector->pluralize(strtolower($resource));
                } else {
                    $urlParts[] = strtolower($resource);
                }
            }
        }

        return $urlParts;
    }

    /**
     * Returns custom HTTP method for provided list of resources, arguments, method.
     *
     * @param string $httpMethod current HTTP method
     * @param array  $resources  resources list
     * @param array  $arguments  list of method arguments
     *
     * @return string
     */
    private function getCustomHttpMethod($httpMethod, array $resources, array $arguments)
    {
        if (in_array($httpMethod, $this->availableConventionalActions)) {
            // allow hypertext as the engine of application state
            // through conventional GET actions
            return 'get';
        }

        if (count($arguments) < count($resources)) {
            // resource collection
            return 'get';
        }

        //custom object
        return 'patch';
    }

    /**
     * Returns first route annotation for method.
     *
     * @param \ReflectionMethod $reflectionMethod
     *
     * @return array|null
     */
    private function readRouteAnnotation(\ReflectionMethod $reflectionMethod)
    {
        $annotations = array();

        foreach ( array('Route', 'Get', 'Post', 'Put', 'Patch', 'Delete', 'Link', 'Unlink', 'Head', 'Options') as $annotationName) {
            if ($annotations_new = $this->readMethodAnnotations($reflectionMethod, $annotationName)) {
                $annotations = array_merge($annotations, $annotations_new);
            }
        }
        return $annotations;
    }

    /**
     * Reads class annotations.
     *
     * @param \ReflectionClass $reflectionClass
     * @param string           $annotationName
     *
     * @return object|null
     */
    private function readClassAnnotation(\ReflectionClass $reflectionClass, $annotationName)
    {
        $annotationClass = "FOS\\RestBundle\\Controller\\Annotations\\$annotationName";

        if ($annotation = $this->annotationReader->getClassAnnotation($reflectionClass, $annotationClass)) {
            return $annotation;
        }
    }

    /**
     * Reads method annotations.
     *
     * @param \ReflectionMethod $reflectionMethod
     * @param string            $annotationName
     *
     * @return object|null
     */
    private function readMethodAnnotation(\ReflectionMethod $reflectionMethod, $annotationName)
    {
        $annotationClass = "FOS\\RestBundle\\Controller\\Annotations\\$annotationName";

        if ($annotation = $this->annotationReader->getMethodAnnotation($reflectionMethod, $annotationClass)) {
            return $annotation;
        }
    }

    /**
     * Reads method annotations.
     *
     * @param \ReflectionMethod $reflectionMethod
     * @param string            $annotationName
     *
     * @return array|null
     */
    private function readMethodAnnotations(\ReflectionMethod $reflectionMethod, $annotationName)
    {
        $annotations = array();
        $annotationClass = "FOS\\RestBundle\\Controller\\Annotations\\$annotationName";

        if ($annotations_new = $this->annotationReader->getMethodAnnotations($reflectionMethod)) {

            foreach($annotations_new as $annotation) {
                if ($annotation instanceof $annotationClass) {
                    $annotations[]= $annotation;
                }
            }
        }

        return $annotations;
    }

    /**
     * @param RestRouteCollection $collection
     * @param string              $routeName
     * @param Route               $route
     * @param bool                $isCollection
     * @param bool                $isInflectable
     * @param object              $annotation
     */
    private function addRoute(RestRouteCollection $collection, $routeName, $route, $isCollection, $isInflectable, $annotation = null)
    {
        if ($annotation) {
            $routeName = $routeName.$annotation->getName();
        }

        if ($isCollection && !$isInflectable) {
            $collection->add(self::COLLECTION_ROUTE_PREFIX.$routeName, $route);
            if (!$collection->get($routeName)) {
                $collection->add($routeName, clone $route);
            }
        } else {
            $collection->add($routeName, $route);
        }
    }
}
