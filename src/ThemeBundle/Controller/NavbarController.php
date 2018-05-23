<?php
/* For licensing terms, see /license.txt */

namespace Chamilo\ThemeBundle\Controller;

use Chamilo\ThemeBundle\Event\MessageListEvent;
use Chamilo\ThemeBundle\Event\NotificationListEvent;
use Chamilo\ThemeBundle\Event\ShowUserEvent;
use Chamilo\ThemeBundle\Event\TaskListEvent;
use Chamilo\ThemeBundle\Event\ThemeEvents;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class NavbarController.
 *
 * @package Chamilo\ThemeBundle\Controller
 */
class NavbarController extends Controller
{
    /**
     * @deprecated
     *
     * @param int $max
     *
     * @return Response
     */
    public function notificationsAction($max = 5)
    {
        if (!$this->getDispatcher()->hasListeners(ThemeEvents::THEME_NOTIFICATIONS)) {
            return new Response();
        }

        $listEvent = $this->getDispatcher()->dispatch(ThemeEvents::THEME_NOTIFICATIONS, new NotificationListEvent());

        return $this->render(
            'ChamiloThemeBundle:Navbar:notifications.html.twig',
            [
                'notifications' => $listEvent->getNotifications(),
                'total' => $listEvent->getTotal(),
            ]
        );
    }

    /**
     * @deprecated
     *
     * @param int $max
     *
     * @return Response
     */
    public function messagesAction($max = 5)
    {
        if (!$this->getDispatcher()->hasListeners(ThemeEvents::THEME_MESSAGES)) {
            return new Response();
        }

        $listEvent = $this->getDispatcher()->dispatch(ThemeEvents::THEME_MESSAGES, new MessageListEvent());

        return $this->render(
            'ChamiloThemeBundle:Navbar:messages.html.twig',
            [
                'messages' => $listEvent->getMessages(),
                'total' => $listEvent->getTotal(),
            ]
        );
    }

    /**
     * @deprecated
     *
     * @param int $max
     *
     * @return Response
     */
    public function tasksAction($max = 5)
    {
        if (!$this->getDispatcher()->hasListeners(ThemeEvents::THEME_TASKS)) {
            return new Response();
        }
        $listEvent = $this->getDispatcher()->dispatch(ThemeEvents::THEME_TASKS, new TaskListEvent());

        return $this->render(
            'ChamiloThemeBundle:Navbar:tasks.html.twig',
            [
                'tasks' => $listEvent->getTasks(),
                'total' => $listEvent->getTotal(),
            ]
        );
    }

    /**
     * @deprecated
     * @return Response
     */
    public function userAction()
    {
        if (!$this->getDispatcher()->hasListeners(ThemeEvents::THEME_NAVBAR_USER)) {
            return new Response();
        }
        $userEvent = $this->getDispatcher()->dispatch(ThemeEvents::THEME_NAVBAR_USER, new ShowUserEvent());

        return $this->render(
            'ChamiloThemeBundle:Navbar:user.html.twig',
            [
                'user' => $userEvent->getUser(),
            ]
        );
    }

    /**
     * @return object|\Symfony\Component\HttpKernel\Debug\TraceableEventDispatcher
     */
    protected function getDispatcher()
    {
        return $this->get('event_dispatcher');
    }
}
