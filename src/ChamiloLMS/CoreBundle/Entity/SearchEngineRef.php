<?php

namespace ChamiloLMS\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SearchEngineRef
 *
 * @ORM\Table(name="search_engine_ref")
 * @ORM\Entity
 */
class SearchEngineRef
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="course_code", type="string", length=40, precision=0, scale=0, nullable=false, unique=false)
     */
    private $courseCode;

    /**
     * @var string
     *
     * @ORM\Column(name="tool_id", type="string", length=100, precision=0, scale=0, nullable=false, unique=false)
     */
    private $toolId;

    /**
     * @var integer
     *
     * @ORM\Column(name="ref_id_high_level", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $refIdHighLevel;

    /**
     * @var integer
     *
     * @ORM\Column(name="ref_id_second_level", type="integer", precision=0, scale=0, nullable=true, unique=false)
     */
    private $refIdSecondLevel;

    /**
     * @var integer
     *
     * @ORM\Column(name="search_did", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $searchDid;


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
     * Set courseCode
     *
     * @param string $courseCode
     * @return SearchEngineRef
     */
    public function setCourseCode($courseCode)
    {
        $this->courseCode = $courseCode;

        return $this;
    }

    /**
     * Get courseCode
     *
     * @return string 
     */
    public function getCourseCode()
    {
        return $this->courseCode;
    }

    /**
     * Set toolId
     *
     * @param string $toolId
     * @return SearchEngineRef
     */
    public function setToolId($toolId)
    {
        $this->toolId = $toolId;

        return $this;
    }

    /**
     * Get toolId
     *
     * @return string 
     */
    public function getToolId()
    {
        return $this->toolId;
    }

    /**
     * Set refIdHighLevel
     *
     * @param integer $refIdHighLevel
     * @return SearchEngineRef
     */
    public function setRefIdHighLevel($refIdHighLevel)
    {
        $this->refIdHighLevel = $refIdHighLevel;

        return $this;
    }

    /**
     * Get refIdHighLevel
     *
     * @return integer 
     */
    public function getRefIdHighLevel()
    {
        return $this->refIdHighLevel;
    }

    /**
     * Set refIdSecondLevel
     *
     * @param integer $refIdSecondLevel
     * @return SearchEngineRef
     */
    public function setRefIdSecondLevel($refIdSecondLevel)
    {
        $this->refIdSecondLevel = $refIdSecondLevel;

        return $this;
    }

    /**
     * Get refIdSecondLevel
     *
     * @return integer 
     */
    public function getRefIdSecondLevel()
    {
        return $this->refIdSecondLevel;
    }

    /**
     * Set searchDid
     *
     * @param integer $searchDid
     * @return SearchEngineRef
     */
    public function setSearchDid($searchDid)
    {
        $this->searchDid = $searchDid;

        return $this;
    }

    /**
     * Get searchDid
     *
     * @return integer 
     */
    public function getSearchDid()
    {
        return $this->searchDid;
    }
}
