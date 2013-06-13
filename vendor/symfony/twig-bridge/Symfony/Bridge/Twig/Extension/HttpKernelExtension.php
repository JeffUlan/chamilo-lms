<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Bridge\Twig\Extension;

use Symfony\Component\HttpKernel\Fragment\FragmentHandler;
use Symfony\Component\HttpKernel\Controller\ControllerReference;

/**
 * Provides integration with the HttpKernel component.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class HttpKernelExtension extends \Twig_Extension
{
    private $handler;

    /**
     * Constructor.
     *
     * @param FragmentHandler $handler A FragmentHandler instance
     */
    public function __construct(FragmentHandler $handler)
    {
        $this->handler = $handler;
    }

    public function getFunctions()
    {
        return array(
            'render'     => new \Twig_Function_Method($this, 'renderFragment', array('is_safe' => array('html'))),
            'render_*'   => new \Twig_Function_Method($this, 'renderFragmentStrategy', array('is_safe' => array('html'))),
            'controller' => new \Twig_Function_Method($this, 'controller'),
        );
    }

    /**
     * Renders a fragment.
     *
     * @param string|ControllerReference $uri      A URI as a string or a ControllerReference instance
     * @param array                      $options  An array of options
     *
     * @return string The fragment content
     *
     * @see Symfony\Component\HttpKernel\Fragment\FragmentHandler::render()
     */
    public function renderFragment($uri, $options = array())
    {
        $strategy = isset($options['strategy']) ? $options['strategy'] : 'inline';
        unset($options['strategy']);

        return $this->handler->render($uri, $strategy, $options);
    }

    /**
     * Renders a fragment.
     *
     * @param string                     $strategy A strategy name
     * @param string|ControllerReference $uri      A URI as a string or a ControllerReference instance
     * @param array                      $options  An array of options
     *
     * @return string The fragment content
     *
     * @see Symfony\Component\HttpKernel\Fragment\FragmentHandler::render()
     */
    public function renderFragmentStrategy($strategy, $uri, $options = array())
    {
        return $this->handler->render($uri, $strategy, $options);
    }

    public function controller($controller, $attributes = array(), $query = array())
    {
        return new ControllerReference($controller, $attributes, $query);
    }

    public function getName()
    {
        return 'http_kernel';
    }
}
