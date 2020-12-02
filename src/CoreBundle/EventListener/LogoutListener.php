<?php

/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\EventListener;

use Database;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class LogoutListener
{
    protected $router;
    protected $checker;
    protected $storage;
    protected $em;

    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        AuthorizationCheckerInterface $checker,
        TokenStorageInterface $storage,
        EntityManagerInterface $em
    ) {
        $this->router = $urlGenerator;
        $this->checker = $checker;
        $this->storage = $storage;
        $this->em = $em;
    }

    /**
     * @return RedirectResponse|null
     */
    public function onSymfonyComponentSecurityHttpEventLogoutEvent(LogoutEvent $event)
    {
        $request = $event->getRequest();

        // Chamilo logout
        $request->getSession()->remove('_locale');
        $request->getSession()->remove('_locale_user');

        /*if (api_is_global_chat_enabled()) {
            $chat = new \Chat();
            $chat->setUserStatus(0);
        }*/

        $userId = $this->storage->getToken()->getUser()->getId();

        $table = Database :: get_main_table(TABLE_STATISTIC_TRACK_E_LOGIN);

        $sql = "SELECT login_id, login_date
                FROM $table
                WHERE login_user_id = $userId
                ORDER BY login_date DESC
                LIMIT 0,1";
        //$row = Database::query($sql);
        $loginId = null;
        $connection = $this->em->getConnection();
        $result = $connection->executeQuery($sql);
        if ($result->rowCount() > 0) {
            $row = $result->fetchAssociative();
            $loginId = $row['login_id'];
        }

        $loginAs = $this->checker->isGranted('ROLE_PREVIOUS_ADMIN');
        if (!$loginAs) {
            $current_date = api_get_utc_datetime();
            $sql = "UPDATE $table
                    SET logout_date='".$current_date."'
        		    WHERE login_id='$loginId'";
            $connection->executeQuery($sql);
        }

        $online_table = Database::get_main_table(TABLE_STATISTIC_TRACK_E_ONLINE);
        $sql = 'DELETE FROM '.$online_table." WHERE login_user_id = $userId";
        $connection->executeQuery($sql);

        $login = $this->router->generate('home');

        return new RedirectResponse($login);
    }
}
