<?php
/**
 * SidebarController.php
 * avanzu-admin
 * Date: 23.02.14
 */

namespace Chamilo\ThemeBundle\Controller;

use Chamilo\ThemeBundle\Event\ShowUserEvent;
use Chamilo\ThemeBundle\Event\SidebarMenuEvent;
use Chamilo\ThemeBundle\Event\SidebarMenuKnpEvent;
use Chamilo\ThemeBundle\Event\ThemeEvents;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SidebarController
 * @package Chamilo\ThemeBundle\Controller
 */
class SidebarController extends Controller
{
    /**
     * Avatar user panel
     * @return Response
     */
    public function userPanelAction()
    {
        if (!$this->getDispatcher()->hasListeners(ThemeEvents::THEME_SIDEBAR_USER)) {
            return new Response();
        }

        $userEvent = $this->getDispatcher()->dispatch(
            ThemeEvents::THEME_SIDEBAR_USER,
            new ShowUserEvent()
        );

        return $this->render(
            'ChamiloThemeBundle:Sidebar:user-panel.html.twig',
            [
                'user' => $userEvent->getUser()
            ]
        );
    }

    /**
     * User social network section
     * @return Response
     */
    public function socialPanelAction()
    {
        if (!$this->getDispatcher()->hasListeners(ThemeEvents::THEME_SIDEBAR_USER)) {
            return new Response();
        }

        $userEvent = $this->getDispatcher()->dispatch(
            ThemeEvents::THEME_SIDEBAR_USER,
            new ShowUserEvent()
        );

        return $this->render(
            'ChamiloThemeBundle:Sidebar:social-panel.html.twig',
            [
                'user' => $userEvent->getUser()
            ]
        );
    }

    /**
     * Search bar
     * @return Response
     */
    public function searchFormAction()
    {
        return $this->render('ChamiloThemeBundle:Sidebar:search-form.html.twig', []);
    }

    /**
     * @return object|\Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher|\Symfony\Component\HttpKernel\Debug\TraceableEventDispatcher
     */
    protected function getDispatcher()
    {
        return $this->get('event_dispatcher');
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function leftMenuAction(Request $request)
    {
        if (!$this->getDispatcher()->hasListeners(
            ThemeEvents::THEME_SIDEBAR_LEFT_MENU
        )
        ) {
            return new Response();
        }

        /** @var SidebarMenuKnpEvent $event */
        $event = $this->getDispatcher()->dispatch(
            ThemeEvents::THEME_SIDEBAR_LEFT_MENU,
            new SidebarMenuKnpEvent($request)
        );

        return $this->render(
            'ChamiloThemeBundle:Sidebar:left_menu.html.twig',
            [
                'menu' => $event->getMenu()
            ]
        );
    }
}
