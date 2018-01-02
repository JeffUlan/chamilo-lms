<?php
/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SysAnnouncement
 *
 * @ORM\Table(name="sys_announcement")
 * @ORM\Entity
 */
class SysAnnouncement
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue()
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_start", type="datetime", nullable=false)
     */
    private $dateStart;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_end", type="datetime", nullable=false)
     */
    private $dateEnd;

    /**
     * @var boolean
     *
     * @ORM\Column(name="visible_teacher", type="boolean", nullable=false)
     */
    private $visibleTeacher;

    /**
     * @var boolean
     *
     * @ORM\Column(name="visible_student", type="boolean", nullable=false)
     */
    private $visibleStudent;

    /**
     * @var boolean
     *
     * @ORM\Column(name="visible_guest", type="boolean", nullable=false)
     */
    private $visibleGuest;

     /**
     * @var boolean
     *
     * @ORM\Column(name="visible_drh", type="boolean", nullable=false)
     */
    private $visibleDrh;

    /**
     * @var boolean
     *
     * @ORM\Column(name="visible_session_admin", type="boolean", nullable=false)
     */
    private $visibleSessionAdmin;

    /**
     * @var boolean
     *
     * @ORM\Column(name="visible_boss", type="boolean", nullable=false)
     */
    private $visibleBoss;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=250, nullable=false)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text", nullable=false)
     */
    private $content;

    /**
     * @var string
     *
     * @ORM\Column(name="lang", type="string", length=70, nullable=true)
     */
    private $lang;

    /**
     * @var integer
     *
     * @ORM\Column(name="access_url_id", type="integer", nullable=false)
     */
    private $accessUrlId;

    /**
     * SysAnnouncement constructor.
     */
    public function __construct()
    {
        $this->visibleBoss = 0;
        $this->visibleDrh = 0;
        $this->visibleGuest= 0;
        $this->visibleSessionAdmin = 0;
        $this->visibleStudent = 0;
        $this->visibleTeacher = 0;
    }

    /**
     * Set dateStart
     *
     * @param \DateTime $dateStart
     * @return SysAnnouncement
     */
    public function setDateStart($dateStart)
    {
        $this->dateStart = $dateStart;

        return $this;
    }

    /**
     * Get dateStart
     *
     * @return \DateTime
     */
    public function getDateStart()
    {
        return $this->dateStart;
    }

    /**
     * Set dateEnd
     *
     * @param \DateTime $dateEnd
     * @return SysAnnouncement
     */
    public function setDateEnd($dateEnd)
    {
        $this->dateEnd = $dateEnd;

        return $this;
    }

    /**
     * Get dateEnd
     *
     * @return \DateTime
     */
    public function getDateEnd()
    {
        return $this->dateEnd;
    }

    /**
     * Set visibleTeacher
     *
     * @param boolean $visibleTeacher
     * @return SysAnnouncement
     */
    public function setVisibleTeacher($visibleTeacher)
    {
        $this->visibleTeacher = $visibleTeacher;

        return $this;
    }

    /**
     * Get visibleTeacher
     *
     * @return boolean
     */
    public function getVisibleTeacher()
    {
        return $this->visibleTeacher;
    }

    /**
     * Set visibleStudent
     *
     * @param boolean $visibleStudent
     * @return SysAnnouncement
     */
    public function setVisibleStudent($visibleStudent)
    {
        $this->visibleStudent = $visibleStudent;

        return $this;
    }

    /**
     * Get visibleStudent
     *
     * @return boolean
     */
    public function getVisibleStudent()
    {
        return $this->visibleStudent;
    }

    /**
     * Set visibleGuest
     *
     * @param boolean $visibleGuest
     * @return SysAnnouncement
     */
    public function setVisibleGuest($visibleGuest)
    {
        $this->visibleGuest = $visibleGuest;

        return $this;
    }

    /**
     * Get visibleGuest
     *
     * @return boolean
     */
    public function getVisibleGuest()
    {
        return $this->visibleGuest;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return SysAnnouncement
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return SysAnnouncement
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set lang
     *
     * @param string $lang
     * @return SysAnnouncement
     */
    public function setLang($lang)
    {
        $this->lang = $lang;

        return $this;
    }

    /**
     * Get lang
     *
     * @return string
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * Set accessUrlId
     *
     * @param integer $accessUrlId
     * @return SysAnnouncement
     */
    public function setAccessUrlId($accessUrlId)
    {
        $this->accessUrlId = $accessUrlId;

        return $this;
    }

    /**
     * Get accessUrlId
     *
     * @return integer
     */
    public function getAccessUrlId()
    {
        return $this->accessUrlId;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return bool
     */
    public function isVisibleDrh(): bool
    {
        return $this->visibleDrh;
    }

    /**
     * @param bool $visibleDrh
     * @return SysAnnouncement
     */
    public function setVisibleDrh(bool $visibleDrh): SysAnnouncement
    {
        $this->visibleDrh = $visibleDrh;

        return $this;
    }

    /**
     * @return bool
     */
    public function isVisibleSessionAdmin(): bool
    {
        return $this->visibleSessionAdmin;
    }

    /**
     * @param bool $visibleSessionAdmin
     * @return SysAnnouncement
     */
    public function setVisibleSessionAdmin(bool $visibleSessionAdmin
    ): SysAnnouncement {
        $this->visibleSessionAdmin = $visibleSessionAdmin;

        return $this;
    }

    /**
     * @return bool
     */
    public function isVisibleBoss(): bool
    {
        return $this->visibleBoss;
    }

    /**
     * @param bool $visibleBoss
     * @return SysAnnouncement
     */
    public function setVisibleBoss(bool $visibleBoss): SysAnnouncement
    {
        $this->visibleBoss = $visibleBoss;

        return $this;
    }

}
