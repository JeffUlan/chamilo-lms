<?php

namespace ChamiloLMS\CourseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CLpView
 *
 * @ORM\Table(name="c_lp_view", indexes={@ORM\Index(name="lp_id", columns={"lp_id"}), @ORM\Index(name="user_id", columns={"user_id"}), @ORM\Index(name="session_id", columns={"session_id"})})
 * @ORM\Entity
 */
class CLpView
{
    /**
     * @var integer
     *
     * @ORM\Column(name="iid", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $iid;

    /**
     * @var integer
     *
     * @ORM\Column(name="c_id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $cId;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="lp_id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $lpId;

    /**
     * @var integer
     *
     * @ORM\Column(name="user_id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $userId;

    /**
     * @var integer
     *
     * @ORM\Column(name="view_count", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $viewCount;

    /**
     * @var integer
     *
     * @ORM\Column(name="last_item", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $lastItem;

    /**
     * @var integer
     *
     * @ORM\Column(name="progress", type="integer", precision=0, scale=0, nullable=true, unique=false)
     */
    private $progress;

    /**
     * @var integer
     *
     * @ORM\Column(name="session_id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $sessionId;


    /**
     * Get iid
     *
     * @return integer 
     */
    public function getIid()
    {
        return $this->iid;
    }

    /**
     * Set cId
     *
     * @param integer $cId
     * @return CLpView
     */
    public function setCId($cId)
    {
        $this->cId = $cId;

        return $this;
    }

    /**
     * Get cId
     *
     * @return integer 
     */
    public function getCId()
    {
        return $this->cId;
    }

    /**
     * Set id
     *
     * @param integer $id
     * @return CLpView
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
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
     * Set lpId
     *
     * @param integer $lpId
     * @return CLpView
     */
    public function setLpId($lpId)
    {
        $this->lpId = $lpId;

        return $this;
    }

    /**
     * Get lpId
     *
     * @return integer 
     */
    public function getLpId()
    {
        return $this->lpId;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     * @return CLpView
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return integer 
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set viewCount
     *
     * @param integer $viewCount
     * @return CLpView
     */
    public function setViewCount($viewCount)
    {
        $this->viewCount = $viewCount;

        return $this;
    }

    /**
     * Get viewCount
     *
     * @return integer 
     */
    public function getViewCount()
    {
        return $this->viewCount;
    }

    /**
     * Set lastItem
     *
     * @param integer $lastItem
     * @return CLpView
     */
    public function setLastItem($lastItem)
    {
        $this->lastItem = $lastItem;

        return $this;
    }

    /**
     * Get lastItem
     *
     * @return integer 
     */
    public function getLastItem()
    {
        return $this->lastItem;
    }

    /**
     * Set progress
     *
     * @param integer $progress
     * @return CLpView
     */
    public function setProgress($progress)
    {
        $this->progress = $progress;

        return $this;
    }

    /**
     * Get progress
     *
     * @return integer 
     */
    public function getProgress()
    {
        return $this->progress;
    }

    /**
     * Set sessionId
     *
     * @param integer $sessionId
     * @return CLpView
     */
    public function setSessionId($sessionId)
    {
        $this->sessionId = $sessionId;

        return $this;
    }

    /**
     * Get sessionId
     *
     * @return integer 
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }
}
