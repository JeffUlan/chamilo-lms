<?php

/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Entity;

use Chamilo\CoreBundle\Traits\UserTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(
 *     name="gradebook_certificate",
 *     indexes={
 *      @ORM\Index(name="idx_gradebook_certificate_category_id", columns={"cat_id"}),
 *      @ORM\Index(name="idx_gradebook_certificate_user_id", columns={"user_id"}),
 *      @ORM\Index(name="idx_gradebook_certificate_category_id_user_id", columns={"cat_id", "user_id"})}
 * )
 * @ORM\Entity
 */
class GradebookCertificate
{
    use UserTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @var int
     *
     * @ORM\Column(name="cat_id", type="integer", nullable=false)
     */
    protected $catId;

    /**
     * @ORM\ManyToOne(targetEntity="Chamilo\CoreBundle\Entity\User", inversedBy="gradeBookCertificates")
     * @ORM\JoinColumn(name="user_id",referencedColumnName="id",onDelete="CASCADE")
     */
    protected User $user;

    /**
     * @var float
     *
     * @ORM\Column(name="score_certificate", type="float", precision=10, scale=0, nullable=false)
     */
    protected $scoreCertificate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    protected $createdAt;

    /**
     * @var string
     *
     * @ORM\Column(name="path_certificate", type="text", nullable=true)
     */
    protected $pathCertificate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="downloaded_at", type="datetime", nullable=true)
     */
    protected $downloadedAt;

    /**
     * Set catId.
     *
     * @param int $catId
     *
     * @return GradebookCertificate
     */
    public function setCatId($catId)
    {
        $this->catId = $catId;

        return $this;
    }

    /**
     * Get catId.
     *
     * @return int
     */
    public function getCatId()
    {
        return $this->catId;
    }

    /**
     * Set scoreCertificate.
     *
     * @param float $scoreCertificate
     *
     * @return GradebookCertificate
     */
    public function setScoreCertificate($scoreCertificate)
    {
        $this->scoreCertificate = $scoreCertificate;

        return $this;
    }

    /**
     * Get scoreCertificate.
     *
     * @return float
     */
    public function getScoreCertificate()
    {
        return $this->scoreCertificate;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return GradebookCertificate
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt.
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set pathCertificate.
     *
     * @param string $pathCertificate
     *
     * @return GradebookCertificate
     */
    public function setPathCertificate($pathCertificate)
    {
        $this->pathCertificate = $pathCertificate;

        return $this;
    }

    /**
     * Get pathCertificate.
     *
     * @return string
     */
    public function getPathCertificate()
    {
        return $this->pathCertificate;
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

    public function getDownloadedAt(): \DateTime
    {
        return $this->downloadedAt;
    }

    public function setDownloadedAt(\DateTime $downloadedAt): self
    {
        $this->downloadedAt = $downloadedAt;

        return $this;
    }
}
