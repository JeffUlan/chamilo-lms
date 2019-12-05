<?php
/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Entity\Resource;

use Chamilo\CoreBundle\Entity\Session;
use Chamilo\UserBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Base entity for all resources.
 *
 * @ORM\Entity(repositoryClass="Chamilo\CoreBundle\Repository\ResourceNodeRepository")
 *
 * @ORM\Table(name="resource_node")
 *
 * @Gedmo\Tree(type="materializedPath")
 */
class ResourceNode
{
    use TimestampableEntity;

    public const PATH_SEPARATOR = '`';

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @Assert\NotBlank()
     *
     * @Gedmo\TreePathSource
     * @ORM\Column(name="slug", type="string", length=255, nullable=true)
     */
    protected $slug;

    /**
     * @ORM\ManyToOne(targetEntity="Chamilo\CoreBundle\Entity\Resource\ResourceType", inversedBy="resourceNodes")
     * @ORM\JoinColumn(name="resource_type_id", referencedColumnName="id", nullable=false)
     */
    protected $resourceType;

    /**
     * @ORM\OneToMany(targetEntity="Chamilo\CoreBundle\Entity\Resource\ResourceLink", mappedBy="resourceNode",
     *                                                                                cascade={"remove"})
     */
    protected $resourceLinks;

    /**
     * @var ResourceFile
     *
     * @ORM\OneToOne(targetEntity="Chamilo\CoreBundle\Entity\Resource\ResourceFile", inversedBy="resourceNode", orphanRemoval=true)
     * @ORM\JoinColumn(name="resource_file_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $resourceFile;

    /**
     * @ORM\ManyToOne(
     *     targetEntity="Chamilo\UserBundle\Entity\User", inversedBy="resourceNodes"
     * )
     * @ORM\JoinColumn(name="creator_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $creator;

    /**
     * @Gedmo\TreeParent
     *
     * @ORM\ManyToOne(
     *     targetEntity="Chamilo\CoreBundle\Entity\Resource\ResourceNode",
     *     inversedBy="children"
     * )
     * @ORM\JoinColumns({@ORM\JoinColumn(onDelete="CASCADE")})
     */
    protected $parent;

    /**
     * @Gedmo\TreeLevel
     *
     * @ORM\Column(name="level", type="integer", nullable=true)
     */
    protected $level;

    /**
     * @var ResourceNode[]
     *
     * @ORM\OneToMany(
     *     targetEntity="Chamilo\CoreBundle\Entity\Resource\ResourceNode",
     *     mappedBy="parent"
     * )
     * @ORM\OrderBy({"id" = "ASC"})
     */
    protected $children;

    /**
     * @Gedmo\TreePath(separator="`")
     *
     * @ORM\Column(name="path", type="string", length=3000, nullable=true)
     */
    protected $path;

