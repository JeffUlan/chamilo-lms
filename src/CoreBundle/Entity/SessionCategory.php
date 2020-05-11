<?php

/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * SessionCategory.
 * @ApiResource(
 *     normalizationContext={"groups"={"session_category:read"}, "swagger_definition_name"="Read"},
 *     denormalizationContext={"groups"={"session_category:write"}},
 * )
 *
 * @ORM\Table(name="session_category")
 * @ORM\Entity
 */
class SessionCategory
{
    /**
     * @var int
     * @Groups({"session_category:read"})
     * @ORM\Column(name="id", type="integer", nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="AccessUrl", inversedBy="sessionCategory", cascade={"persist"})
     * @ORM\JoinColumn(name="access_url_id", referencedColumnName="id")
     */
    protected $url;

    /**
     * @ORM\OneToMany(targetEntity="Chamilo\CoreBundle\Entity\Session", mappedBy="category")
     */
    protected $session;

    /**
     * @var string
     * @Groups({"session_category:read", "session_category:write"})
     * @ORM\Column(name="name", type="string", length=100, nullable=true, unique=false)
     */
    protected $name;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_start", type="date", nullable=true, unique=false)
     */
    protected $dateStart;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_end", type="date", nullable=true, unique=false)
     */
    protected $dateEnd;

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->name;
    }

    /**
     * Set url.
     *
     * @return SessionCategory
     */
    public function setUrl(AccessUrl $url)
    {
        $this->url = $url;

        return $this;
    }

    public function getUrl(): AccessUrl
    {
        return $this->url;
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
     * @return SessionCategory
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
     * Set dateStart.
     *
     * @param \DateTime $dateStart
     *
     * @return SessionCategory
     */
    public function setDateStart($dateStart)
    {
        $this->dateStart = $dateStart;

        return $this;
    }

    /**
     * Get dateStart.
     *
     * @return \DateTime
     */
    public function getDateStart()
    {
        return $this->dateStart;
    }

    /**
     * Set dateEnd.
     *
     * @param \DateTime $dateEnd
     *
     * @return SessionCategory
     */
    public function setDateEnd($dateEnd)
    {
        $this->dateEnd = $dateEnd;

        return $this;
    }

    /**
     * Get dateEnd.
     *
     * @return \DateTime
     */
    public function getDateEnd()
    {
        return $this->dateEnd;
    }
}
