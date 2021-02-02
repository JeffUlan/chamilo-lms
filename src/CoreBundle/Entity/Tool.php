<?php

/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;

/**
 * Tool.
 *
 * @ORM\Table(name="tool")
 * @ORM\Entity
 */
class Tool
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(name="name", type="string", nullable=false, unique=true)
     */
    protected string $name;

    /**
     * @ORM\OneToMany(targetEntity="Chamilo\CoreBundle\Entity\ResourceType", mappedBy="tool", cascade={"persist", "remove"})
     */
    protected $resourceTypes;

    public function __construct()
    {
        $this->resourceTypes = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->getName();
    }

    /**
     * @return ArrayCollection
     */
    /*public function getToolResourceRight()
    {
        return $this->toolResourceRight;
    }*/

    /*public function setToolResourceRight($toolResourceRight)
    {
        $this->toolResourceRight = new ArrayCollection();

        foreach ($toolResourceRight as $item) {
            $this->addToolResourceRight($item);
        }
    }*/

    /**
     * @return $this
     */
    /*public function addToolResourceRight(ToolResourceRight $toolResourceRight)
    {
        $toolResourceRight->setTool($this);
        $this->toolResourceRight[] = $toolResourceRight;

        return $this;
    }*/

    /*public function getResourceNodes()
    {
        return $this->resourceNodes;
    }*/

    /**
     * @return $this
     */
    /*public function setResourceNodes($resourceNodes)
    {
        $this->resourceNodes = $resourceNodes;

        return $this;
    }*/

    /**
     * Get id.
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set name.
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return ArrayCollection
     */
    public function getResourceTypes()
    {
        return $this->resourceTypes;
    }

    public function hasResourceType(ResourceType $resourceType): bool
    {
        if ($this->resourceTypes->count()) {
            $criteria = Criteria::create()->where(
                Criteria::expr()->eq('name', $resourceType->getName())
            );
            $relation = $this->resourceTypes->matching($criteria);

            return $relation->count() > 0;
        }

        return false;
    }

    public function setResourceTypes($resourceTypes): self
    {
        $this->resourceTypes = $resourceTypes;

        return $this;
    }

    /**
     * @param $name
     *
     * @return ResourceType
     */
    public function getResourceTypeByName($name)
    {
        $criteria = Criteria::create()->where(Criteria::expr()->eq('name', $name));

        return $this->getResourceTypes()->matching($criteria)->first();
    }
}
