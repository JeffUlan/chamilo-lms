<?php
/* For licensing terms, see /license.txt */

namespace ChamiloLMS\Controller\Tool\Curriculum;

use ChamiloLMS\Controller\CommonController;
use Silex\Application;
use Symfony\Component\Form\Extension\Validator\Constraints\FormValidator;
use Symfony\Component\HttpFoundation\Response;
use Entity;
use ChamiloLMS\Form\CurriculumItemType;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * Class CurriculumItemController
 * @package ChamiloLMS\Controller
 * @author Julio Montoya <gugli100@gmail.com>
 */
class CurriculumItemController extends CommonController
{
    /**
     *
     * @Route("/")
     * @Method({"GET"})
     */
    public function indexAction()
    {
        return parent::listingAction();
    }

    /**
    *
    * @Route("/{id}", requirements={"id" = "\d+"})
    * @Method({"GET"})
    */
    public function readAction($id)
    {
        return parent::readAction($id);
    }

    /**
    * @Route("/add")
    * @Method({"GET"})
    */
    public function addAction()
    {
        return parent::addAction();
    }

    /**
    * @Route("/add-from-category/{id}")
    * @Method({"GET, POST"})
    */
    public function addFromCategoryAction($id)
    {
         $breadcrumbs = array(
            array(
                'name' => get_lang('Curriculum'),
                'url' => array(
                    'route' => 'curriculum_user.controller:indexAction',
                    'routeParameters' => array(
                        'course' => $this->getCourse()->getCode()
                    )
                )
            ),
            array(
                'name' => get_lang('Categories'),
                'url' => array(
                    'route' => 'curriculum_category.controller:indexAction',
                    'routeParameters' => array(
                        'course' => $this->getCourse()->getCode()
                    )
                )

            ),
            array(
                'name' => get_lang('AddItems')
            )
        );
        $this->setBreadcrumb($breadcrumbs);
        $request = $this->getRequest();
        $formType = $this->getFormType();

        $entity = new Entity\CurriculumItem();

        $category = $this->get('orm.em')->getRepository('Entity\CurriculumCategory')->find($id);
        $entity->setCategory($category);

        $form = $this->get('form.factory')->create($formType, $entity);

        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            if ($form->isValid()) {
                /** @var Entity\CurriculumCategory $item */
                $item = $form->getData();
                $em = $this->getManager();
                $em->persist($item);
                $em->flush();

                $this->get('session')->getFlashBag()->add('success', "Added");
                $url = $this->createUrl('list_link');
                return $this->redirect($url);
            }
        }

        $template = $this->get('template');
        $template->assign('links', $this->generateLinks());
        $template->assign('form', $form->createView());
        $template->assign('parent_id', $id);
        $response = $template->render_template($this->getTemplatePath().'add_from_category.tpl');

        return new Response($response, 200, array());
    }

    /**
    *
    * @Route("/{id}/edit", requirements={"id" = "\d+"})
    * @Method({"GET"})
    */
    public function editAction($id)
    {
         $breadcrumbs = array(
            array(
                'name' => get_lang('Curriculum'),
                'url' => array(
                    'route' => 'curriculum_user.controller:indexAction',
                    'routeParameters' => array(
                        'course' => $this->getCourse()->getCode()
                    )
                )
            ),
            array(
                'name' => get_lang('Categories'),
                'url' => array(
                    'route' => 'curriculum_category.controller:indexAction',
                    'routeParameters' => array(
                        'course' => $this->getCourse()->getCode()
                    )
                )

            ),
            array(
                'name' => get_lang('Edit')
            )
        );
        $this->setBreadcrumb($breadcrumbs);
        return parent::editAction($id);
    }

    /**
    *
    * @Route("/{id}/delete", requirements={"id" = "\d+"})
    * @Method({"GET"})
    */
    public function deleteAction($id)
    {
        return parent::deleteAction($id);
    }

    protected function getControllerAlias()
    {
        return 'curriculum_item.controller';
    }

    protected function generateDefaultCrudRoutes()
    {
        $routes = parent::generateDefaultCrudRoutes();
        $routes['add_from_category'] = 'curriculum_item.controller:addFromCategoryAction';
        return $routes ;
    }

    /**
    * {@inheritdoc}
    */
    protected function getTemplatePath()
    {
        return 'tool/curriculum/item/';
    }

    /**
     * {@inheritdoc}
     */
    protected function getRepository()
    {
        return $this->get('orm.em')->getRepository('Entity\CurriculumItem');
    }

    /**
     * {@inheritdoc}
     */
    protected function getNewEntity()
    {
        return new Entity\CurriculumItem();
    }

    /**
     * {@inheritdoc}
     */
    protected function getFormType()
    {
        return new CurriculumItemType();
    }
}
