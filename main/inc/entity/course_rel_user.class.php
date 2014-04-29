<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @license see /license.txt
 * @author autogenerated
 */
class CourseRelUser extends \Entity
{
    /**
     * @return \Entity\Repository\CourseRelUserRepository
     */
     public static function repository(){
        return \Entity\Repository\CourseRelUserRepository::instance();
    }

    /**
     * @return \Entity\CourseRelUser
     */
     public static function create(){
        return new self();
    }

    /**
     * @var string $course_code
     */
    protected $course_code;

    /**
     * @var integer $user_id
     */
    protected $user_id;

    /**
     * @var integer $relation_type
     */
    protected $relation_type;

    /**
     * @var boolean $status
     */
    protected $status;

    /**
     * @var string $role
     */
    protected $role;

    /**
     * @var integer $group_id
     */
    protected $group_id;

    /**
     * @var integer $tutor_id
     */
    protected $tutor_id;

    /**
     * @var integer $sort
     */
    protected $sort;

    /**
     * @var integer $user_course_cat
     */
    protected $user_course_cat;

    /**
     * @var integer $legal_agreement
     */
    protected $legal_agreement;


    /**
     * Set course_code
     *
     * @param string $value
     * @return CourseRelUser
     */
    public function set_course_code($value)
    {
        $this->course_code = $value;
        return $this;
    }

    /**
     * Get course_code
     *
     * @return string 
     */
    public function get_course_code()
    {
        return $this->course_code;
    }

    /**
     * Set user_id
     *
     * @param integer $value
     * @return CourseRelUser
     */
    public function set_user_id($value)
    {
        $this->user_id = $value;
        return $this;
    }

    /**
     * Get user_id
     *
     * @return integer 
     */
    public function get_user_id()
    {
        return $this->user_id;
    }

    /**
     * Set relation_type
     *
     * @param integer $value
     * @return CourseRelUser
     */
    public function set_relation_type($value)
    {
        $this->relation_type = $value;
        return $this;
    }

    /**
     * Get relation_type
     *
     * @return integer 
     */
    public function get_relation_type()
    {
        return $this->relation_type;
    }

    /**
     * Set status
     *
     * @param boolean $value
     * @return CourseRelUser
     */
    public function set_status($value)
    {
        $this->status = $value;
        return $this;
    }

    /**
     * Get status
     *
     * @return boolean 
     */
    public function get_status()
    {
        return $this->status;
    }

    /**
     * Set role
     *
     * @param string $value
     * @return CourseRelUser
     */
    public function set_role($value)
    {
        $this->role = $value;
        return $this;
    }

    /**
     * Get role
     *
     * @return string 
     */
    public function get_role()
    {
        return $this->role;
    }

    /**
     * Set group_id
     *
     * @param integer $value
     * @return CourseRelUser
     */
    public function set_group_id($value)
    {
        $this->group_id = $value;
        return $this;
    }

    /**
     * Get group_id
     *
     * @return integer 
     */
    public function get_group_id()
    {
        return $this->group_id;
    }

    /**
     * Set tutor_id
     *
     * @param integer $value
     * @return CourseRelUser
     */
    public function set_tutor_id($value)
    {
        $this->tutor_id = $value;
        return $this;
    }

    /**
     * Get tutor_id
     *
     * @return integer 
     */
    public function get_tutor_id()
    {
        return $this->tutor_id;
    }

    /**
     * Set sort
     *
     * @param integer $value
     * @return CourseRelUser
     */
    public function set_sort($value)
    {
        $this->sort = $value;
        return $this;
    }

    /**
     * Get sort
     *
     * @return integer 
     */
    public function get_sort()
    {
        return $this->sort;
    }

    /**
     * Set user_course_cat
     *
     * @param integer $value
     * @return CourseRelUser
     */
    public function set_user_course_cat($value)
    {
        $this->user_course_cat = $value;
        return $this;
    }

    /**
     * Get user_course_cat
     *
     * @return integer 
     */
    public function get_user_course_cat()
    {
        return $this->user_course_cat;
    }

    /**
     * Set legal_agreement
     *
     * @param integer $value
     * @return CourseRelUser
     */
    public function set_legal_agreement($value)
    {
        $this->legal_agreement = $value;
        return $this;
    }

    /**
     * Get legal_agreement
     *
     * @return integer 
     */
    public function get_legal_agreement()
    {
        return $this->legal_agreement;
    }
}