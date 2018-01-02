<?php
/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Entity;

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
     * @ORM\ManyToOne(targetEntity="Chamilo\CoreBundle\Entity\Course", inversedBy="searchEngineRefs")
     * @ORM\JoinColumn(name="c_id", referencedColumnName="id")
     */
    private $course;

    /**
     * @var string
     *
     * @ORM\Column(name="tool_id", type="string", length=100, nullable=false)
     */
    private $toolId;

    /**
     * @var integer
     *
     * @ORM\Column(name="ref_id_high_level", type="integer", nullable=false)
     */
    private $refIdHighLevel;

    /**
     * @var integer
     *
     * @ORM\Column(name="ref_id_second_level", type="integer", nullable=true)
     */
    private $refIdSecondLevel;

    /**
     * @var integer
     *
     * @ORM\Column(name="search_did", type="integer", nullable=false)
     */
    private $searchDid;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;



    /**
     * Set course
     * @param \Chamilo\CoreBundle\Entity\Course $course
     * @return \Chamilo\CoreBundle\Entity\SearchEngineRef
     */
    public function setCourse(Course $course)
    {
        $this->course = $course;

        return $this;
    }

    /**
     * Get course
     * @return \Chamilo\CoreBundle\Entity\Course
     */
    public function getCourse()
    {
        return $this->course;
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

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
}
