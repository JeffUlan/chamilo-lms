<?php
/* For licensing terms, see /license.txt */

namespace ChamiloLMS\Controller\Admin\Administrator;

use ChamiloLMS\Controller\CommonController;
use Silex\Application;
use Symfony\Component\Form\Extension\Validator\Constraints\FormValidator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Entity;
use ChamiloLMS\Form\BranchType;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * Class RoleController
 * @todo @route and @method function don't work yet
 * @package ChamiloLMS\Controller
 * @author Julio Montoya <gugli100@gmail.com>
 */
class BranchController extends CommonController
{
    /**
     * @Route("/")
     * @Method({"GET"})
     */
    public function indexAction()
    {
        $options = array(
            'decorate' => true,
            'rootOpen' => '<ul>',
            'rootClose' => '</ul>',
            'childOpen' => '<li>',
            'childClose' => '</li>',
            'nodeDecorator' => function($row) {
                $addChildren = '<a class="btn" href="'.$this->createUrl('add_from_parent_link', array('id' => $row['id'])).'">Add children</a>';
                $readLink = '<a href="'.$this->createUrl('read_link', array('id' => $row['id'])).'">'.$row['branchName'].'</a>';
                $editLink = '<a class="btn" href="'.$this->createUrl('update_link', array('id' => $row['id'])).'">Edit</a>';
                $deleteLink = '<a class="btn" href="'.$this->createUrl('delete_link', array('id' => $row['id'])).'"/>Delete</a>';
                return $readLink.' '.$addChildren.' '.$editLink.' '.$deleteLink;
            }
            //'representationField' => 'slug',
            //'html' => true
        );

        // @todo put this in a function
        $repo = $this->getRepository();

        $query = $this->getManager()
            ->createQueryBuilder()
            ->select('node')
            ->from('Entity\BranchSync', 'node')
            //->where('node.cId = 0')
            ->orderBy('node.root, node.lft', 'ASC')
            ->getQuery();

        $htmlTree = $repo->buildTree($query->getArrayResult(), $options);
        $this->get('template')->assign('tree', $htmlTree);
        $this->get('template')->assign('links', $this->generateLinks());
        $response = $this->get('template')->render_template($this->getTemplatePath().'list.tpl');
        return new Response($response, 200, array());
    }

    /**
    *
    * @Route("/{id}", requirements={"id" = "\d+"}, defaults={"foo" = "bar"})
    * @Method({"GET"})
    */
    public function readAction($id)
    {
        $template = $this->get('template');
        $template->assign('links', $this->generateLinks());
        $repo = $this->getRepository();
        $item = $this->getEntity($id);
        $children = $repo->children($item);
        $template->assign('item', $item);
        $template->assign('subitems', $children);
        $response = $template->render_template($this->getTemplatePath().'read.tpl');
        return new Response($response, 200, array());
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
    * @Route("/{id}/add")
    * @Method({"GET, POST"})
    */
    public function addFromParentAction($id)
    {
        $request = $this->getRequest();
        $formType = $this->getFormType();

        $branch = new Entity\BranchSync();
        $branch->setParentId($id);

        $form = $this->get('form.factory')->create($formType, $branch);

        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            if ($form->isValid()) {
                $item = $form->getData();
                $parent = $this->getEntity($item->getParentId());
                $item->setParent($parent);

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
        $response = $template->render_template($this->getTemplatePath().'add_from_parent.tpl');
        return new Response($response, 200, array());

    }

    /**
    *
    * @Route("/{id}/edit", requirements={"id" = "\d+"}, defaults={"foo" = "bar"})
    * @Method({"GET"})
    */
    public function editAction($id)
    {
        return parent::editAction($id);
    }

    /**
    *
    * @Route("/{id}/delete", requirements={"id" = "\d+"}, defaults={"foo" = "bar"})
    * @Method({"GET"})
    */
    public function deleteAction($id)
    {
        // Check if branch doesn't have children :(
        $repo = $this->getRepository();
        $item = $this->getEntity($id);
        $children = $repo->children($item);
        if (count($children) == 0) {
            return parent::deleteAction($id);
        } else {
            $this->get('session')->getFlashBag()->
                add('warning', "Please remove all children of this node before you try to delete it.");
            $url = $this->createUrl('list_link');
            return $this->redirect($url);
        }
    }

    /**
    * //Route("/search/{keyword}")
    * @Route("/search/")
    * @Method({"GET"})
    */
    public function searchAction()
    {
        $request = $this->getRequest();
        $keyword = $request->get('tag');
        $repo = $this->getRepository();
        $entities = $repo->searchByKeyword($keyword);
        $data = array();
        if ($entities) {
            /** Entity\BranchSync $entity */
            foreach ($entities as $entity) {
                $data[] = array(
                    'key' => (string) $entity->getId(),
                    'value' => $entity->getBranchName(),
                );
            }
        }
        return new JsonResponse($data);
    }

    /**
    * @Route("/exists/")
    * @Method({"GET"})
    */
    public function existsAction()
    {
        $request = $this->getRequest();
        $id = $request->get('id');
        $item = $this->getEntity($id);
        $repo = $this->getRepository();
        $result = 0;
        if ($item) {
            $result  = 1;
        }
        return new Response($result, 200, array());
    }

    protected function getControllerAlias()
    {
        return 'branch.controller';
    }

    protected function generateDefaultCrudRoutes()
    {
        $routes = parent::generateDefaultCrudRoutes();
        $routes['add_from_parent_link'] = 'branch.controller:addFromParentAction';
        return $routes ;
    }


    /**
    * {@inheritdoc}
    */
    protected function getTemplatePath()
    {
        return 'admin/administrator/branches/';
    }

    /**
     * @return \Entity\Repository\BranchSyncRepository
     */
    protected function getRepository()
    {
        return $this->get('orm.em')->getRepository('Entity\BranchSync');
    }

    /**
     * {@inheritdoc}
     */
    protected function getNewEntity()
    {
        return new Entity\BranchSync();
    }

    /**
     * {@inheritdoc}
     */
    protected function getFormType()
    {
        return new BranchType();
    }
}
