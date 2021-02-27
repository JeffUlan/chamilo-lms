<?php

declare(strict_types=1);

/* For licensing terms, see /license.txt */

namespace Chamilo\CourseBundle\Entity;

use Chamilo\CoreBundle\Entity\AbstractResource;
use Chamilo\CoreBundle\Entity\ResourceInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * CLink.
 *
 * @ORM\Table(name="c_link")
 * @ORM\Entity
 */
class CLink extends AbstractResource implements ResourceInterface
{
    /**
     * @ORM\Column(name="iid", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    protected ?int $iid;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(name="url", type="text", nullable=false)
     */
    protected string $url;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(name="title", type="string", length=255, nullable=false)
     */
    protected string $title;

    /**
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected ?string $description;

    /**
     * @ORM\ManyToOne(targetEntity="Chamilo\CourseBundle\Entity\CLinkCategory", inversedBy="links")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="iid")
     */
    protected ?CLinkCategory $category = null;

    /**
     * @ORM\Column(name="display_order", type="integer", nullable=false)
     */
    protected int $displayOrder;

    /**
     * @ORM\Column(name="on_homepage", type="string", length=10, nullable=false)
     */
    protected string $onHomepage;

    /**
     * @ORM\Column(name="target", type="string", length=10, nullable=true)
     */
    protected ?string $target;

    public function __construct()
    {
        $this->displayOrder = 0;
        $this->description = '';
    }

    public function __toString(): string
    {
        return $this->getTitle();
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Set description.
     *
     * @return CLink
     */
    public function setDescription(string $description)
    {
        $this->description = $description;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Set displayOrder.
     *
     * @param int $displayOrder
     *
     * @return CLink
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
     * Set onHomepage.
     *
     * @param string $onHomepage
     *
     * @return CLink
     */
    public function setOnHomepage($onHomepage)
    {
        $this->onHomepage = $onHomepage;

        return $this;
    }

    /**
     * Get onHomepage.
     *
     * @return string
     */
    public function getOnHomepage()
    {
        return $this->onHomepage;
    }

    /**
     * Set target.
     *
     * @param string $target
     *
     * @return CLink
     */
    public function setTarget($target)
    {
        $this->target = $target;

        return $this;
    }

    /**
     * Get target.
     *
     * @return string
     */
    public function getTarget()
    {
        return $this->target;
    }

    public function getIid(): int
    {
        return $this->iid;
    }

    public function getCategory(): ?CLinkCategory
    {
        return $this->category;
    }

    public function setCategory(?CLinkCategory $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getResourceIdentifier(): int
    {
        return $this->iid;
    }

    public function getResourceName(): string
    {
        return $this->getTitle();
    }

    public function setResourceName(string $name): self
    {
        return $this->setTitle($name);
    }
}
