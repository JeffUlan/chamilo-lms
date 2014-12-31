<?php
/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Entity;

use Chamilo\UserBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class SessionRelCourseRelUser
 *
 * @ORM\Table(
 *      name="session_rel_course_rel_user",
 *      indexes={@ORM\Index(
 *          name="idx_session_rel_course_rel_user_id_user", columns={"id_user"}
 *          ),
 *       @ORM\Index(name="idx_session_rel_course_rel_user_course_id", columns={"c_id"})})
 * @ORM\Entity
 */
class SessionRelCourseRelUser
{
    public $statusList = array(

    );

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
     *
     * @ORM\Column(name="visibility", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $visibility;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $status;

    /**
     * @var integer
     *
     * @ORM\Column(name="legal_agreement", type="integer", precision=0, scale=0, nullable=true, unique=false)
     */
    private $legalAgreement;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="Chamilo\UserBundle\Entity\User", inversedBy="sessionCourseSubscriptions", cascade={"persist"})
     * @ORM\JoinColumn(name="id_user", referencedColumnName="id")
     */
    protected $user;

    /**
     * @var Session
     * @ORM\ManyToOne(targetEntity="Session", inversedBy="userCourseSubscriptions", cascade={"persist"})
     * @ORM\JoinColumn(name="id_session", referencedColumnName="id")
     */
    protected $session;

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * @param mixed $session
     */
    public function setSession($session)
    {
        $this->session = $session;
    }

    /**
     * @return mixed
     */
    public function getCourse()
    {
        return $this->course;
    }

    /**
     * @param mixed $course
     */
    public function setCourse($course)
    {
        $this->course = $course;
    }

    /**
     * @var Course
     * @ORM\ManyToOne(targetEntity="Chamilo\CoreBundle\Entity\Course", inversedBy="sessionUserSubscriptions", cascade={"persist"})
     * @ORM\JoinColumn(name="c_id", referencedColumnName="id")
     */
    protected $course;

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
     * Set visibility
     *
     * @param integer $visibility
     * @return SessionRelCourseRelUser
     */
    public function setVisibility($visibility)
    {
        $this->visibility = $visibility;

        return $this;
    }

    /**
     * Get visibility
     *
     * @return integer
     */
    public function getVisibility()
    {
        return $this->visibility;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return SessionRelCourseRelUser
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set legalAgreement
     *
     * @param integer $legalAgreement
     * @return SessionRelCourseRelUser
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
}
