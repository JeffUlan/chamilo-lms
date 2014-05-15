<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @license see /license.txt
 * @author autogenerated
 */
class StudentPublication extends \CourseEntity
{
    /**
     * @return \Entity\Repository\StudentPublicationRepository
     */
     public static function repository(){
        return \Entity\Repository\StudentPublicationRepository::instance();
    }

    /**
     * @return \Entity\StudentPublication
     */
     public static function create(){
        return new self();
    }

    /**
     * @var integer $c_id
     */
    protected $c_id;

    /**
     * @var integer $id
     */
    protected $id;

    /**
     * @var string $url
     */
    protected $url;

    /**
     * @var string $title
     */
    protected $title;

    /**
     * @var text $description
     */
    protected $description;

    /**
     * @var string $author
     */
    protected $author;

    /**
     * @var boolean $active
     */
    protected $active;

    /**
     * @var boolean $accepted
     */
    protected $accepted;

    /**
     * @var integer $post_group_id
     */
    protected $post_group_id;

    /**
     * @var datetime $sent_date
     */
    protected $sent_date;

    /**
     * @var string $filetype
     */
    protected $filetype;

    /**
     * @var integer $has_properties
     */
    protected $has_properties;

    /**
     * @var boolean $view_properties
     */
    protected $view_properties;

    /**
     * @var float $qualification
     */
    protected $qualification;

    /**
     * @var datetime $date_of_qualification
     */
    protected $date_of_qualification;

    /**
     * @var integer $parent_id
     */
    protected $parent_id;

    /**
     * @var integer $qualificator_id
     */
    protected $qualificator_id;

    /**
     * @var float $weight
     */
    protected $weight;

    /**
     * @var integer $session_id
     */
    protected $session_id;

    /**
     * @var integer $user_id
     */
    protected $user_id;

    /**
     * @var integer $allow_text_assignment
     */
    protected $allow_text_assignment;

    /**
     * @var integer $contains_file
     */
    protected $contains_file;


    /**
     * Set c_id
     *
     * @param integer $value
     * @return StudentPublication
     */
    public function set_c_id($value)
    {
        $this->c_id = $value;
        return $this;
    }

    /**
     * Get c_id
     *
     * @return integer 
     */
    public function get_c_id()
    {
        return $this->c_id;
    }

