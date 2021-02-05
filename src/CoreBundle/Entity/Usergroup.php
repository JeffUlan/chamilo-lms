<?php

/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     attributes={"security"="is_granted('ROLE_ADMIN')"},
 *     normalizationContext={"groups"={"usergroup:read"}}
 * )
 *
 * @ORM\Table(name="usergroup")
 * @ORM\Entity
 */
class Usergroup extends AbstractResource implements ResourceInterface, ResourceIllustrationInterface, ResourceToRootInterface
{
    use TimestampableEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @Assert\NotBlank()
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false, unique=false)
     */
    protected string $name;

    /**
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected ?string $description;

    /**
     * @var int
     *
     * @ORM\Column(name="group_type", type="integer", nullable=false)
     */
    protected $groupType;

    /**
     * @var string
     *
     * @ORM\Column(name="picture", type="string", length=255, nullable=true)
     */
    protected ?string $picture;

    /**
     * @ORM\Column(name="url", type="string", length=255, nullable=true)
     */
    protected ?string $url;

    /**
     * @var string
     *
     * @ORM\Column(name="visibility", type="string", length=255, nullable=false)
     */
    protected $visibility;

    /**
     * @var string
     *
     * @ORM\Column(name="author_id", type="integer", nullable=true)
     */
    protected $authorId;

    /**
     * @var int
     *
     * @ORM\Column(name="allow_members_leave_group", type="integer")
     */
    protected $allowMembersToLeaveGroup;

    /**
     * @var UsergroupRelUser[]
     * @ORM\OneToMany(targetEntity="UsergroupRelUser", mappedBy="usergroup", cascade={"persist"}, orphanRemoval=true)
     */
    protected $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->getName();
    }

    /**
     * @return UsergroupRelUser[]
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @param $users
     */
    public function setUsers($users)
    {
        $this->users = new ArrayCollection();

        foreach ($users as $user) {
            $this->addUsers($user);
        }
    }

    public function addUsers(UsergroupRelUser $user)
    {
        $user->setUsergroup($this);
        $this->users[] = $user;
    }

    /**
     * Remove $user.
     */
    public function removeUsers(UsergroupRelUser $user)
    {
        foreach ($this->users as $key => $value) {
            if ($value->getId() == $user->getId()) {
                unset($this->users[$key]);
            }
        }
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
     */
    public function setName(string $name): self
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

    /**
     * @return int
     */
    public function getGroupType()
    {
        return $this->groupType;
    }

    /**
     * @param int $groupType
     *
     * @return Usergroup
     */
    public function setGroupType($groupType)
    {
        $this->groupType = $groupType;

        return $this;
    }

    public function getVisibility(): string
    {
        return $this->visibility;
    }

    public function setVisibility(string $visibility): Usergroup
    {
        $this->visibility = $visibility;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): Usergroup
    {
        $this->url = $url;

        return $this;
    }

    public function getAuthorId(): string
    {
        return $this->authorId;
    }

    public function setAuthorId(string $authorId): Usergroup
    {
        $this->authorId = $authorId;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function getDefaultIllustration($size): string
    {
        $size = empty($size) ? 32 : (int) $size;

        return "/img/icons/$size/group_na.png";
    }

    public function getResourceIdentifier(): int
    {
        return $this->getId();
    }

    public function getResourceName(): string
    {
        return $this->getName();
    }

    public function setResourceName(string $name): self
    {
        return $this->setName($name);
    }
}
