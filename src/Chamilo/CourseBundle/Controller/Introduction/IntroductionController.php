<?php
/* For licensing terms, see /license.txt */

namespace Chamilo\CourseBundle\Controller\Introduction;

use Chamilo\CourseBundle\Controller\ToolBaseController;
use Chamilo\CoreBundle\Entity\CToolIntro;
use Symfony\Component\HttpFoundation\Response;
use Chamilo\CoreBundle\Controller\CrudController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class IntroductionToolController
 * @package Chamilo\CourseBundle\Controller\Introduction
 * @author Julio Montoya <gugli100@gmail.com>
 * @Route("/introduction")
 */
class IntroductionController extends ToolBaseController
{

    /**
     * @Route("/edit/{tool}")
     * @Method({"GET|POST"})
     *
     * @param string $tool
     * @return Response
     */
    public function editAction($tool)
    {
        $message = null;
        // @todo use proper functions not api functions.
        $courseId = api_get_course_int_id();
        $sessionId = api_get_session_id();
        $tool = \Database::escape_string($tool);

        $TBL_INTRODUCTION = \Database::get_course_table(TABLE_TOOL_INTRO);

        $url = $this->generateUrl(
            'chamilo_course_introduction_introduction_edit',
            array('tool' => $tool, 'course' => api_get_course_id())
        );

        $form = $this->getFormValidator($url, $tool);

        if ($form->validate()) {
            $values  = $form->exportValues();
            $content = $values['content'];

            $sql = "REPLACE $TBL_INTRODUCTION
                    SET c_id = $courseId,
                        id = '$tool',
                        intro_text='".\Database::escape_string($content)."',
                        session_id='".intval($sessionId)."'";
            \Database::query($sql);
            $message = \Display::return_message(get_lang('IntroductionTextUpdated'), 'confirmation', false);
        } else {

            $sql = "SELECT intro_text FROM $TBL_INTRODUCTION
                    WHERE c_id = $courseId AND id='".$tool."' AND session_id = '".intval($sessionId)."'";
            $result = \Database::query($sql);
            $content = null;
            if (\Database::num_rows($result) > 0) {
                $row = \Database::fetch_array($result);
                $content = $row['intro_text'];
            }
            $form->setDefaults(array('content' => $content));
        }

        /*$this->getTemplate()->assign('content', $form->return_form());
        $this->getTemplate()->assign('message', $message);
        $response = $this->getTemplate()->renderLayout('layout_1_col.tpl');*/
        $response = null;
        return $this->render(
            'ChamiloCoreBundle:Legacy:index.html.twig',
            array(
                'content' => $form->return_form(),
                'message' => $message
            )
        );
    }

    /**
     * @Route("/delete/{tool}")
     * @Method({"GET"})
     *
     * @param string $tool
     * @return Response
     */
    public function deleteAction(Request $request)
    {
        //$tool
        $request = $this->getRequest();
        $courseId = $request->get('courseId');
        $sessionId = $request->get('sessionId');
        $criteria = array(
            'sessionId' => $sessionId,
            'id' => $tool,
            'cId' => $courseId
        );

        $toolIntro = $this->getRepository()->findOneBy($criteria);
        if ($toolIntro) {
            $this->getManager()->remove($toolIntro);
            $this->getManager()->flush();
            $this->get('session')->getFlashBag()->add('success', "IntroductionTextDeleted");
        }

        return $this->redirect('course_home');
    }

    /**
     *
     * @param $url
     * @param string
     * @return \FormValidator
     */
    public function getFormValidator($url, $tool)
    {
        $toolbar_set = 'IntroductionTool';
        $width = '100%';
        $height = '300';

        $editor_config = array('ToolbarSet' => $toolbar_set, 'Width' => $width, 'Height' => $height);

        $form = new \FormValidator('form', 'post', $url);
        $form->add_html_editor('content', null, null, false, $editor_config);
        if ($tool == 'course_homepage') {
            $form->addElement(
                'label',
                get_lang('YouCanUseAllTheseTags'),
                '(('.implode(')) <br /> ((', \CourseHome::availableTools()).'))'
            );
        }
        $form->addElement('button', 'submit', get_lang('SaveIntroText'));
        return $form;
    }

}
