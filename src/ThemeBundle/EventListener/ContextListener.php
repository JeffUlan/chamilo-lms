<?php

/* For licensing terms, see /license.txt */

namespace Chamilo\ThemeBundle\EventListener;

use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * Class ContextListener.
 */
class ContextListener
{
    use ContainerAwareTrait;

    protected $indicator = '^/admin';

    public function onRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $uri = $request->getPathInfo();
        if (!preg_match('!'.$this->indicator.'!', $uri)) {
            return;
        }

        if (false == ($user = $this->getUser())) {
            return;
        }
    }

    public function getUser()
    {
        if (!$this->container->has('security.context')) {
            return false;
        }

        if (null === $token = $this->container->get('security.context')->getToken()) {
            return false;
        }

        if (!is_object($user = $token->getUser())) {
            return false;
        }

        return $user;
    }

    public function onController(FilterControllerEvent $event)
    {
    }
}
