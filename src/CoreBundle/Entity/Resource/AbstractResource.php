<?php
/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Entity\Resource;


use APY\DataGridBundle\Grid\Mapping as GRID;
use Chamilo\CoreBundle\Entity\Course;
use Chamilo\CoreBundle\Entity\Session;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 * @ORM\EntityListeners({"Chamilo\CoreBundle\Entity\Listener\ResourceListener"})
 */
abstract class AbstractResource implements ResourceInterface
{
    /**
     * @ORM\OneToOne(targetEntity="Chamilo\CoreBundle\Entity\Resource\ResourceNode", cascade={"remove"}, orphanRemoval=true)
     * @ORM\JoinColumn(name="resource_node_id", referencedColumnName="id", onDelete="CASCADE")
     */
    public $resourceNode;

    abstract public function getResourceName(): string;

    /**
     * @return $this
     */
    public function setResourceNode(ResourceNode $resourceNode): self
    {
        $this->resourceNode = $resourceNode;

        return $this;
    }

    /**
     * @return ResourceNode
     */
    public function getResourceNode(): ResourceNode
    {
        return $this->resourceNode;
    }

    /**
     * @param Course       $course
     * @param Session|null $session
     *
     * @return ResourceLink|null
     */
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
        if ($resourceNode && $resourceNode->getResourceLinks()) {
            $result = $resourceNode->getResourceLinks()->matching($criteria)->first();
        }

        return $result;
    }
}
