<?php

namespace JMS\DiExtraBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * This pass allows you to easily create lazy-loading service maps.
 *
 * ```php
 *    $container->addCompilerPass(new LazyServiceMapPass(
 *        'jms_serializer.visitor',
 *        'format',
 *        function(ContainerBuilder $container, Definition $def) {
 *            $container->getDefinition('jms_serializer')
 *                ->addArgument($def);
 *        }
 *    ));
 * ```
 *
 * In the example above, we make the definition of visitors lazy-loading.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class LazyServiceMapPass implements CompilerPassInterface, \Serializable
{
    private $tagName;
    private $keyAttributeName;
    private $callable;

    public function __construct($tagName, $keyAttributeName, $callable)
    {
        $this->tagName = $tagName;
        $this->keyAttributeName = $keyAttributeName;
        $this->callable = $callable;
    }

    public function process(ContainerBuilder $container)
    {
        if ( ! is_callable($this->callable)) {
            throw new \RuntimeException('The callable is invalid. If you had serialized this pass, the original callable might not be available anymore.');
        }

        $serviceMap = array();
        foreach ($container->findTaggedServiceIds($this->tagName) as $id => $tags) {
            foreach ($tags as $tag) {
                if ( ! isset($tag[$this->keyAttributeName])) {
                    throw new \RuntimeException(sprintf('The attribute "%s" must be set for service "%s" and tag "%s".', $this->keyAttributeName, $id, $this->tagName));
                }
    
                $serviceMap[$tag[$this->keyAttributeName]] = $id;
            }
        }

        $def = new Definition('JMS\DiExtraBundle\DependencyInjection\Collection\LazyServiceMap');
        $def->addArgument(new Reference('service_container'));
        $def->addArgument($serviceMap);

        call_user_func($this->callable, $container, $def);
    }

    public function serialize()
    {
        return serialize(array($this->tagName, $this->keyAttributeName));
    }

    public function unserialize($str)
    {
        list($this->tagName, $this->keyAttributeName) = unserialize($str);
    }
}
