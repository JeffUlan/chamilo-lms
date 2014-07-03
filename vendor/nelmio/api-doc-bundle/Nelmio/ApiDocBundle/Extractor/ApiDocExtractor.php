<?php

/*
 * This file is part of the NelmioApiDocBundle.
 *
 * (c) Nelmio <hello@nelm.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nelmio\ApiDocBundle\Extractor;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Util\ClassUtils;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Nelmio\ApiDocBundle\Parser\ParserInterface;
use Nelmio\ApiDocBundle\Parser\PostParserInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Nelmio\ApiDocBundle\Util\DocCommentExtractor;

class ApiDocExtractor
{
    const ANNOTATION_CLASS = 'Nelmio\\ApiDocBundle\\Annotation\\ApiDoc';

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var Reader
     */
    protected $reader;

    /**
     * @var DocCommentExtractor
     */
    private $commentExtractor;

    /**
     * @var ParserInterface[]
     */
    protected $parsers = array();

    /**
     * @var HandlerInterface[]
     */
    protected $handlers;

    public function __construct(ContainerInterface $container, RouterInterface $router, Reader $reader, DocCommentExtractor $commentExtractor, array $handlers)
    {
        $this->container        = $container;
        $this->router           = $router;
        $this->reader           = $reader;
        $this->commentExtractor = $commentExtractor;
        $this->handlers         = $handlers;
    }

    /**
     * Return a list of route to inspect for ApiDoc annotation
     * You can extend this method if you don't want all the routes
     * to be included.
     *
     * @return Route[] An array of routes
     */
    public function getRoutes()
    {
        return $this->router->getRouteCollection()->all();
    }

    /**
     * Extracts annotations from all known routes
     *
     * @return array
     */
    public function all()
    {
        return $this->extractAnnotations($this->getRoutes());
    }

    /**
     * Returns an array of data where each data is an array with the following keys:
     *  - annotation
     *  - resource
     *
     * @param array $routes array of Route-objects for which the annotations should be extracted
     *
     * @return array
     */
    public function extractAnnotations(array $routes)
    {
        $array     = array();
        $resources = array();
        $excludeSections = $this->container->getParameter('nelmio_api_doc.exclude_sections');

        foreach ($routes as $route) {
            if (!$route instanceof Route) {
                throw new \InvalidArgumentException(sprintf('All elements of $routes must be instances of Route. "%s" given', gettype($route)));
            }

            if ($method = $this->getReflectionMethod($route->getDefault('_controller'))) {
                $annotation = $this->reader->getMethodAnnotation($method, self::ANNOTATION_CLASS);
                if ($annotation && !in_array($annotation->getSection(), $excludeSections)) {
                    if ($annotation->isResource()) {
                        if ($resource = $annotation->getResource()) {
                            $resources[] = $resource;
                        } else {
                            // remove format from routes used for resource grouping
                            $resources[] = str_replace('.{_format}', '', $route->getPattern());
                        }
                    }

                    $array[] = array('annotation' => $this->extractData($annotation, $route, $method));
                }
            }
        }

        rsort($resources);
        foreach ($array as $index => $element) {
            $hasResource = false;
            $pattern     = $element['annotation']->getRoute()->getPattern();

            foreach ($resources as $resource) {
                if (0 === strpos($pattern, $resource) || $resource === $element['annotation']->getResource()) {
                    $array[$index]['resource'] = $resource;

                    $hasResource = true;
                    break;
                }
            }

            if (false === $hasResource) {
                $array[$index]['resource'] = 'others';
            }
        }

        $methodOrder = array('GET', 'POST', 'PUT', 'DELETE');
        usort($array, function ($a, $b) use ($methodOrder) {
            if ($a['resource'] === $b['resource']) {
                if ($a['annotation']->getRoute()->getPattern() === $b['annotation']->getRoute()->getPattern()) {
                    $methodA = array_search($a['annotation']->getRoute()->getRequirement('_method'), $methodOrder);
                    $methodB = array_search($b['annotation']->getRoute()->getRequirement('_method'), $methodOrder);

                    if ($methodA === $methodB) {
                        return strcmp(
                            $a['annotation']->getRoute()->getRequirement('_method'),
                            $b['annotation']->getRoute()->getRequirement('_method')
                        );
                    }

                    return $methodA > $methodB ? 1 : -1;
                }

                return strcmp(
                    $a['annotation']->getRoute()->getPattern(),
                    $b['annotation']->getRoute()->getPattern()
                );
            }

            return strcmp($a['resource'], $b['resource']);
        });

        return $array;
    }

    /**
     * Returns the ReflectionMethod for the given controller string.
     *
     * @param string $controller
     * @return \ReflectionMethod|null
     */
    public function getReflectionMethod($controller)
    {
        if (preg_match('#(.+)::([\w]+)#', $controller, $matches)) {
            $class = $matches[1];
            $method = $matches[2];
        } elseif (preg_match('#(.+):([\w]+)#', $controller, $matches)) {
            $controller = $matches[1];
            $method = $matches[2];
            if ($this->container->has($controller)) {
                $this->container->enterScope('request');
                $this->container->set('request', new Request(), 'request');
                $class = ClassUtils::getRealClass(get_class($this->container->get($controller)));
                $this->container->leaveScope('request');
            }
        }

        if (isset($class) && isset($method)) {
            try {
                return new \ReflectionMethod($class, $method);
            } catch (\ReflectionException $e) {
            }
        }

        return null;
    }

    /**
     * Returns an ApiDoc annotation.
     *
     * @param string $controller
     * @param string $route
     * @return ApiDoc|null
     */
    public function get($controller, $route)
    {
        if ($method = $this->getReflectionMethod($controller)) {
            if ($annotation = $this->reader->getMethodAnnotation($method, self::ANNOTATION_CLASS)) {
                if ($route = $this->router->getRouteCollection()->get($route)) {
                    return $this->extractData($annotation, $route, $method);
                }
            }
        }

        return null;
    }

    /**
     * Registers a class parser to use for parsing input class metadata
     *
     * @param ParserInterface $parser
     */
    public function addParser(ParserInterface $parser)
    {
        $this->parsers[] = $parser;
    }

    /**
     * Returns a new ApiDoc instance with more data.
     *
     * @param  ApiDoc            $annotation
     * @param  Route             $route
     * @param  \ReflectionMethod $method
     * @return ApiDoc
     */
    protected function extractData(ApiDoc $annotation, Route $route, \ReflectionMethod $method)
    {
        // create a new annotation
        $annotation = clone $annotation;

        // doc
        $annotation->setDocumentation($this->commentExtractor->getDocCommentText($method));

        // parse annotations
        $this->parseAnnotations($annotation, $route, $method);

        // route
        $annotation->setRoute($route);

        // input (populates 'parameters' for the formatters)
        if (null !== $input = $annotation->getInput()) {
            $parameters      = array();
            $normalizedInput = $this->normalizeClassParameter($input);

            $supportedParsers = array();
            foreach ($this->getParsers($normalizedInput) as $parser) {
                if ($parser->supports($normalizedInput)) {
                    $supportedParsers[] = $parser;
                    $parameters         = $this->mergeParameters($parameters, $parser->parse($normalizedInput));
                }
            }

            foreach ($supportedParsers as $parser) {
                if ($parser instanceof PostParserInterface) {
                    $parameters = $this->mergeParameters(
                        $parameters,
                        $parser->postParse($normalizedInput, $parameters)
                    );
                }
            }

            $parameters = $this->clearClasses($parameters);

            if ('PUT' === $method) {
                // All parameters are optional with PUT (update)
                array_walk($parameters, function ($val, $key) use (&$data) {
                    $parameters[$key]['required'] = false;
                });
            }

            $annotation->setParameters($parameters);
        }

        // output (populates 'response' for the formatters)
        if (null !== $output = $annotation->getOutput()) {
            $response         = array();
            $normalizedOutput = $this->normalizeClassParameter($output);

            foreach ($this->getParsers($normalizedOutput) as $parser) {
                if ($parser->supports($normalizedOutput)) {
                    $response = $this->mergeParameters($response, $parser->parse($normalizedOutput));
                }
            }

            $response = $this->clearClasses($response);

            $annotation->setResponse($response);
        }

        return $annotation;
    }

    protected function normalizeClassParameter($input)
    {
        $defaults = array(
            'class'   => '',
            'groups'  => array(),
        );

        // normalize strings
        if (is_string($input)) {
            $input = array('class' => $input);
        }

        // normalize groups
        if (isset($input['groups']) && is_string($input['groups'])) {
            $input['groups'] = array_map('trim', explode(',', $input['groups']));
        }

        return array_merge($defaults, $input);
    }

    /**
     * Merges two parameter arrays together. This logic allows documentation to be built
     * from multiple parser passes, with later passes extending previous passes:
     *  - Boolean values are true if any parser returns true.
     *  - Requirement parameters are concatenated.
     *  - Other string values are overridden by later parsers when present.
     *  - Array parameters are recursively merged.
     *
     * @param  array $p1 The pre-existing parameters array.
     * @param  array $p2 The newly-returned parameters array.
     * @return array The resulting, merged array.
     */
    protected function mergeParameters($p1, $p2)
    {
        $params = $p1;

        foreach ($p2 as $propname => $propvalue) {
            if (!isset($p1[$propname])) {
                $params[$propname] = $propvalue;
            } else {
                $v1 = $p1[$propname];

                foreach ($propvalue as $name => $value) {
                    if (is_array($value)) {
                        if (isset($v1[$name]) && is_array($v1[$name])) {
                            $v1[$name] = $this->mergeParameters($v1[$name], $value);
                        } else {
                            $v1[$name] = $value;
                        }
                    } elseif (!is_null($value)) {
                        if (in_array($name, array('required', 'readonly'))) {
                            $v1[$name] = $v1[$name] || $value;
                        } elseif (in_array($name, array('requirement'))) {
                            if (isset($v1[$name])) {
                                $v1[$name] .= ', ' . $value;
                            } else {
                                $v1[$name] = $value;
                            }
                        } else {
                            $v1[$name] = $value;
                        }
                    }
                }

                $params[$propname] = $v1;
            }
        }

        return $params;
    }

    /**
     * Parses annotations for a given method, and adds new information to the given ApiDoc
     * annotation. Useful to extract information from the FOSRestBundle annotations.
     *
     * @param ApiDoc           $annotation
     * @param Route            $route
     * @param ReflectionMethod $method
     */
    protected function parseAnnotations(ApiDoc $annotation, Route $route, \ReflectionMethod $method)
    {
        $annots = $this->reader->getMethodAnnotations($method);
        foreach ($this->handlers as $handler) {
            $handler->handle($annotation, $annots, $route, $method);
        }
    }

    /**
     * Clears the temporary 'class' parameter from the parameters array before it is returned.
     *
     * @param  array $array The source array.
     * @return array The cleared array.
     */
    protected function clearClasses($array)
    {
        if (is_array($array)) {
            unset($array['class']);
            foreach ($array as $name => $item) {
                $array[$name] = $this->clearClasses($item);
            }
        }

        return $array;
    }

    private function getParsers(array $parameters)
    {
        if (isset($parameters['parsers'])) {
            $parsers = array();
            foreach ($this->parsers as $parser) {
                if (in_array(get_class($parser), $parameters['parsers'])) {
                    $parsers[] = $parser;
                }
            }
        } else {
            $parsers = $this->parsers;
        }

        return $parsers;
    }
}
