<?php
/* For licensing terms, see /license.txt */
namespace ChamiloLMS\Component\Auth;

use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\Event\SwitchUserEvent;

class LoginListener
{
    /**
     * Fired on switch user, you can remove attributes or whatever you want here.
     * @param SwitchUserEvent $event
     */
    public function onSecuritySwitchUser(SwitchUserEvent $event)
    {
        /** @var \Entity\User $user */
        $user = $event->getTargetUser();
        /*var_dump($user );
        var_dump($event->getRequest()->getUser());
        */

        $request = $event->getRequest();
        $session = $request->getSession();
        \ChamiloSession::setSession($session);

        $session = $event->getRequest()->getSession();
        //$session->remove('partThatShouldNotCarryOver');
    }
}
