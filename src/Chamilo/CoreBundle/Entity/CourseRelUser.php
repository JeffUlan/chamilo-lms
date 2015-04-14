<?php
/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Entity;

use Chamilo\CourseBundle\Entity\CGroupInfo;
use Chamilo\UserBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * CourseRelUser
 *
 * @ORM\Table(
 *      name="course_rel_user",
 *      indexes={
 *          @ORM\Index(name="course_rel_user_user_id", columns={"id", "user_id"}),
 *          @ORM\Index(name="course_rel_user_c_id_user_id", columns={"id", "c_id", "user_id"})
 *      }
 * )
 * @ORM\Entity
 * @ORM\Table(name="course_rel_user")
 */
class CourseRelUser
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     * @todo use status instead of this
     * @deprecated
     * @ORM\Column(name="relation_type", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $relationType;

    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $status;

    /**
     * @var string
     * @todo this value should be renamed to description
     * @ORM\Column(name="role", type="string", length=60, precision=0, scale=0, nullable=true, unique=false)
     */
    private $role;

    /**
     * @var integer
     *
     * @ORM\Column(name="tutor_id", type="integer", precision=0, scale=0, nullable=true, unique=false)
     */
    private $tutorId;

    /**
     * @var integer
     *
     * @ORM\Column(name="sort", type="integer", precision=0, scale=0, nullable=true, unique=false)
     */
    private $sort;

    /**
     * @var integer
     *
     * @ORM\Column(name="user_course_cat", type="integer", precision=0, scale=0, nullable=true, unique=false)
     */
    private $userCourseCat;

    /**
     * @var integer
     *
     * @ORM\Column(name="legal_agreement", type="integer", precision=0, scale=0, nullable=true, unique=false)
     */
    private $legalAgreement;

    /**
     * @ORM\ManyToOne(targetEntity="Chamilo\UserBundle\Entity\User", inversedBy="courses", cascade={"persist"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="Chamilo\CoreBundle\Entity\Course", inversedBy="users", cascade={"persist"})
     * @ORM\JoinColumn(name="c_id", referencedColumnName="id")
     */
    protected $course;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->userCourseCat = 0;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return strval($this->getCourse()->getCode());
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @param Course $course
     * @return $this
     */
    public function setCourse(Course $course)
    {
        $this->course = $course;

        return $this;
    }

    /**
     * Get Course
     *
     * @return Course
     */
    public function getCourse()
    {
        return $this->course;
    }

    /**
     * @param $user
     * @return $this
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get User
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set relationType
     *
     * @param integer $relationType
     * @return CourseRelUser
     */
    public function setRelationType($relationType)
    {
        $this->relationType = $relationType;

        return $this;
    }

    /**
     * Get relationType
     *
     * @return integer
     */
    public function getRelationType()
    {
        return $this->relationType;
    }

    /**
     * Set status
     *
     * @param boolean $status
     * @return CourseRelUser
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return boolean
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set role
     *
     * @param string $role
     * @return CourseRelUser
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set groupId
     *
     * @param integer $groupId
     * @return CourseRelUser
     */
    public function setGroupId($groupId)
    {
        $this->groupId = $groupId;

        return $this;
    }

    /**
     * Get groupId
     *
     * @return integer
     */
    public function getGroupId()
    {
        return $this->groupId;
    }

    /**
     * Set tutorId
     *
     * @param integer $tutorId
     * @return CourseRelUser
     */
    public function setTutorId($tutorId)
    {
        $this->tutorId = $tutorId;

        return $this;
    }

    /**
     * Get tutorId
     *
     * @return integer
     */
    public function getTutorId()
    {
        return $this->tutorId;
    }

    /**
     * Set sort
     *
     * @param integer $sort
     * @return CourseRelUser
     */
    public function setSort($sort)
    {
        $this->sort = $sort;

        return $this;
    }

    /**
     * Get sort
     *
     * @return integer
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * Set userCourseCat
     *
     * @param integer $userCourseCat
     * @return CourseRelUser
     */
    public function setUserCourseCat($userCourseCat)
    {
        $this->userCourseCat = $userCourseCat;

        return $this;
    }

    /**
     * Get userCourseCat
     *
     * @return integer
     */
    public function getUserCourseCat()
    {
        return $this->userCourseCat;
    }

    /**
     * Set legalAgreement
     *
     * @param integer $legalAgreement
     * @return CourseRelUser
     */
    public function setLegalAgreement($legalAgreement)
    {
        $this->legalAgreement = $legalAgreement;

        return $this;
    }

    /**
     * Get legalAgreement
     *
     * @return integer
     */
    public function getLegalAgreement()
    {
        return $this->legalAgreement;
    }

    /**
     * Get relation_type list
     * @deprecated
     *
     * @return array
     */
    public static function getRelationTypeList()
    {
        return array(
            '0' => '',
            COURSE_RELATION_TYPE_RRHH => 'drh',
        );
    }

    /**
     * Get status list
     * @return array
     */
    public static function getStatusList()
    {
        return array(
            User::COURSE_MANAGER => 'Teacher',
            User::STUDENT => 'Student'
            //User::DRH => 'DRH'
        );
    }
}
