<?php

/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Entity\Listener;

use Chamilo\CoreBundle\Entity\AccessUrl;
use Chamilo\CoreBundle\Entity\Session;
use Chamilo\CoreBundle\Repository\SessionRepository;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;

/**
 * Class SessionListener
 * Session entity listener, when a session is created/updated.
 */
class SessionListener
{
    protected $request;

    /**
     * ResourceListener constructor.
     */
    public function __construct(RequestStack $request, Security $security)
    {
        $this->security = $security;
        $this->request = $request;
    }

    /**
     * This code is executed when a new session is created.
     *
     * new object : prePersist
     * edited object: preUpdate
     *
     * @throws \Exception
     */
    public function prePersist(Session $session, LifecycleEventArgs $args)
    {
        $em = $args->getEntityManager();
        $id = $this->request->getCurrentRequest()->getSession()->get('access_url_id');
        $url = $em->getRepository('ChamiloCoreBundle:AccessUrl')->find($id);
        $session->addUrl($url);
        //$this->checkLimit($repo, $url);
    }

    /**
     * This code is executed when a session is updated.
     *
     * @throws \Exception
     */
    public function preUpdate(Session $session, LifecycleEventArgs $args)
    {
    }

    /**
     * @param SessionRepository $repo
     *
     * @throws \Exception
     */
    protected function checkLimit($repo, AccessUrl $url)
    {
        $limit = $url->getLimitSessions();

        if (!empty($limit)) {
            $count = $repo->getCountSessionByUrl($url);
            if ($count >= $limit) {
                api_warn_hosting_contact('hosting_limit_sessions', $limit);

                throw new \Exception('PortalSessionsLimitReached');
            }
        }
    }
}
