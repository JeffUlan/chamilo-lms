<?php

/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Entity\Listener;

use Chamilo\CoreBundle\Entity\AbstractResource;
use Chamilo\CoreBundle\Entity\AccessUrl;
use Chamilo\CoreBundle\Entity\Course;
use Chamilo\CoreBundle\Entity\ResourceFile;
use Chamilo\CoreBundle\Entity\ResourceLink;
use Chamilo\CoreBundle\Entity\ResourceNode;
use Chamilo\CoreBundle\Entity\ResourceRight;
use Chamilo\CoreBundle\Entity\ResourceToRootInterface;
use Chamilo\CoreBundle\Entity\ResourceType;
use Chamilo\CoreBundle\Entity\ResourceWithUrlInterface;
use Chamilo\CoreBundle\Entity\Session;
use Chamilo\CoreBundle\Security\Authorization\Voter\ResourceNodeVoter;
use Chamilo\CoreBundle\ToolChain;
use Cocur\Slugify\SlugifyInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;

/**
 * Class ResourceListener.
 */
class ResourceListener
{
    protected $slugify;
    protected $request;
    protected $accessUrl;

    /**
     * ResourceListener constructor.
     */
    public function __construct(
        SlugifyInterface $slugify,
        ToolChain $toolChain,
        RequestStack $request,
        Security $security
    ) {
        $this->slugify = $slugify;
        $this->security = $security;
        $this->toolChain = $toolChain;
        $this->request = $request;
        $this->accessUrl = null;
    }

    public function getAccessUrl($em)
    {
        if (null === $this->accessUrl) {
            $request = $this->request->getCurrentRequest();
            if (null === $request) {
                throw new \Exception('An Request is needed');
            }
            $sessionRequest = $request->getSession();

            if (null === $sessionRequest) {
                throw new \Exception('An Session request is needed');
            }

            $id = $sessionRequest->get('access_url_id');
            $url = $em->getRepository('ChamiloCoreBundle:AccessUrl')->find($id);

            if ($url) {
                $this->accessUrl = $url;

                return $url;
            }
        }

        if (null === $this->accessUrl) {
            throw new \Exception('An AccessUrl is needed');
        }

        return $this->accessUrl;
    }

    public function prePersist(AbstractResource $resource, LifecycleEventArgs $event)
    {
        error_log('resource listener prePersist for obj: '.get_class($resource));
        $em = $event->getEntityManager();
        $request = $this->request;

        $url = null;
        if ($resource instanceof ResourceWithUrlInterface) {
            $url = $this->getAccessUrl($em);
            $resource->addUrl($url);
        }

        if ($resource->hasResourceNode()) {
            // This will attach the resource to the main resource node root (For example a Course).
            if ($resource instanceof ResourceToRootInterface) {
                $url = $this->getAccessUrl($em);
                $resource->getResourceNode()->setParent($url->getResourceNode());
            }
            error_log('resource has already a resource node. Do nothing');
            // Do not override resource node, it's already added.
            return true;
        }

        // Add resource node
        $creator = $this->security->getUser();

        if (null === $creator) {
            throw new \InvalidArgumentException('User creator not found');
        }

        $resourceNode = new ResourceNode();
        $resource->setResourceNode($resourceNode);
        $this->updateResourceName($resource);
        $resourceName = $resource->getResourceName();

        // @todo use static table instead of Doctrine
        $repo = $em->getRepository(ResourceType::class);
        $class = str_replace('Entity', 'Repository', get_class($event->getEntity()));
        $class .= 'Repository';
        $name = $this->toolChain->getResourceTypeNameFromRepository($class);
        $resourceType = $repo->findOneBy(['name' => $name]);

        if (null === $resourceType) {
            throw new \InvalidArgumentException('ResourceType not found');
        }

        $resourceNode
            ->setCreator($creator)
            ->setResourceType($resourceType)
        ;

        // Add resource directly to the resource node root (Example: for a course resource).
        if ($resource instanceof ResourceToRootInterface) {
            $url = $this->getAccessUrl($em);
            $resourceNode->setParent($url->getResourceNode());
        }

        if ($resource->hasParentResourceNode()) {
            $nodeRepo = $em->getRepository(ResourceNode::class);
            $parent = $nodeRepo->find($resource->getParentResourceNode());
            $resourceNode->setParent($parent);
        }

        if ($resource->hasUploadFile()) {
            // @todo check CreateResourceNodeFileAction
            /** @var File $uploadedFile */
            $uploadedFile = $request->getCurrentRequest()->files->get('uploadFile');

            if (empty($uploadedFile)) {
                $content = $request->getCurrentRequest()->get('contentFile');
                $title = $resourceName.'.html';
                $handle = tmpfile();
                fwrite($handle, $content);
                $meta = stream_get_meta_data($handle);
                $uploadedFile = new UploadedFile($meta['uri'], $title, 'text/html', null, true);
            }

            // File upload
            if ($uploadedFile instanceof UploadedFile) {
                $resourceFile = new ResourceFile();
                $resourceFile->setName($uploadedFile->getFilename());
                $resourceFile->setOriginalName($uploadedFile->getFilename());
                $resourceFile->setFile($uploadedFile);
                $em->persist($resourceFile);
                $resourceNode->setResourceFile($resourceFile);
            }
        }

        // Use by api platform
        $links = $resource->getResourceLinkArray();
        if ($links) {
            $courseRepo = $em->getRepository(Course::class);
            $sessionRepo = $em->getRepository(Session::class);

            foreach ($links as $link) {
                $resourceLink = new ResourceLink();
                if (isset($link['c_id']) && !empty($link['c_id'])) {
                    $course = $courseRepo->find($link['c_id']);
                    if ($course) {
                        $resourceLink->setCourse($course);
                    } else {
                        throw new \InvalidArgumentException(sprintf('Course #%s does not exists', $link['c_id']));
                    }
                }

                if (isset($link['session_id']) && !empty($link['session_id'])) {
                    $session = $sessionRepo->find($link['session_id']);
                    if ($session) {
                        $resourceLink->setSession($session);
                    } else {
                        throw new \InvalidArgumentException(sprintf('Session #%s does not exists', $link['session_id']));
                    }
                }

                if (isset($link['visibility'])) {
                    $resourceLink->setVisibility((int) $link['visibility']);
                } else {
                    throw new \InvalidArgumentException('Link needs a visibility key');
                }

                $resourceLink->setResourceNode($resourceNode);
                $em->persist($resourceLink);
            }
        }

        // Use by Chamilo.
        $this->setLinks($resourceNode, $resource, $em);

        if (null !== $resource->getParent()) {
            $resourceNode->setParent($resource->getParent()->getResourceNode());
        }

        error_log('Listener end, adding resource node');
        $resource->setResourceNode($resourceNode);

        // All resources should have a parent, except AccessUrl.
        if (!($resource instanceof AccessUrl)) {
            if (null == $resourceNode->getParent()) {
                throw new \InvalidArgumentException('Resource Node should have a parent');
            }
        }
    }

