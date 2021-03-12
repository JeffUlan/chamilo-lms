<?php

declare(strict_types=1);

/* For licensing terms, see /license.txt */

namespace Chamilo\CourseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * CLpItem.
 *
 * @ORM\Table(
 *     name="c_lp_item",
 *     indexes={
 *         @ORM\Index(name="course", columns={"c_id"}),
 *         @ORM\Index(name="lp_id", columns={"lp_id"}),
 *         @ORM\Index(name="idx_c_lp_item_cid_lp_id", columns={"c_id", "lp_id"})
 *     }
 * )
 * @ORM\Entity
 */
class CLpItem
{
    /**
     * @ORM\Column(name="iid", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    protected ?int $iid = null;

    /**
     * @ORM\Column(name="c_id", type="integer")
     */
    protected int $cId;

    /**
     * @ORM\ManyToOne(targetEntity="Chamilo\CourseBundle\Entity\CLp", inversedBy="items")
     * @ORM\JoinColumn(name="lp_id", referencedColumnName="iid")
     */
    protected CLp $lp;

    /**
     * @Assert\NotBlank()
     *
     * @ORM\Column(name="title", type="string", length=511, nullable=false)
     */
    protected string $title;

    /**
     * @Assert\NotBlank()
     *
     * @ORM\Column(name="item_type", type="string", length=32, nullable=false)
     */
    protected string $itemType;

    /**
     * @ORM\Column(name="ref", type="text", nullable=false)
     */
    protected string $ref;

    /**
     * @ORM\Column(name="description", type="string", length=511, nullable=true)
     */
    protected ?string $description;

    /**
     * @ORM\Column(name="path", type="text", nullable=false)
     */
    protected string $path;

    /**
     * @ORM\Column(name="min_score", type="float", precision=10, scale=0, nullable=false)
     */
    protected float $minScore;

    /**
     * @ORM\Column(name="max_score", type="float", precision=10, scale=0, nullable=true, options={"default":"100"})
     */
    protected ?float $maxScore;

    /**
     * @ORM\Column(name="mastery_score", type="float", precision=10, scale=0, nullable=true)
     */
    protected ?float $masteryScore = null;

    /**
     * @ORM\Column(name="parent_item_id", type="integer", nullable=false)
     */
    protected int $parentItemId;

    /**
     * @ORM\Column(name="previous_item_id", type="integer", nullable=false)
     */
    protected int $previousItemId;

    /**
     * @ORM\Column(name="next_item_id", type="integer", nullable=false)
     */
    protected int $nextItemId;

    /**
     * @ORM\Column(name="display_order", type="integer", nullable=false)
     */
    protected int $displayOrder;

    /**
     * @ORM\Column(name="prerequisite", type="text", nullable=true)
     */
    protected ?string $prerequisite = null;

    /**
     * @ORM\Column(name="parameters", type="text", nullable=true)
     */
    protected ?string $parameters = null;

    /**
     * @ORM\Column(name="launch_data", type="text", nullable=false)
     */
    protected string $launchData;

    /**
     * @ORM\Column(name="max_time_allowed", type="string", length=13, nullable=true)
     */
    protected ?string $maxTimeAllowed = null;

    /**
     * @ORM\Column(name="terms", type="text", nullable=true)
     */
    protected ?string $terms = null;

    /**
     * @ORM\Column(name="search_did", type="integer", nullable=true)
     */
    protected ?int $searchDid = null;

    /**
     * @ORM\Column(name="audio", type="string", length=250, nullable=true)
     */
    protected ?string $audio = null;

    /**
     * @ORM\Column(name="prerequisite_min_score", type="float", precision=10, scale=0, nullable=true)
     */
    protected ?float $prerequisiteMinScore = null;

    /**
     * @ORM\Column(name="prerequisite_max_score", type="float", precision=10, scale=0, nullable=true)
     */
    protected ?float $prerequisiteMaxScore = null;

    public function __construct()
    {
        $this->path = '';
        $this->parentItemId = 0;
        $this->previousItemId = 0;
        $this->description = '';
        $this->maxScore = 100.0;
    }

    public function getIid(): ?int
    {
        return $this->iid;
    }

    public function setLp(CLp $lp): self
    {
        $this->lp = $lp;

        return $this;
    }

    public function getLp(): CLp
    {
        return $this->lp;
    }

    /**
     * Set itemType.
     *
     * @return CLpItem
     */
    public function setItemType(string $itemType)
    {
        $this->itemType = $itemType;

        return $this;
    }

    /**
     * Get itemType.
     *
     * @return string
     */
    public function getItemType()
    {
        return $this->itemType;
    }

    /**
     * Set ref.
     *
     * @return CLpItem
     */
    public function setRef(string $ref)
    {
        $this->ref = $ref;

        return $this;
    }

    /**
     * Get ref.
     *
     * @return string
     */
    public function getRef()
    {
        return $this->ref;
    }

