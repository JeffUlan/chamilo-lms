<?php
declare(strict_types=1);

/* For licensing terms, see /license.txt */

namespace Chamilo\CourseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="c_plagiarism_compilatio_docs")
 * @ORM\Entity
 */
class CPlagiarismCompilatioDocs
{

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    protected int $id;

    /**
     * @ORM\Column(name="c_id", type="integer", nullable=false)
     */
    protected int $cId;

    /**
     * @ORM\Column(name="document_id", type="integer", nullable=false)
     */
    protected int $documentId;

    /**
     * @ORM\Column(name="compilatio_id", type="string", length=32, nullable=true)
     */
    protected ?string $compilatioId = null;

    public function __construct()
    {

    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return int
     */
    public function getCId(): int
    {
        return $this->cId;
    }

    /**
     * @param int $cId
     */
    public function setCId(int $cId): self
    {
        $this->cId = $cId;

        return $this;
    }

    /**
     * @return int
     */
    public function getDocumentId(): int
    {
        return $this->documentId;
    }

    /**
     * @param int $documentId
     */
    public function setDocumentId(int $documentId): self
    {
        $this->documentId = $documentId;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCompilatioId(): ?string
    {
        return $this->compilatioId;
    }

    /**
     * @param string|null $compilatioId
     */
    public function setCompilatioId(?string $compilatioId): self
    {
        $this->compilatioId = $compilatioId;

        return $this;
    }
}
