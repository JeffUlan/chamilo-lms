<?php

/* For licensing terms, see /license.txt */

namespace Chamilo\CourseBundle\Entity;

use Chamilo\CoreBundle\Entity\Resource\AbstractResource;
use Chamilo\CoreBundle\Entity\Resource\ResourceInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * CThematic.
 *
 * @ORM\Table(
 *  name="c_thematic",
 *  indexes={
 *      @ORM\Index(name="course", columns={"c_id"}),
 *      @ORM\Index(name="active", columns={"active", "session_id"})
 *  }
 * )
 * @ORM\Entity
 */
class CThematic extends AbstractResource implements ResourceInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="iid", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    protected $iid;

    /**
     * @var int
     *
     * @ORM\Column(name="c_id", type="integer")
     */
    protected $cId;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=true)
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="text", nullable=false)
     */
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text", nullable=true)
     */
    protected $content;

    /**
     * @var int
     *
     * @ORM\Column(name="display_order", type="integer", nullable=false)
     */
    protected $displayOrder;

    /**
     * @var bool
     *
     * @ORM\Column(name="active", type="boolean", nullable=false)
     */
    protected $active;

    /**
     * @var int
     *
     * @ORM\Column(name="session_id", type="integer", nullable=false)
     */
    protected $sessionId;

    /**
     * @var CThematicPlan[]
     *
     * @ORM\OneToMany(targetEntity="Chamilo\CourseBundle\Entity\CThematicPlan", mappedBy="thematic", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    protected $plans;

    /**
     * @var CThematicAdvance[]
     *
     * @ORM\OrderBy({"startDate" = "ASC"})
     *
     * @ORM\OneToMany(targetEntity="Chamilo\CourseBundle\Entity\CThematicAdvance", mappedBy="thematic", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    protected $advances;

    public function __construct()
    {
        $this->id = 0;
        $this->plans = new ArrayCollection();
        $this->advances = new ArrayCollection();
    }

    /**
     * Set title.
     *
     * @param string $title
     *
     * @return CThematic
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set content.
     *
     * @param string $content
     *
     * @return CThematic
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content.
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set displayOrder.
     *
     * @param int $displayOrder
     *
     * @return CThematic
     */
    public function setDisplayOrder($displayOrder)
    {
        $this->displayOrder = $displayOrder;

        return $this;
    }

    /**
     * Get displayOrder.
     *
     * @return int
     */
    public function getDisplayOrder()
    {
        return $this->displayOrder;
    }

    /**
     * Set active.
     *
     * @param bool $active
     *
     * @return CThematic
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active.
     *
     * @return bool
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set sessionId.
     *
     * @param int $sessionId
     *
     * @return CThematic
     */
    public function setSessionId($sessionId)
    {
        $this->sessionId = $sessionId;

        return $this;
    }

    /**
     * Get sessionId.
     *
     * @return int
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }

    /**
     * Set id.
     *
     * @param int $id
     *
     * @return CThematic
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set cId.
     *
     * @param int $cId
     *
     * @return CThematic
     */
    public function setCId($cId)
    {
        $this->cId = $cId;

        return $this;
    }

    public function getIid(): int
    {
        return $this->iid;
    }

    /**
     * Get cId.
     *
     * @return int
     */
    public function getCId()
    {
        return $this->cId;
    }

    /**
     * @return CThematicPlan[]|ArrayCollection
     */
    public function getPlans()
    {
        return $this->plans;
    }

    /**
     * @return CThematicAdvance[]|ArrayCollection
     */
    public function getAdvances()
    {
        return $this->advances;
    }

    public function __toString(): string
    {
        return $this->getTitle();
    }

    /**
     * Resource identifier.
     */
    public function getResourceIdentifier(): int
    {
        return $this->getIid();
    }

    public function getResourceName(): string
    {
        return $this->getTitle();
    }
}