    /**
     * Set id
     *
     * @param integer $value
     * @return StudentPublication
     */
    public function set_id($value)
    {
        $this->id = $value;
        return $this;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function get_id()
    {
        return $this->id;
    }

    /**
     * Set url
     *
     * @param string $value
     * @return StudentPublication
     */
    public function set_url($value)
    {
        $this->url = $value;
        return $this;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function get_url()
    {
        return $this->url;
    }

    /**
     * Set title
     *
     * @param string $value
     * @return StudentPublication
     */
    public function set_title($value)
    {
        $this->title = $value;
        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function get_title()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param text $value
     * @return StudentPublication
     */
    public function set_description($value)
    {
        $this->description = $value;
        return $this;
    }

    /**
     * Get description
     *
     * @return text 
     */
    public function get_description()
    {
        return $this->description;
    }

    /**
     * Set author
     *
     * @param string $value
     * @return StudentPublication
     */
    public function set_author($value)
    {
        $this->author = $value;
        return $this;
    }

    /**
     * Get author
     *
     * @return string 
     */
    public function get_author()
    {
        return $this->author;
    }

    /**
     * Set active
     *
     * @param boolean $value
     * @return StudentPublication
     */
    public function set_active($value)
    {
        $this->active = $value;
        return $this;
    }

    /**
     * Get active
     *
     * @return boolean 
     */
    public function get_active()
    {
        return $this->active;
    }

    /**
     * Set accepted
     *
     * @param boolean $value
     * @return StudentPublication
     */
    public function set_accepted($value)
    {
        $this->accepted = $value;
        return $this;
    }

    /**
     * Get accepted
     *
     * @return boolean 
     */
    public function get_accepted()
    {
        return $this->accepted;
    }

    /**
     * Set post_group_id
     *
     * @param integer $value
     * @return StudentPublication
     */
    public function set_post_group_id($value)
    {
        $this->post_group_id = $value;
        return $this;
    }

    /**
     * Get post_group_id
     *
     * @return integer 
     */
    public function get_post_group_id()
    {
        return $this->post_group_id;
    }

    /**
     * Set sent_date
     *
     * @param datetime $value
     * @return StudentPublication
     */
    public function set_sent_date($value)
    {
        $this->sent_date = $value;
        return $this;
    }

    /**
     * Get sent_date
     *
     * @return datetime 
     */
    public function get_sent_date()
    {
        return $this->sent_date;
    }

    /**
     * Set filetype
     *
     * @param string $value
     * @return StudentPublication
     */
    public function set_filetype($value)
    {
        $this->filetype = $value;
        return $this;
    }

    /**
     * Get filetype
     *
     * @return string 
     */
    public function get_filetype()
    {
        return $this->filetype;
    }

    /**
     * Set has_properties
     *
     * @param integer $value
     * @return StudentPublication
     */
    public function set_has_properties($value)
    {
        $this->has_properties = $value;
        return $this;
    }

    /**
     * Get has_properties
     *
     * @return integer 
     */
    public function get_has_properties()
    {
        return $this->has_properties;
    }

    /**
     * Set view_properties
     *
     * @param boolean $value
     * @return StudentPublication
     */
    public function set_view_properties($value)
    {
        $this->view_properties = $value;
        return $this;
    }

    /**
     * Get view_properties
     *
     * @return boolean 
     */
    public function get_view_properties()
    {
        return $this->view_properties;
    }

    /**
     * Set qualification
     *
     * @param float $value
     * @return StudentPublication
     */
    public function set_qualification($value)
    {
        $this->qualification = $value;
        return $this;
    }

    /**
     * Get qualification
     *
     * @return float 
     */
    public function get_qualification()
    {
        return $this->qualification;
    }

    /**
     * Set date_of_qualification
     *
     * @param datetime $value
     * @return StudentPublication
     */
    public function set_date_of_qualification($value)
    {
        $this->date_of_qualification = $value;
        return $this;
    }

    /**
     * Get date_of_qualification
     *
     * @return datetime 
     */
    public function get_date_of_qualification()
    {
        return $this->date_of_qualification;
    }

    /**
     * Set parent_id
     *
     * @param integer $value
     * @return StudentPublication
     */
    public function set_parent_id($value)
    {
        $this->parent_id = $value;
        return $this;
    }

    /**
     * Get parent_id
     *
     * @return integer 
     */
    public function get_parent_id()
    {
        return $this->parent_id;
    }

    /**
     * Set qualificator_id
     *
     * @param integer $value
     * @return StudentPublication
     */
    public function set_qualificator_id($value)
    {
        $this->qualificator_id = $value;
        return $this;
    }

    /**
     * Get qualificator_id
     *
     * @return integer 
     */
    public function get_qualificator_id()
    {
        return $this->qualificator_id;
    }

    /**
     * Set weight
     *
     * @param float $value
     * @return StudentPublication
     */
    public function set_weight($value)
    {
        $this->weight = $value;
        return $this;
    }

    /**
     * Get weight
     *
     * @return float 
     */
    public function get_weight()
    {
        return $this->weight;
    }

    /**
     * Set session_id
     *
     * @param integer $value
     * @return StudentPublication
     */
    public function set_session_id($value)
    {
        $this->session_id = $value;
        return $this;
    }

    /**
     * Get session_id
     *
     * @return integer 
     */
    public function get_session_id()
    {
        return $this->session_id;
    }

    /**
     * Set user_id
     *
     * @param integer $value
     * @return StudentPublication
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
     * Set allow_text_assignment
     *
     * @param integer $value
     * @return StudentPublication
     */
    public function set_allow_text_assignment($value)
    {
        $this->allow_text_assignment = $value;
        return $this;
    }

    /**
     * Get allow_text_assignment
     *
     * @return integer 
     */
    public function get_allow_text_assignment()
    {
        return $this->allow_text_assignment;
    }

    /**
     * Set contains_file
     *
     * @param integer $value
     * @return StudentPublication
     */
    public function set_contains_file($value)
    {
        $this->contains_file = $value;
        return $this;
    }

    /**
     * Get contains_file
     *
     * @return integer 
     */
    public function get_contains_file()
    {
        return $this->contains_file;
    }
}