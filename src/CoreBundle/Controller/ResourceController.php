<?php
/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Controller;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ResourceController.
 *
 * @author Julio Montoya <gugli100@gmail.com>.
 *
 * @Route("/resource")
 *
 * @package Chamilo\CoreBundle\Controller
 */
class ResourceController extends BaseController
{
    /**
     * @Route("/upload", name="resource_upload", methods={"GET", "POST"}, options={"expose"=true})
     *
     * @return Response
     */
    public function uploadFile(): Response
    {
        $helper = $this->container->get('oneup_uploader.templating.uploader_helper');
        $endpoint = $helper->endpoint('courses');

        return $this->render(
            '@ChamiloCore/Resource/upload.html.twig',
            [
            ]
        );
    }

    /**
     * Gets a document from the courses/MATHS/document/file.jpg to the user.
     *
     * @todo check permissions
     *
     * @param string $course
     * @param string $file
     *
     * @return \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function getDocumentAction($course, $file)
    {
        try {
            /** @var Filesystem $fs */
            $fs = $this->container->get('oneup_flysystem.courses_filesystem');

            $path = $course.'/document/'.$file;

            // Has folder
            if (!$fs->has($course)) {
                return $this->abort();
            }

            /** @var Local $adapter */
            $adapter = $fs->getAdapter();
            $filePath = $adapter->getPathPrefix().$path;

            return new BinaryFileResponse($filePath);
        } catch (\InvalidArgumentException $e) {
            return $this->abort();
        }
    }

    /**
     * Gets a document from the data/courses/MATHS/scorm/file.jpg to the user.
     *
     * @todo check permissions
     *
     * @param Application $app
     * @param string      $courseCode
     * @param string      $file
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|void
     */
    public function getScormDocumentAction($app, $courseCode, $file)
    {
        try {
            $file = $app['chamilo.filesystem']->getCourseScormDocument(
                $courseCode,
                $file
            );

            return $app->sendFile($file->getPathname());
        } catch (\InvalidArgumentException $e) {
            return $app->abort(404, 'File not found');
        }
    }

    /**
     * Gets a document from the data/default_platform_document/* folder.
     *
     * @param Application $app
     * @param string      $file
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|void
     */
    public function getDefaultCourseDocumentAction($app, $file)
    {
        try {
            $file = $app['chamilo.filesystem']->get(
                'default_course_document/'.$file
            );

            return $app->sendFile($file->getPathname());
        } catch (\InvalidArgumentException $e) {
            return $app->abort(404, 'File not found');
        }
    }

    /**
     * @param Application $app
     * @param $groupId
     * @param $file
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|void
     */
    public function getGroupFile($app, $groupId, $file)
    {
        try {
            $file = $app['chamilo.filesystem']->get(
                'upload/groups/'.$groupId.'/'.$file
            );

            return $app->sendFile($file->getPathname());
        } catch (\InvalidArgumentException $e) {
            return $app->abort(404, 'File not found');
        }
    }

    /**
     * @param Application $app
     * @param $file
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|void
     */
    public function getUserFile($app, $file)
    {
        try {
            $file = $app['chamilo.filesystem']->get('upload/users/'.$file);

            return $app->sendFile($file->getPathname());
        } catch (\InvalidArgumentException $e) {
            return $app->abort(404, 'File not found');
        }
    }
}
