<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * EntityCCourseDescription
 *
 * @Table(name="c_course_description")
 * @Entity
 */
class EntityCCourseDescription
{
    /**
     * @var integer
     *
     * @Column(name="c_id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @Id
     * @GeneratedValue(strategy="NONE")
     */
    private $cId;

    /**
     * @var integer
     *
     * @Column(name="id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @Id
     * @GeneratedValue(strategy="NONE")
     */
    private $id;

    /**
     * @var string
     *
     * @Column(name="title", type="string", length=255, precision=0, scale=0, nullable=true, unique=false)
     */
    private $title;

    /**
     * @var string
     *
     * @Column(name="content", type="text", precision=0, scale=0, nullable=true, unique=false)
     */
    private $content;

    /**
     * @var integer
     *
     * @Column(name="session_id", type="integer", precision=0, scale=0, nullable=true, unique=false)
     */
    private $sessionId;

    /**
     * @var boolean
     *
     * @Column(name="description_type", type="boolean", precision=0, scale=0, nullable=false, unique=false)
     */
    private $descriptionType;

    /**
     * @var integer
     *
     * @Column(name="progress", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $progress;


    /**
     * Set cId
     *
     * @param integer $cId
     * @return EntityCCourseDescription
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
     * @return EntityCCourseDescription
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
     * Set title
     *
     * @param string $title
     * @return EntityCCourseDescription
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
     * @return EntityCCourseDescription
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
     * Set sessionId
     *
     * @param integer $sessionId
     * @return EntityCCourseDescription
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

    /**
     * Set descriptionType
     *
     * @param boolean $descriptionType
     * @return EntityCCourseDescription
     */
    public function setDescriptionType($descriptionType)
    {
        $this->descriptionType = $descriptionType;

        return $this;
    }

    /**
     * Get descriptionType
     *
     * @return boolean 
     */
    public function getDescriptionType()
    {
        return $this->descriptionType;
    }

    /**
     * Set progress
     *
     * @param integer $progress
     * @return EntityCCourseDescription
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
}
