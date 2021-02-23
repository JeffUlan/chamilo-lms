<?php

/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
use Chamilo\CoreBundle\Controller\CreateResourceFileAction;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

//
//*     attributes={"security"="is_granted('ROLE_ADMIN')"},
/**
 * @ApiResource(
 *     iri="http://schema.org/MediaObject",
 *     normalizationContext={
 *      "groups"={"resource_file:read", "resource_node:read", "document:read", "media_object_read"}
 *     },
 *     collectionOperations={
 *         "post"={
 *             "controller"=CreateResourceFileAction::class,
 *             "deserialize"=false,
 *             "security"="is_granted('ROLE_USER')",
 *             "validation_groups"={"Default", "media_object_create", "document:write"},
 *             "openapi_context"={
 *                 "requestBody"={
 *                     "content"={
 *                         "multipart/form-data"={
 *                             "schema"={
 *                                 "type"="object",
 *                                 "properties"={
 *                                     "file"={
 *                                         "type"="string",
 *                                         "format"="binary"
 *                                     }
 *                                 }
 *                             }
 *                         }
 *                     }
 *                 }
 *             }
 *         },
 *         "get"
 *     },
 *     itemOperations={
 *         "get"
 *     }
 * )
 * @ApiFilter(SearchFilter::class, properties={"name": "partial"})
 * @ApiFilter(PropertyFilter::class)
 * @ApiFilter(OrderFilter::class, properties={"id", "name", "size", "updatedAt"})
 * @ORM\Entity
 * @Vich\Uploadable
 *
 * @ORM\Table(name="resource_file")
 */
class ResourceFile
{
    use TimestampableEntity;

    /**
     * @Groups({"resource_file:read", "resource_node:read", "document:read"})
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @Assert\NotBlank()
     * @Groups({"resource_file:read", "resource_node:read", "document:read"})
     *
     * @ORM\Column(type="string", length=255)
     */
    protected $name;

    /**
     * @Groups({"resource_file:read", "resource_node:read", "document:read"})
     * @ORM\Column(type="text", nullable=true)
     */
    protected ?string $mimeType;

    /**
     * @Groups({"resource_file:read", "resource_node:read", "document:read"})
     * @ORM\Column(type="text", nullable=true)
     */
    protected ?string $originalName;

    /**
     * @var array
     *
     * @Groups({"resource_file:read", "resource_node:read", "document:read"})
     * @ORM\Column(type="simple_array", nullable=true)
     */
    protected $dimensions;

    /**
     * @var int
     *
     * @Groups({"resource_file:read", "resource_node:read", "document:read"})
     *
     * @ORM\Column(type="integer")
     */
    protected $size;

    /**
     * @var File
     *
     * @Assert\NotNull()
     * @Vich\UploadableField(
     *     mapping="resources",
     *     fileNameProperty="name",
     *     size="size",
     *     mimeType="mimeType",
     *     originalName="originalName",
     *     dimensions="dimensions"
     * )
     */
    protected $file;

    /**
     * @ORM\Column(name="crop", type="string", length=255, nullable=true)
     */
    protected ?string $crop;

    /**
     * @ORM\OneToOne(targetEntity="Chamilo\CoreBundle\Entity\ResourceNode", mappedBy="resourceFile")
     */
    protected ResourceNode $resourceNode;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    protected ?array $metadata;

    /**
     * @Groups({"resource_file:read", "resource_node:read", "document:read"})
     */
    protected ?bool $image;

    /**
     * @Groups({"resource_file:read", "resource_node:read", "document:read"})
     */
    protected ?bool $video;

    /**
     * @Groups({"resource_file:read", "resource_node:read", "document:read"})
     */
    protected ?bool $text;

    /**
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected ?string $description;

    /**
     * @var \DateTime|\DateTimeImmutable
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    protected $updatedAt;

    public function __construct()
    {
        $this->size = 0;
        $this->metadata = [];
        $this->dimensions = [];
    }

    public function __toString(): string
    {
        return $this->getOriginalName();
    }

    public function isText(): bool
    {
        $mimeType = $this->getMimeType();
        if (false !== strpos($mimeType, 'text')) {
            return true;
        }

        return false;
    }

    public function isImage(): bool
    {
        $mimeType = $this->getMimeType();
        if (false !== strpos($mimeType, 'image')) {
            return true;
        }

        return false;
    }

    public function isVideo(): bool
    {
        $mimeType = $this->getMimeType();
        if (false !== strpos($mimeType, 'video')) {
            return true;
        }

        return false;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName($name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getCrop()
    {
        return $this->crop;
    }

    /**
     * @param string $crop
     *
     * @return $this
     */
    public function setCrop($crop): self
    {
        $this->crop = $crop;

        return $this;
    }

    public function getSize(): int
    {
        return (int) $this->size;
    }

    /**
     * @param int $size
     */
    public function setSize($size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getResourceNode(): ResourceNode
    {
        return $this->resourceNode;
    }

    public function setResourceNode(ResourceNode $resourceNode): self
    {
        $this->resourceNode = $resourceNode;

        return $this;
    }

    /*public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }*/

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /*public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }*/

    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    /**
     * @param string $mimeType
     */
    public function setMimeType($mimeType): self
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    public function getOriginalName(): string
    {
        return $this->originalName;
    }

    /**
     * @param string $originalName
     */
    public function setOriginalName($originalName): self
    {
        $this->originalName = $originalName;

        return $this;
    }

    public function getDimensions(): array
    {
        return $this->dimensions;
    }

    public function setDimensions($dimensions): self
    {
        $this->dimensions = $dimensions;

        return $this;
    }

    public function getWidth(): int
    {
        $data = $this->getDimensions();
        if ($data) {
            //$data = explode(',', $data);

            return (int) $data[0];
        }

        return 0;
    }

    public function getHeight(): int
    {
        $data = $this->getDimensions();

        if ($data) {
            //$data = explode(',', $data);

            return (int) $data[1];
        }

        return 0;
    }

    public function getMetadata(): array
    {
        return $this->metadata;
    }

    public function setMetadata(array $metadata): self
    {
        $this->metadata = $metadata;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

    /**
     * @param File|UploadedFile $file
     */
    public function setFile(File $file = null): self
    {
        $this->file = $file;

        if (null !== $file) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }

        return $this;
    }
}
