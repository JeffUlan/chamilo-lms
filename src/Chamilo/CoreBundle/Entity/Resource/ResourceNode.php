<?php
/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Entity\Resource;

use Chamilo\CoreBundle\Entity\Tool;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Chamilo\UserBundle\Entity\User;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Base entity for all resources.
 * @ORM\Entity(repositoryClass="Gedmo\Tree\Entity\Repository\MaterializedPathRepository")
 * @ORM\Table(name="resource_node")
 * @Gedmo\Tree(type="materializedPath")
 *
 */
class ResourceNode
{
    const PATH_SEPARATOR = '`';

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Chamilo\CoreBundle\Entity\Tool")
     * @ORM\JoinColumn(name="tool_id", referencedColumnName="id")
     **/
    protected $tool;

    /**
     * @ORM\OneToMany(targetEntity="Chamilo\CoreBundle\Entity\Resource\ResourceLink", mappedBy="resourceNode", cascade={"remove"})
     **/
    protected $links;

    /**
     * @ORM\ManyToOne(targetEntity="Chamilo\CoreBundle\Entity\Resource\AbstractResource", inversedBy="resourceNodes", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="resource_id", referencedColumnName="id")
     **/
    //protected $resource;

    /**
     * @ORM\ManyToOne(
     *     targetEntity="Chamilo\UserBundle\Entity\User",
     *     inversedBy="resourceNodes",
     *     cascade={"persist"}
     * )
     * @ORM\JoinColumn(onDelete="CASCADE", nullable=false)
     */
    protected $creator;

    /**
     * @Gedmo\TreePathSource
     * @ORM\Column()
     * @Assert\NotBlank()
     */
    protected $name;

    /**
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(
     *     targetEntity="Chamilo\CoreBundle\Entity\Resource\ResourceNode",
     *     inversedBy="children"
     * )
     * @ORM\JoinColumns({@ORM\JoinColumn(onDelete="CASCADE")})
     */
    protected $parent;

    /**
     * @Gedmo\TreeLevel
     * @ORM\Column(name="level", type="integer", nullable=true)
     *
     */
    protected $level;

    /**
     * @ORM\OneToMany(
     *     targetEntity="Chamilo\CoreBundle\Entity\Resource\ResourceNode",
     *     mappedBy="parent"
     * )
     * @ORM\OrderBy({"id" = "ASC"})
     */
    protected $children;

    /**
     * @Gedmo\TreePath(separator="`")
     * @ORM\Column(name="path", type="string", length=3000, nullable=true)
     *
     */
    protected $path;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    protected $createdAt;

    /**
     * @ORM\Column(name="updated_at", type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    protected $updatedAt;

    //private $pathForCreationLog = '';

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->rights = new ArrayCollection();
        $this->children = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->getPath();
    }

    /**
     * Returns the resource id.
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setUpdatedAt(\DateTime $updatedAt = null)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt(\DateTime $createdAt = null)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Returns the tool.
     *
     * @return Tool
     */
    public function getTool()
    {
        return $this->tool;
    }

    /**
     * Returns the resource type.
     * @param Tool $tool
     *
     * @return $this
     */
    public function setTool(Tool $tool)
    {
        $this->tool = $tool;

        return $this;
    }

    /**
     * Returns the resource creator.
     *
     * @return User
     */
    public function getCreator()
    {
        return $this->creator;
    }

    /**
     * Sets the resource creator.
     *
     * @param User $creator
     *
     * @return $this
     */
    public function setCreator(User $creator)
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
     * @return AbstractResource
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Return the lvl value of the resource in the tree.
     *
     * @return integer
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Returns the "raw" path of the resource
     * (the path merge names and ids of all items).
     * Eg.: "Root-1/subdir-2/file.txt-3/"
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Returns the path cleaned from its ids.
     * Eg.: "Root/subdir/file.txt"
     * @return
     */
    public function getPathForDisplay()
    {
        return self::convertPathForDisplay($this->path);
    }

    /**
     * Sets the resource name.
     *
     * @param  string $name
     * @throws an     exception if the name contains the path separator ('/').
     *
     * @return $this
     */
    public function setName($name)
    {
        if (strpos(self::PATH_SEPARATOR, $name) !== false) {
            throw new \InvalidArgumentException(
                'Invalid character "'.self::PATH_SEPARATOR.'" in resource name.'
            );
        }

        $this->name = $name;

        return $this;
    }

    /**
     * Returns the resource name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
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
        $pathForDisplay = preg_replace(
            '/-\d+'.self::PATH_SEPARATOR.'/',
            ' / ',
            $path
        );

        if ($pathForDisplay !== null && strlen($pathForDisplay) > 0) {
            $pathForDisplay = substr_replace($pathForDisplay, "", -3);
        }

        return $pathForDisplay;
    }


    /**
     * This is required for logging the resource path at the creation.
     * Do not use this function otherwise.
     *
     * @return type
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
     * @param $resource
     * @return $this
     */
    public function setResource($resource)
    {
        $this->resource = $resource;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getLinks()
    {
        return $this->links;
    }
}
