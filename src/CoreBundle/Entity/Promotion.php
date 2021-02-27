<?php

declare(strict_types=1);

/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Promotion.
 *
 * @ORM\Table(name="promotion")
 * @ORM\Entity
 */
class Promotion
{
    use TimestampableEntity;

    public const PROMOTION_STATUS_ACTIVE = 1;
    public const PROMOTION_STATUS_INACTIVE = 0;

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue()
     */
    protected int $id;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    protected string $name;

    /**
     * @ORM\Column(name="description", type="text", nullable=false)
     */
    protected ?string $description;

    /**
     * @ORM\ManyToOne(targetEntity="Chamilo\CoreBundle\Entity\Career")
     * @ORM\JoinColumn(name="career_id", referencedColumnName="id")
     */
    protected Career $career;

    /**
     * @ORM\Column(name="status", type="integer", nullable=false)
     */
    protected int $status;

    public function __construct()
    {
        $this->status = self::PROMOTION_STATUS_ACTIVE;
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
     * Set name.
     *
     * @param string $name
     *
     * @return Promotion
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description.
     *
     * @param string $description
     */
    public function setDescription($description): self
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

    public function setCareer(Career $career): self
    {
        $this->career = $career;

        return $this;
    }

    /**
     * Get career.
     *
     * @return Career
     */
    public function getCareer()
    {
        return $this->career;
    }

    /**
     * Set status.
     *
     * @param int $status
     */
    public function setStatus($status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status.
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }
}
