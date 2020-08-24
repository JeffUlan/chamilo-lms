<?php

/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Chamilo\CourseBundle\Entity\CGroup;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 * @ORM\EntityListeners({"Chamilo\CoreBundle\Entity\Listener\ResourceListener"})
 */
abstract class AbstractResource
{
    /**
     * @var string|null
     *
     * @ApiProperty(iri="http://schema.org/contentUrl")
     * @Groups({"resource_file:read", "resource_node:read", "document:read", "media_object_read"})
     */
    public $contentUrl;

    /**
     * @var string|null
     *
     * @Groups({"resource_file:read", "resource_node:read", "document:read", "document:write", "media_object_read"})
     */
    public $contentFile;

    /**
     * @Assert\Valid()
     * @ApiSubresource()
     * @Groups({"resource_node:read", "resource_node:write", "document:write" })
     * @ORM\OneToOne(
     *     targetEntity="Chamilo\CoreBundle\Entity\ResourceNode",
     *     cascade={"persist", "remove"},
     *     orphanRemoval=true
     * )
     * @ORM\JoinColumn(name="resource_node_id", referencedColumnName="id", onDelete="CASCADE")
     */
    public $resourceNode;

    /**
     * @Groups({"resource_node:read", "resource_node:write", "document:read", "document:write"})
     */
    public $parentResourceNode;

    /**
     * @ApiProperty(iri="http://schema.org/image")
     */
    public $uploadFile;

    /**
     * @Groups({"resource_node:read", "document:read"})
     */
    public $resourceLinkList;

    /** @var ResourceLink[] */
    public $linkEntityList;

    /** @var AbstractResource */
    public $parentResource;

    abstract public function getResourceName(): string;

    abstract public function setResourceName(string $name);

    public function addLink(ResourceLink $link)
    {
        $this->linkEntityList[] = $link;

        return $this;
    }

    public function addCourseLink(Course $course, Session $session = null, CGroup $group = null)
    {
        $resourceLink = new ResourceLink();
        $resourceLink
            ->setVisibility(ResourceLink::VISIBILITY_PUBLISHED)
            ->setCourse($course)
            ->setSession($session)
            ->setGroup($group)
        ;
        $this->addLink($resourceLink);

        return $this;
    }

    public function addGroupLink(Course $course, Session $session = null, CGroup $group = null)
    {
        $resourceNode = $this->getResourceNode();
        $exists = $resourceNode->getResourceLinks()->exists(
            function ($key, $element) use ($group) {
                if ($element->getGroup()) {
                    return $group->getIid() == $element->getGroup()->getIid();
                }
            }
        );

        if (false === $exists) {
            $resourceLink = new ResourceLink();
            $resourceLink
                ->setResourceNode($resourceNode)
                ->setCourse($course)
                ->setSession($session)
                ->setGroup($group)
                ->setVisibility(ResourceLink::VISIBILITY_PUBLISHED)
            ;
            $this->addLink($resourceLink);
        }

        return $this;
    }

    public function addUserLink(User $user, Course $course = null, Session $session = null, CGroup $group = null)
    {
        $resourceLink = new ResourceLink();
        $resourceLink
            ->setVisibility(ResourceLink::VISIBILITY_PUBLISHED)
            ->setUser($user)
            ->setCourse($course)
            ->setSession($session)
            ->setGroup($group)
        ;

        $this->addLink($resourceLink);

        return $this;
    }

    public function setParent(AbstractResource $parent)
    {
        $this->parentResource = $parent;

        return $this;
    }

    public function getParent()
    {
        return $this->parentResource;
    }

    /**
     * @param array $userList User id list
     */
    public function addResourceToUserList(
        array $userList,
        Course $course = null,
        Session $session = null,
        CGroup $group = null
    ) {
        if (!empty($userList)) {
            foreach ($userList as $user) {
                $this->addUserLink($user, $course, $session, $group);
            }
        }

        return $this;
    }

    public function getLinks()
    {
        return $this->linkEntityList;
    }

    public function addResourceLink($link)
    {
        $this->resourceLinkList[] = $link;

        return $this;
    }

    public function setResourceLinkList($links)
    {
        $this->resourceLinkList = $links;

        return $this;
    }

    public function getResourceLinkListFromEntity()
    {
        return $this->resourceLinkList;
    }

    /*public function getResourceLinkList(): array
    {
        $resourceNode = $this->getResourceNode();
        $links = $resourceNode->getResourceLinks();
        $resourceLinkList = [];

        foreach ($links as $link) {
            $resourceLinkList[] = [
                'id' => $link->getId(),
                'session' => $link->getSession(),
                'course' => $link->getCourse(),
                'visibility' => $link->getVisibility(),
                'visibilityName' => $link->getVisibilityName(),
                'group' => $link->getGroup(),
                'userGroup' => $link->getUserGroup(),
            ];
        }

        return $resourceLinkList;
    }*/

    public function hasParentResourceNode(): bool
    {
        return null !== $this->parentResourceNode;
    }

    public function setParentResourceNode($resourceNode): self
    {
        $this->parentResourceNode = $resourceNode;

        return $this;
    }

    public function getParentResourceNode()
    {
        return $this->parentResourceNode;
    }

    public function hasUploadFile(): bool
    {
        return null !== $this->uploadFile;
    }

    public function getUploadFile()
    {
        return $this->uploadFile;
    }

    public function setUploadFile($file): self
    {
        $this->uploadFile = $file;

        return $this;
    }

    public function setResourceNode(ResourceNode $resourceNode): self
    {
        $this->resourceNode = $resourceNode;

        return $this;
    }

    public function hasResourceNode(): bool
    {
        return $this->resourceNode instanceof ResourceNode;
    }

    public function getResourceNode(): ResourceNode
    {
        return $this->resourceNode;
    }

    public function getCourseSessionResourceLink(Course $course, Session $session = null): ?ResourceLink
    {
        return $this->getFirstResourceLinkFromCourseSession($course, $session);
    }

    public function getFirstResourceLink(): ?ResourceLink
    {
        $resourceNode = $this->getResourceNode();

        if ($resourceNode && $resourceNode->getResourceLinks()->count()) {
            $result = $resourceNode->getResourceLinks()->first();
            if ($result) {
                return $result;
            }
        }

        return null;
    }

    /**
     * See ResourceLink to see the visibility constants. Example: ResourceLink::VISIBILITY_DELETED.
     */
    public function getLinkVisibility(Course $course, Session $session = null): ?ResourceLink
    {
        return $this->getCourseSessionResourceLink($course, $session)->getVisibility();
    }

    public function isVisible(Course $course, Session $session = null): bool
    {
        $link = $this->getCourseSessionResourceLink($course, $session);
        if (null === $link) {
            return false;
        }

        return ResourceLink::VISIBILITY_PUBLISHED === $link->getVisibility();
    }

    public function getFirstResourceLinkFromCourseSession(Course $course, Session $session = null): ?ResourceLink
    {
        $criteria = Criteria::create();
        $criteria
            ->where(Criteria::expr()->eq('course', $course))
            ->andWhere(
                Criteria::expr()->eq('session', $session)
            );
        $resourceNode = $this->getResourceNode();

        $result = null;
        if ($resourceNode && $resourceNode->getResourceLinks()->count() > 0) {
            //var_dump($resourceNode->getResourceLinks()->count());
            foreach ($resourceNode->getResourceLinks() as $link) {
                //var_dump(get_class($link));
            }
            $result = $resourceNode->getResourceLinks()->matching($criteria)->first();
            //var_dump($result);
            if ($result) {
                return $result;
            }
        }

        return null;
    }
}
