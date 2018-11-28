<?php
/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Menu;

use Chamilo\FaqBundle\Entity\Category;
use Chamilo\PageBundle\Entity\Page;
use Chamilo\PageBundle\Entity\Site;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Knp\Menu\Matcher\Matcher;
use Knp\Menu\Matcher\Voter\UriVoter;
use Knp\Menu\Renderer\ListRenderer;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Chamilo\CoreBundle\Menu\LeftMenuBuilder;

/**
 * Class NavBuilder.
 *
 * @package Chamilo\CoreBundle\Menu
 */
class NavBuilder implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param array $itemOptions The options given to the created menuItem
     * @param string $currentUri The current URI
     *
     * @return ItemInterface
     */
    public function createCategoryMenu(array $itemOptions = [], $currentUri = null)
    {
        $factory = $this->container->get('knp_menu.factory');
        $menu = $factory->createItem('categories', $itemOptions);

        $this->buildCategoryMenu($menu, $itemOptions, $currentUri);

        return $menu;
    }

    /**
     * @param ItemInterface $menu The item to fill with $routes
     * @param array $options The item options
     * @param string $currentUri The current URI
     */
    public function buildCategoryMenu(ItemInterface $menu, array $options = [], $currentUri = null)
    {
        //$categories = $this->categoryManager->getCategoryTree();

        //$this->fillMenu($menu, $categories, $options, $currentUri);
        $menu->addChild('home', ['route' => 'home']);
    }

    /**
     * Top menu left.
     *
     * @param FactoryInterface $factory
     * @param array $options
     *
     * @return ItemInterface
     */
    public function menuApp(FactoryInterface $factory, array $options): ItemInterface
    {
        $container = $this->container;
        $checker = $container->get('security.authorization_checker');
        $translator = $container->get('translator');
        $urlRoot = $this->container->get('router')->getContext()->getBaseUrl();
        $mainURL = $this->container->get('router')->generate('web.main');
        $baseUrl = $this->container->get('router')->getContext()->getHost();
        $baseHttp = $this->container->get('router')->getContext()->getScheme();
        $urlPortal = $baseHttp."://".$baseUrl."/";

        if ($urlRoot == "/public") {
            $mainURL = substr($mainURL, 7);
        }

        $menu = $factory->createItem('root');
        $settingsManager = $container->get('chamilo.settings.manager');

        $menu->addChild(
            'home',
            [
                'label' => $translator->trans('Home'),
                'route' => 'legacy_index',
                'icon' => 'home',
            ]
        );

        if ($checker && $checker->isGranted('IS_AUTHENTICATED_FULLY')) {
            $menu->addChild(
                'courses',
                [
                    'label' => $translator->trans('Courses'),
                    'uri' => $urlPortal . 'user_portal.php',
                    'icon' => 'book'
                ]
            );

            $menu['courses']->addChild(
                'courses',
                [
                    'label' => $translator->trans('All my courses'),
                    'uri' => $urlPortal . 'user_portal.php'
                ]
            );

            $browse = $settingsManager->getSetting('display.allow_students_to_browse_courses');

            if ($browse == 'true') {
                if ($checker->isGranted('ROLE_STUDENT') && !api_is_drh() && !api_is_session_admin()
                ) {
                    $menu['courses']->addChild(
                        'catalog',
                        [
                            'label' => $translator->trans('Course catalog'),
                            'uri' => $mainURL . 'auth/courses.php'
                        ]
                    );
                }
            }

            $menu['courses']->addChild(
                $translator->trans('Course history'),
                [
                    'uri' => 'userportal'
                ]
            );

            if (api_is_allowed_to_create_course()) {
                $lang = $translator->trans('Create course');
                if ($settingsManager->getSetting('course.course_validation') == 'true') {
                    $lang = $translator->trans('Create course request');
                }

                $menu['courses']->addChild(
                    'create-course',
                    [
                        'label' => $lang,
                        'uri' => $mainURL . 'create_course/add_course.php'
                    ]
                );
            }


            if ($checker->isGranted('ROLE_ADMIN')) {
                $menu['courses']->addChild(
                    $translator->trans('Add Session'),
                    [
                        'uri' => $mainURL . 'session/session_add.php'
                    ]
                );
            }

            $menu->addChild(
                'calendar',
                [
                    'label' => $translator->trans('Calendar'),
                    'uri' => $mainURL . 'calendar/agenda_js.php',
                    'icon' => 'calendar-alt'
                ]
            );

            $menu->addChild(
                'reports',
                [
                    'label' => $translator->trans('Reporting'),
                    'uri' => $mainURL . 'mySpace/index.php',
                    'icon' => 'chart-bar'
                ]
            );

            if ('true' === $settingsManager->getSetting('social.allow_social_tool')) {
                $menu->addChild(
                    'social',
                    [
                        'label' => $translator->trans('Social'),
                        'uri' => $mainURL . 'social/home.php',
                        'icon' => 'heart'
                    ]
                );

                $menu['social']->addChild(
                    $translator->trans('My profile'),
                    [
                        'uri' => $mainURL . 'social/home.php'
                    ]
                );
                $menu['social']->addChild(
                    $translator->trans('My shared profile'),
                    [
                        'uri' => $mainURL . 'social/profile.php'
                    ]
                );
                $menu['social']->addChild(
                    $translator->trans('Friends'),
                    [
                        'uri' => $mainURL . 'social/friends.php'
                    ]
                );
                $menu['social']->addChild(
                    $translator->trans('Social Groups'),
                    [
                        'uri' => $mainURL . 'social/groups.php',
                    ]
                );
                $menu['social']->addChild(
                    $translator->trans('My Files'),
                    [
                        'uri' => $mainURL . 'social/myfiles.php'
                    ]
                );
            }

            if ($checker->isGranted('ROLE_ADMIN')) {
                $menu->addChild(
                    'dashboard',
                    [
                        'label' => $translator->trans('Dashboard'),
                        'uri' => $mainURL . 'dashboard/index.php',
                        'icon' => 'cube'
                    ]
                );

                $menu->addChild(
                    'administrator',
                    [
                        'label' => $translator->trans('Administration'),
                        'uri' => $mainURL . 'admin/index.php',
                        'icon' => 'cogs'
                    ]
                );

                $menu['administrator']->addChild(
                    'options',
                    [
                        'label' => $translator->trans('All options'),
                        'uri' => $mainURL . 'admin/index.php'
                    ]
                );

                $menu['administrator']->addChild(
                    'userlist',
                    [
                        'label' => $translator->trans('User list'),
                        'uri' => $mainURL . 'admin/user_list.php'
                    ]
                );
                $menu['administrator']->addChild(
                    'courselist',
                    [
                        'label' => $translator->trans('Course list'),
                        'uri' => $mainURL . 'admin/course_list.php'
                    ]
                );
                $menu['administrator']->addChild(
                    'sessionlist',
                    [
                        'label' => $translator->trans('Session list'),
                        'uri' => $mainURL . 'session/session_list.php'
                    ]
                );
                $menu['administrator']->addChild(
                    'languages',
                    [
                        'label' => $translator->trans('Languages'),
                        'uri' => $mainURL . 'admin/languages.php'
                    ]
                );
                $menu['administrator']->addChild(
                    'plugins',
                    [
                        'label' => $translator->trans('Plugins'),
                        'uri' => $mainURL . 'admin/settings.php?category=Plugins'
                    ]
                );
                $menu['administrator']->addChild(
                    'settings',
                    [
                        'label' => $translator->trans('Advanced settings'),
                        'uri' => '../public/admin/settings/platform'
                    ]
                );
            }
        }

        $categories = $container->get('doctrine')->getRepository('ChamiloFaqBundle:Category')->retrieveActive();
        if ($categories) {
            $faq = $menu->addChild(
                'FAQ',
                [
                    'route' => 'faq_index',
                ]
            );
            /** @var Category $category */
            foreach ($categories as $category) {
                $faq->addChild(
                    $category->getHeadline(),
                    [
                        'route' => 'faq',
                        'routeParameters' => [
                            'categorySlug' => $category->getSlug(),
                            'questionSlug' => '',
                        ],
                    ]
                )->setAttribute('divider_append', true);
            }
        }

        // Getting site information
        $site = $container->get('sonata.page.site.selector');
        $host = $site->getRequestContext()->getHost();
        $siteManager = $container->get('sonata.page.manager.site');
        /** @var Site $site */
        $site = $siteManager->findOneBy([
            'host' => [$host, 'localhost'],
            'enabled' => true,
        ]);

        $isLegacy = $container->get('request_stack')->getCurrentRequest()->get('load_legacy');
        $urlAppend = $container->getParameter('url_append');
        $legacyIndex = '';
        if ($isLegacy) {
            $legacyIndex = $urlAppend . '/public';
        }

        if ($site) {
            $pageManager = $container->get('sonata.page.manager.page');
            // Parents only of homepage
            $criteria = ['site' => $site, 'enabled' => true, 'parent' => 1];
            $pages = $pageManager->findBy($criteria);

            //$pages = $pageManager->loadPages($site);
            /** @var Page $page */
            foreach ($pages as $page) {
                /*if ($page->getRouteName() !== 'page_slug') {
                    continue;
                }*/

                // Avoid home
                if ($page->getUrl() === '/') {
                    continue;
                }

                if (!$page->isCms()) {
                    continue;
                }

                $url = $legacyIndex . $page->getUrl();

                $subMenu = $menu->addChild(
                    $page->getName(),
                    [
                        'route' => $page->getRouteName(),
                        'routeParameters' => [
                            'path' => $url,
                        ],
                    ]
                );

                /** @var Page $child */
                foreach ($page->getChildren() as $child) {
                    $url = $legacyIndex . $child->getUrl();
                    $subMenu->addChild(
                        $child->getName(),
                        [
                            'route' => $page->getRouteName(),
                            'routeParameters' => [
                                'path' => $url,
                            ],
                        ]
                    )->setAttribute('divider_append', true);
                }
            }
        }

        // Set CSS classes for the items
        foreach ($menu->getChildren() as $child) {
            $child
                ->setLinkAttribute('class', 'sidebar-link')
                ->setAttribute('class', 'nav-item');
        }

        return $menu;
    }

    /**
     * Course menu.
     *
     * @param FactoryInterface $factory
     * @param array $options
     *
     * @return ItemInterface
     */
    public function courseMenu(FactoryInterface $factory, array $options)
    {
        $checker = $this->container->get('security.authorization_checker');
        $menu = $factory->createItem('root');
        $translator = $this->container->get('translator');
        $checked = $this->container->get('session')->get('IS_AUTHENTICATED_FULLY');
        $settingsManager = $this->container->get('chamilo.settings.manager');

        if ($checked) {
            $menu->setChildrenAttribute('class', 'nav nav-pills nav-stacked');
            $menu->addChild(
                $translator->trans('MyCourses'),
                [
                    'route' => 'userportal',
                    'routeParameters' => ['type' => 'courses'],
                ]
            );

            return $menu;

            if (api_is_allowed_to_create_course()) {
                $lang = $translator->trans('CreateCourse');
                if ($settingsManager->getSetting('course.course_validation') == 'true') {
                    $lang = $translator->trans('CreateCourseRequest');
                }
                $menu->addChild(
                    $lang,
                    ['route' => 'add_course']
                );
            }

            $link = $this->container->get('router')->generate('web.main');

            $menu->addChild(
                $translator->trans('ManageCourses'),
                [
                    'uri' => $link . 'auth/courses.php?action=sortmycourses',
                ]
            );

            $browse = $settingsManager->getSetting('display.allow_students_to_browse_courses');

            if ($browse == 'true') {
                if ($checker->isGranted('ROLE_STUDENT') && !api_is_drh() && !api_is_session_admin()
                ) {
                    $menu->addChild(
                        $translator->trans('CourseCatalog'),
                        [
                            'uri' => $link . 'auth/courses.php',
                        ]
                    );
                } else {
                    $menu->addChild(
                        $translator->trans('Dashboard'),
                        [
                            'uri' => $link . 'dashboard/index.php',
                        ]
                    );
                }
            }

            /** @var \Knp\Menu\MenuItem $menu */
            $menu->addChild(
                $translator->trans('History'),
                [
                    'route' => 'userportal',
                    'routeParameters' => [
                        'type' => 'sessions',
                        'filter' => 'history',
                    ],
                ]
            );
        }

        return $menu;
    }
}
