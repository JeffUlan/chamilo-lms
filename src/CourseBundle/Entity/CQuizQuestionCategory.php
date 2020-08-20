<?php

/* For licensing terms, see /license.txt */

namespace Chamilo\CourseBundle\Entity;

use Chamilo\CoreBundle\Entity\AbstractResource;
use Chamilo\CoreBundle\Entity\ResourceInterface;
use Chamilo\CoreBundle\Entity\Session;
use Chamilo\CourseBundle\Traits\ShowCourseResourcesInSessionTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping as ORM;

/**
 * CQuizQuestionCategory.
 *
 * @ORM\Table(
 *  name="c_quiz_question_category",
 *  indexes={
 *      @ORM\Index(name="course", columns={"c_id"})
 *  }
 * )
 * @ORM\Entity
 */
class CQuizQuestionCategory extends AbstractResource implements ResourceInterface
{
    use ShowCourseResourcesInSessionTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="iid", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    protected $iid;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=false)
     */
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;

    /**
     * @ORM\ManyToOne(targetEntity="Chamilo\CoreBundle\Entity\Course")
     * @ORM\JoinColumn(name="c_id", referencedColumnName="id", nullable=false)
     */
    protected $course;

    /**
     * @ORM\ManyToOne(targetEntity="Chamilo\CoreBundle\Entity\Session", cascade={"persist"})
     * @ORM\JoinColumn(name="session_id", referencedColumnName="id", nullable=true)
     */
    protected $session;

    /**
     *
     * @ORM\ManyToMany(targetEntity="Chamilo\CourseBundle\Entity\CQuizQuestion")
     * @ORM\JoinTable(name="c_quiz_question_rel_category",
     *      joinColumns={@ORM\JoinColumn(name="category_id", referencedColumnName="iid")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="question_id", referencedColumnName="iid")}
     * )
     */
    protected $questions;

    public function __construct()
    {
        $this->questions = new ArrayCollection();
    }

    /**
     * @return ArrayCollection
     */
    public function getQuestions()
    {
        return $this->questions;
    }

    public function setQuestions($questions): self
    {
        $this->questions = $questions;

        return $this;
    }


    public function __toString(): string
    {
        return $this->getTitle();
    }

    /**
     * @return int
     */
    public function getIid()
    {
        return $this->iid;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle()
    {
        return (string) $this->title;
    }

    /**
     * Set description.
     *
     * @param string $description
     *
     * @return CQuizQuestionCategory
     */
    public function setDescription($description)
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

    public function getCourse()
    {
        return $this->course;
    }

    /**
     * @return CQuizQuestionCategory
     */
    public function setCourse($course)
    {
        $this->course = $course;

        return $this;
    }

    public function getSession()
    {
        return $this->session;
    }

    /**
     * @param Session $session
     *
     * @return CQuizQuestionCategory
     */
    public function setSession($session)
    {
        $this->session = $session;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasSession()
    {
        return null !== $this->session;
    }

    /**
     * @ORM\PostPersist()
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        // Update id with iid value
        /*$em = $args->getEntityManager();
        $em->persist($this);
        $em->flush();*/
    }

    /**
     * Resource identifier.
     */
    public function getResourceIdentifier(): int
    {
        return $this->getIid();
    }

    public function getResourceName(): string
    {
        return $this->getTitle();
    }
}
