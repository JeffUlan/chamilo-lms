<?php
/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Controller;

use APY\DataGridBundle\Grid\Grid;
use APY\DataGridBundle\Grid\Row;
use APY\DataGridBundle\Grid\Source\Entity;
use Chamilo\CoreBundle\Component\Editor\CkEditor\CkEditor;
use Chamilo\CoreBundle\Component\Editor\Connector;
use Chamilo\CoreBundle\Component\Editor\Finder;
use Chamilo\CoreBundle\Component\Utils\ChamiloApi;
use Chamilo\CoreBundle\Entity\Resource\AbstractResource;
use Chamilo\CoreBundle\Repository\ResourceFactory;
use Chamilo\CoreBundle\Security\Authorization\Voter\ResourceNodeVoter;
use Chamilo\SettingsBundle\Manager\SettingsManager;
use FM\ElfinderBundle\Connector\ElFinderConnector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class EditorController.
 *
 * @Route("/editor")
 */
class EditorController extends BaseController
{
    /**
     * Get templates (left column when creating a document).
     *
     * @Route("/templates", methods={"GET"}, name="editor_templates")
     *
     * @return Response
     */
    public function editorTemplatesAction(TranslatorInterface $translator, RouterInterface $router)
    {
        $editor = new CkEditor(
            $translator,
            $router
        );
        $templates = $editor->simpleFormatTemplates();

        return $this->render(
            '@ChamiloTheme/Editor/templates.html.twig',
            ['templates' => $templates]
        );
    }

    /**
     * @Route("/myfilemanager", methods={"GET"}, name="editor_myfiles")
     */
    public function editorFileManager(): Response
    {
        \Chat::setDisableChat();
        $params = [
            'course_condition' => '?'.$this->getCourseUrlQuery(),
        ];

        return $this->render('@ChamiloTheme/Editor/elfinder.html.twig', $params);
    }

    /**
     * @Route("/resources/{tool}/{type}/{parentId}", methods={"GET"}, name="resources_filemanager")
     *
     * @param int $parentId
     */
    public function customEditorFileManager(ResourceFactory $resourceFactory, Request $request, $tool, $type, Grid $grid, $parentId = 0): Response
    {
        $id = $request->get('id');

        $course = $this->getCourse();
        $session = $this->getCourseSession();
        $sessionId = $session ? $session->getId() : 0;
        $parent = $course->getResourceNode();

        $repository = $resourceFactory->createRepository($tool, $type);
        $class = $repository->getRepository()->getClassName();

        if (!empty($parentId)) {
            $parent = $repository->getResourceNodeRepository()->find($parentId);
        }

        $this->denyAccessUnlessGranted(
            ResourceNodeVoter::VIEW,
            $parent,
            $this->trans('Unauthorised access to resource')
        );

        $source = new Entity($class, 'resource');

        $qb = $repository->getResourcesByCourse($course, $session, null, $parent);

        // 3. Set QueryBuilder to the source.
        $source->initQueryBuilder($qb);
        $grid->setSource($source);

        $title = $grid->getColumn('title');
        $title->setSafe(false);

        $grid->setLimits(20);
        $grid->setHiddenColumns(['iid']);

        /*$grid->setRouteUrl(
            $this->generateUrl(
                'chamilo_core_resource_list',
                [
                    'tool' => 'document',
                    'type' => 'files',
                    'cidReq' => $this->getCourse()->getCode(),
                    'sid' => $sessionId,
                    'id' => $id,
                ]
            )
        );*/

        $titleColumn = $repository->getTitleColumn($grid);

        $titleColumn->setTitle($this->trans('Name'));

        $grid->getColumn('filetype')->setTitle($this->trans('Type'));

        $routeParams = $this->getCourseParams();
        $removePath = $course->getResourceNode()->getPath();

        $titleColumn->manipulateRenderCell(
            function ($value, Row $row, $router) use ($tool, $type, $routeParams, $removePath) {
                /** @var AbstractResource $entity */
                $entity = $row->getEntity();
                $resourceNode = $entity->getResourceNode();
                $id = $resourceNode->getId();

                $myParams = $routeParams;
                $myParams['id'] = $id;
                $myParams['parentId'] = $id;
                $myParams['tool'] = $tool;
                $myParams['type'] = $type;

                unset($myParams[0]);
                $icon = $resourceNode->getIcon().' &nbsp;';
                if ($resourceNode->hasResourceFile()) {
                    $documentParams = $this->getCourseParams();
                    $documentParams['tool'] = $tool;
                    $documentParams['type'] = $type;
                    //$documentParams['file'] = $resourceNode->getPathForDisplayRemoveBase($removePath);
                    // use id instead of old path (like in Chamilo v1)
                    $documentParams['id'] = $resourceNode->getId();
                    $url = $router->generate(
                        'chamilo_core_resource_view',
                        $documentParams
                    );

                    return $icon.'<a href="'.$url.'" class="select_to_ckeditor">'.$value.'</a>';
                }

                $url = $router->generate(
                    'resources_filemanager',
                    $myParams
                );

                return $icon.'<a href="'.$url.'">'.$value.'</a>';
            }
        );

        return $grid->getGridResponse(
            '@ChamiloTheme/Editor/custom.html.twig',
            ['id' => $id, 'grid' => $grid, 'tool' => $tool, 'type' => $type]
        );
    }

    /**
     * @Route("/connector", methods={"GET", "POST"}, name="editor_connector")
     *
     * @return Response
     */
    public function editorConnector(TranslatorInterface $translator, RouterInterface $router)
    {
        $course = $this->getCourse();
        $session = $this->getCourseSession();

        /** @var Connector $connector */
        $connector = new Connector(
            $this->getDoctrine()->getManager(),
            [],
            $router,
            $translator,
            $this->container->get('security.authorization_checker'),
            $this->getUser(),
            $course,
            $session
        );

        $driverList = [
            'PersonalDriver',
            //'CourseDriver',
            //'CourseUserDriver',
            //'HomeDriver'
        ];
        $connector->setDriverList($driverList);

        $operations = $connector->getOperations();

        // Run elFinder
        ob_start();
        $finder = new Finder($operations);
        $elFinderConnector = new ElFinderConnector($finder);
        $elFinderConnector->run();
        $content = ob_get_contents();

        return $this->render(
            '@ChamiloTheme/layout_empty.html.twig',
            ['content' => $content]
        );
    }

    /**
     * @Route("/config", methods={"GET"}, name="config_editor")
     *
     * @return Response
     */
    public function configEditorAction(SettingsManager $settingsManager)
    {
        $moreButtonsInMaximizedMode = false;
        //$settingsManager = $this->get('chamilo.settings.manager');

        if ($settingsManager->getSetting('editor.more_buttons_maximized_mode') === 'true') {
            $moreButtonsInMaximizedMode = true;
        }

        return $this->render(
            '@ChamiloTheme/Editor/config_js.html.twig',
            [
                // @todo replace api_get_bootstrap_and_font_awesome
                'bootstrap_css' => api_get_bootstrap_and_font_awesome(true),
                'css_editor' => ChamiloApi::getEditorBlockStylePath(),
                'more_buttons_in_max_mode' => $moreButtonsInMaximizedMode,
            ]
        );
    }
}