    //protected $pathForCreationLog = '';

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->children = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getPathForDisplay();
    }

    /**
     * Returns the resource id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Returns the resource creator.
     *
     * @return User
     */
    public function getCreator(): ?User
    {
        return $this->creator;
    }

    public function setCreator(User $creator = null)
    {
        $this->creator = $creator;

        return $this;
    }

    /**
     * Returns the children resource instances.
     *
     * @return ArrayCollection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Sets the parent resource.
     *
     * @param ResourceNode $parent
     *
     * @return $this
     */
    public function setParent(ResourceNode $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Returns the parent resource.
     *
     * @return ResourceNode
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Return the lvl value of the resource in the tree.
     *
     * @return int
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Returns the "raw" path of the resource
     * (the path merge names and ids of all items).
     * Eg.: "Root-1/subdir-2/file.txt-3/".
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Returns the path cleaned from its ids.
     * Eg.: "Root/subdir/file.txt".
     *
     * @return string
     */
    public function getPathForDisplay()
    {
        return self::convertPathForDisplay($this->path);
    }

    /**
     * @return string
     */
    public function getPathForDisplayRemoveBase(string $base)
    {
        $path = str_replace($base, '', $this->path);

        return self::convertPathForDisplay($path);
    }

    /**
     * @return mixed
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param mixed $slug
     *
     * @return ResourceNode
     */
    public function setSlug($slug)
    {
        if (strpos(self::PATH_SEPARATOR, $slug) !== false) {
            throw new \InvalidArgumentException('Invalid character "'.self::PATH_SEPARATOR.'" in resource name.');
        }

        $this->slug = $slug;

        return $this;
    }

    /**
     * Convert a path for display: remove ids.
     *
     * @param string $path
     *
     * @return string
     */
    public static function convertPathForDisplay($path)
    {
        /*$pathForDisplay = preg_replace(
            '/-\d+'.self::PATH_SEPARATOR.'/',
            ' / ',
            $path
        );
        if ($pathForDisplay !== null && strlen($pathForDisplay) > 0) {
            $pathForDisplay = substr_replace($pathForDisplay, '', -3);
        }
        */
        $pathForDisplay = preg_replace(
            '/-\d+'.self::PATH_SEPARATOR.'/',
            '/',
            $path
        );

        if ($pathForDisplay !== null && strlen($pathForDisplay) > 0) {
            $pathForDisplay = substr_replace($pathForDisplay, '', -1);
        }

        return $pathForDisplay;
    }

    /**
     * This is required for logging the resource path at the creation.
     * Do not use this function otherwise.
     */
    public function setPathForCreationLog($path)
    {
        $this->pathForCreationLog = $path;
    }

    /**
     * This is required for logging the resource path at the creation.
     * Do not use this function otherwise.
     *
     * @return type
     */
    public function getPathForCreationLog()
    {
        return $this->pathForCreationLog;
    }

    /**
     * @return ResourceType
     */
    public function getResourceType()
    {
        return $this->resourceType;
    }

    /**
     * @param ResourceType $resourceType
     *
     * @return ResourceNode
     */
    public function setResourceType($resourceType)
    {
        $this->resourceType = $resourceType;

        return $this;
    }

    /**
     * @return ArrayCollection|ResourceLink[]
     */
    public function getResourceLinks()
    {
        return $this->resourceLinks;
    }

    /**
     * @param mixed $resourceLinks
     *
     * @return ResourceNode
     */
    public function setResourceLinks($resourceLinks)
    {
        $this->resourceLinks = $resourceLinks;

        return $this;
    }

    /**
     * @param Session $session
     *
     * @return ArrayCollection
     */
    public function hasSession(Session $session = null)
    {
        $links = $this->getResourceLinks();
        $criteria = Criteria::create();

        $criteria->andWhere(
            Criteria::expr()->eq('session', $session)
        );

        $result = $links->matching($criteria);

        return $result;
    }

    /**
     * @return bool
     */
    public function hasResourceFile()
    {
        return $this->resourceFile !== null;
    }

    /**
     * @return ResourceFile
     */
    public function getResourceFile(): ?ResourceFile
    {
        return $this->resourceFile;
    }

    /**
     * @return bool
     */
    public function hasEditableContent()
    {
        if ($this->hasResourceFile()) {
            $mimeType = $this->getResourceFile()->getMimeType();
            if (strpos($mimeType, 'text') !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isResourceFileAnImage()
    {
        if ($this->hasResourceFile()) {
            $mimeType = $this->getResourceFile()->getMimeType();
            if (strpos($mimeType, 'image') !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isResourceFileAVideo()
    {
        if ($this->hasResourceFile()) {
            $mimeType = $this->getResourceFile()->getMimeType();
            if (strpos($mimeType, 'video') !== false) {
                return true;
            }
        }

        return false;
    }

    public function setResourceFile(ResourceFile $resourceFile): ResourceNode
    {
        $this->resourceFile = $resourceFile;

        return $this;
    }

    /**
     * @return string
     */
    public function getIcon()
    {
        $class = 'fa fa-folder';
        if ($this->hasResourceFile()) {
            $class = 'far fa-file';

            if ($this->isResourceFileAnImage()) {
                $class = 'far fa-file-image';
            }
            if ($this->isResourceFileAVideo()) {
                $class = 'far fa-file-video';
            }
        }

        return '<i class="'.$class.'"></i>';
    }
}