    /**
     * Set title.
     *
     * @return CLpItem
     */
    public function setTitle(string $title)
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
     * Set description.
     *
     * @return CLpItem
     */
    public function setDescription(string $description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set path.
     *
     * @return CLpItem
     */
    public function setPath(string $path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set minScore.
     *
     * @return CLpItem
     */
    public function setMinScore(float $minScore)
    {
        $this->minScore = $minScore;

        return $this;
    }

    /**
     * Get minScore.
     *
     * @return float
     */
    public function getMinScore()
    {
        return $this->minScore;
    }

    /**
     * Set maxScore.
     *
     * @return CLpItem
     */
    public function setMaxScore(float $maxScore)
    {
        $this->maxScore = $maxScore;

        return $this;
    }

    /**
     * Get maxScore.
     *
     * @return float
     */
    public function getMaxScore()
    {
        return $this->maxScore;
    }

    /**
     * Set masteryScore.
     *
     * @return CLpItem
     */
    public function setMasteryScore(float $masteryScore)
    {
        $this->masteryScore = $masteryScore;

        return $this;
    }

    /**
     * Get masteryScore.
     *
     * @return float
     */
    public function getMasteryScore()
    {
        return $this->masteryScore;
    }

    /**
     * Set parentItemId.
     *
     * @return CLpItem
     */
    public function setParentItemId(int $parentItemId)
    {
        $this->parentItemId = $parentItemId;

        return $this;
    }

    /**
     * Get parentItemId.
     *
     * @return int
     */
    public function getParentItemId()
    {
        return $this->parentItemId;
    }

    /**
     * Set previousItemId.
     *
     * @return CLpItem
     */
    public function setPreviousItemId(int $previousItemId)
    {
        $this->previousItemId = $previousItemId;

        return $this;
    }

    /**
     * Get previousItemId.
     *
     * @return int
     */
    public function getPreviousItemId()
    {
        return $this->previousItemId;
    }

    /**
     * Set nextItemId.
     *
     * @return CLpItem
     */
    public function setNextItemId(int $nextItemId)
    {
        $this->nextItemId = $nextItemId;

        return $this;
    }

    /**
     * Get nextItemId.
     *
     * @return int
     */
    public function getNextItemId()
    {
        return $this->nextItemId;
    }

    /**
     * Set displayOrder.
     *
     * @return CLpItem
     */
    public function setDisplayOrder(int $displayOrder)
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
     * Set prerequisite.
     *
     * @return CLpItem
     */
    public function setPrerequisite(string $prerequisite)
    {
        $this->prerequisite = $prerequisite;

        return $this;
    }

    /**
     * Get prerequisite.
     *
     * @return string
     */
    public function getPrerequisite()
    {
        return $this->prerequisite;
    }

    /**
     * Set parameters.
     *
     * @return CLpItem
     */
    public function setParameters(string $parameters)
    {
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * Get parameters.
     *
     * @return string
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Set launchData.
     *
     * @return CLpItem
     */
    public function setLaunchData(string $launchData)
    {
        $this->launchData = $launchData;

        return $this;
    }

    /**
     * Get launchData.
     *
     * @return string
     */
    public function getLaunchData()
    {
        return $this->launchData;
    }

    /**
     * Set maxTimeAllowed.
     *
     * @return CLpItem
     */
    public function setMaxTimeAllowed(string $maxTimeAllowed)
    {
        $this->maxTimeAllowed = $maxTimeAllowed;

        return $this;
    }

    /**
     * Get maxTimeAllowed.
     *
     * @return string
     */
    public function getMaxTimeAllowed()
    {
        return $this->maxTimeAllowed;
    }

    /**
     * Set terms.
     *
     * @return CLpItem
     */
    public function setTerms(string $terms)
    {
        $this->terms = $terms;

        return $this;
    }

    /**
     * Get terms.
     *
     * @return string
     */
    public function getTerms()
    {
        return $this->terms;
    }

    /**
     * Set searchDid.
     *
     * @return CLpItem
     */
    public function setSearchDid(int $searchDid)
    {
        $this->searchDid = $searchDid;

        return $this;
    }

    /**
     * Get searchDid.
     *
     * @return int
     */
    public function getSearchDid()
    {
        return $this->searchDid;
    }

    /**
     * Set audio.
     *
     * @return CLpItem
     */
    public function setAudio(string $audio)
    {
        $this->audio = $audio;

        return $this;
    }

    /**
     * Get audio.
     *
     * @return string
     */
    public function getAudio()
    {
        return $this->audio;
    }

    /**
     * Set prerequisiteMinScore.
     *
     * @return CLpItem
     */
    public function setPrerequisiteMinScore(float $prerequisiteMinScore)
    {
        $this->prerequisiteMinScore = $prerequisiteMinScore;

        return $this;
    }

    /**
     * Get prerequisiteMinScore.
     *
     * @return float
     */
    public function getPrerequisiteMinScore()
    {
        return $this->prerequisiteMinScore;
    }

    /**
     * Set prerequisiteMaxScore.
     *
     * @return CLpItem
     */
    public function setPrerequisiteMaxScore(float $prerequisiteMaxScore)
    {
        $this->prerequisiteMaxScore = $prerequisiteMaxScore;

        return $this;
    }

    /**
     * Get prerequisiteMaxScore.
     *
     * @return float
     */
    public function getPrerequisiteMaxScore()
    {
        return $this->prerequisiteMaxScore;
    }

    /**
     * Set cId.
     *
     * @return CLpItem
     */
    public function setCId(int $cId)
    {
        $this->cId = $cId;

        return $this;
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
}