    /**
     * When updating a Resource.
     */
    public function preUpdate(AbstractResource $resource, PreUpdateEventArgs $event)
    {
        error_log('Resource listener preUpdate');

        $this->setLinks($resource->getResourceNode(), $resource, $event->getEntityManager());

        if ($resource->hasUploadFile()) {
            $uploadedFile = $resource->getUploadFile();

            // File upload
            if ($uploadedFile instanceof UploadedFile) {
                /*$resourceFile = new ResourceFile();
                $resourceFile->setName($uploadedFile->getFilename());
                $resourceFile->setOriginalName($uploadedFile->getFilename());
                $resourceFile->setFile($uploadedFile);
                $em->persist($resourceFile);*/
                //$resourceNode->setResourceFile($uploadedFile);
            }
        }
    }

    public function postUpdate(AbstractResource $resource, LifecycleEventArgs $event)
    {
        //error_log('resource listener postUpdate');
        //$em = $event->getEntityManager();
        //$this->updateResourceName($resource, $resource->getResourceName(), $em);
    }

    public function updateResourceName(AbstractResource $resource)
    {
        $resourceName = $resource->getResourceName();

        if (empty($resourceName)) {
            throw new \InvalidArgumentException('Resource needs a name');
        }

        $extension = $this->slugify->slugify(pathinfo($resourceName, PATHINFO_EXTENSION));
        if (empty($extension)) {
            //$slug = $this->slugify->slugify($resourceName);
        } else {
            /*$originalExtension = pathinfo($resourceName, PATHINFO_EXTENSION);
            $originalBasename = \basename($resourceName, $originalExtension);
            $slug = sprintf('%s.%s', $this->slugify->slugify($originalBasename), $originalExtension);*/
        }

        $resource->getResourceNode()->setTitle($resourceName);
    }

    public function setLinks(ResourceNode $resourceNode, AbstractResource $resource, $em)
    {
        error_log('setLinks');
        $links = $resource->getResourceLinkEntityList();
        if ($links) {
            foreach ($links as $link) {
                $rights = [];
                switch ($link->getVisibility()) {
                    case ResourceLink::VISIBILITY_PENDING:
                    case ResourceLink::VISIBILITY_DRAFT:
                        $editorMask = ResourceNodeVoter::getEditorMask();
                        $resourceRight = new ResourceRight();
                        $resourceRight
                            ->setMask($editorMask)
                            ->setRole(ResourceNodeVoter::ROLE_CURRENT_COURSE_TEACHER)
                        ;
                        $rights[] = $resourceRight;

                        break;
                }

                if (!empty($rights)) {
                    foreach ($rights as $right) {
                        $link->addResourceRight($right);
                    }
                }
                //error_log('link adding to node: '.$resource->getResourceNode()->getId());
                //error_log('link with user : '.$link->getUser()->getUsername());
                $resource->getResourceNode()->addResourceLink($link);

                $em->persist($link);
            }
        }
    }
}
