<?php
/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Controller\User;

use Chamilo\CoreBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ProfileController.
 *
 * @package Chamilo\CoreBundle\Controller
 */
class ProfileController extends BaseController
{
    /**
     * My files.
     *
     * @Route("/{username}/files", methods={"GET"})
     *
     */
    public function fileAction($username)
    {
        if ($this->getUser()->getUsername() != $username) {
            return $this->abort(401);
        }

        $userId = \UserManager::get_user_id_from_username($username);
        $userInfo = api_get_user_info($userId);

        $this->getTemplate()->assign(
            'driver_list',
            'PersonalDriver,DropBoxDriver'
        );

        $editor = $this->getTemplate()->renderTemplate(
            $this->getHtmlEditor()->getEditorStandAloneTemplate()
        );

        $this->getTemplate()->assign('user', $userInfo);
        $this->getTemplate()->assign('editor', $editor);

        $response = $this->getTemplate()->renderTemplate(
            $this->getTemplatePath().'files.tpl'
        );

        return new Response($response, 200, []);
    }

    /**
     * Gets that rm.wav sound.
     *
     * @Route("/{username}/sounds/{file}", methods={"GET"})
     *
     */
    public function getSoundAction()
    {
        $file = api_get_path(LIBRARY_PATH).'elfinder/rm.wav';

        return $this->app->sendFile($file);
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplatePath()
    {
        return 'user/';
    }
}
