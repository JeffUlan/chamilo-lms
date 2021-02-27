<?php

declare(strict_types=1);

/* For licensing terms, see /license.txt */

namespace Chamilo\CourseBundle\Entity;

use Chamilo\CoreBundle\Entity\AbstractResource;
use Chamilo\CoreBundle\Entity\ResourceInterface;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * CAnnouncement.
 *
 * @ORM\Table(name="c_announcement")
 * @ORM\Entity
 */
class CAnnouncement extends AbstractResource implements ResourceInterface
{
    /**
     * @ORM\Column(name="iid", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    protected int $iid;

    /**
     * @Assert\NotBlank()
     *
     * @ORM\Column(name="title", type="text", nullable=false)
     */
    protected string $title;

    /**
     * @ORM\Column(name="content", type="text", nullable=true)
     */
    protected ?string $content;

    /**
     * @ORM\Column(name="end_date", type="date", nullable=true)
     */
    protected ?DateTime $endDate;

    /**
     * @ORM\Column(name="display_order", type="integer", nullable=false)
     */
    protected int $displayOrder;

    /**
     * @ORM\Column(name="email_sent", type="boolean", nullable=true)
     */
    protected ?bool $emailSent;

    /**
     * @var Collection<int, CAnnouncementAttachment>
     *
     * @ORM\OneToMany(
     *     targetEntity="CAnnouncementAttachment",
     *     mappedBy="announcement", cascade={"persist", "remove"}, orphanRemoval=true
     * )
     */
    protected Collection $attachments;

    public function __construct()
    {
        $this->content = '';
        $this->attachments = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->getTitle();
    }

    public function getAttachments(): ArrayCollection
    {
        return $this->attachments;
    }

    public function setAttachments(ArrayCollection $attachments): self
    {
        $this->attachments = $attachments;

        return $this;
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

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setEndDate(?DateTime $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getEndDate(): ?DateTime
    {
        return $this->endDate;
    }

    /**
     * Set displayOrder.
     *
     * @param int $displayOrder
     */
    public function setDisplayOrder($displayOrder): self
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
     * Set emailSent.
     *
     * @param bool $emailSent
     */
    public function setEmailSent($emailSent): self
    {
        $this->emailSent = $emailSent;

        return $this;
    }

    /**
     * Get emailSent.
     *
     * @return bool
     */
    public function getEmailSent()
    {
        return $this->emailSent;
    }

    public function getIid(): int
    {
        return $this->iid;
    }

    public function getResourceIdentifier(): int
    {
        return $this->getIid();
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
