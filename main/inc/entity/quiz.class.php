<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @license see /license.txt
 * @author autogenerated
 */
class Quiz extends \CourseEntity
{
    /**
     * @return \Entity\Repository\QuizRepository
     */
     public static function repository(){
        return \Entity\Repository\QuizRepository::instance();
    }

    /**
     * @return \Entity\Quiz
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
     * @var string $title
     */
    protected $title;

    /**
     * @var text $description
     */
    protected $description;

    /**
     * @var string $sound
     */
    protected $sound;

    /**
     * @var boolean $type
     */
    protected $type;

    /**
     * @var integer $random
     */
    protected $random;

    /**
     * @var boolean $random_answers
     */
    protected $random_answers;

    /**
     * @var boolean $active
     */
    protected $active;

    /**
     * @var integer $results_disabled
     */
    protected $results_disabled;

    /**
     * @var text $access_condition
     */
    protected $access_condition;

    /**
     * @var integer $max_attempt
     */
    protected $max_attempt;

    /**
     * @var datetime $start_time
     */
    protected $start_time;

    /**
     * @var datetime $end_time
     */
    protected $end_time;

    /**
     * @var integer $feedback_type
     */
    protected $feedback_type;

    /**
     * @var integer $expired_time
     */
    protected $expired_time;

    /**
     * @var integer $session_id
     */
    protected $session_id;

    /**
     * @var integer $propagate_neg
     */
    protected $propagate_neg;

    /**
     * @var integer $review_answers
     */
    protected $review_answers;

    /**
     * @var integer $random_by_category
     */
    protected $random_by_category;

    /**
     * @var text $text_when_finished
     */
    protected $text_when_finished;

    /**
     * @var integer $display_category_name
     */
    protected $display_category_name;

    /**
     * @var integer $pass_percentage
     */
    protected $pass_percentage;


    /**
     * Set c_id
     *
     * @param integer $value
     * @return Quiz
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
     * @return Quiz
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
     * Set title
     *
     * @param string $value
     * @return Quiz
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
     * @return Quiz
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
     * Set sound
     *
     * @param string $value
     * @return Quiz
     */
    public function set_sound($value)
    {
        $this->sound = $value;
        return $this;
    }

    /**
     * Get sound
     *
     * @return string 
     */
    public function get_sound()
    {
        return $this->sound;
    }

    /**
     * Set type
     *
     * @param boolean $value
     * @return Quiz
     */
    public function set_type($value)
    {
        $this->type = $value;
        return $this;
    }

    /**
     * Get type
     *
     * @return boolean 
     */
    public function get_type()
    {
        return $this->type;
    }

    /**
     * Set random
     *
     * @param integer $value
     * @return Quiz
     */
    public function set_random($value)
    {
        $this->random = $value;
        return $this;
    }

    /**
     * Get random
     *
     * @return integer 
     */
    public function get_random()
    {
        return $this->random;
    }

    /**
     * Set random_answers
     *
     * @param boolean $value
     * @return Quiz
     */
    public function set_random_answers($value)
    {
        $this->random_answers = $value;
        return $this;
    }

    /**
     * Get random_answers
     *
     * @return boolean 
     */
    public function get_random_answers()
    {
        return $this->random_answers;
    }

    /**
     * Set active
     *
     * @param boolean $value
     * @return Quiz
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
     * Set results_disabled
     *
     * @param integer $value
     * @return Quiz
     */
    public function set_results_disabled($value)
    {
        $this->results_disabled = $value;
        return $this;
    }

    /**
     * Get results_disabled
     *
     * @return integer 
     */
    public function get_results_disabled()
    {
        return $this->results_disabled;
    }

    /**
     * Set access_condition
     *
     * @param text $value
     * @return Quiz
     */
    public function set_access_condition($value)
    {
        $this->access_condition = $value;
        return $this;
    }

    /**
     * Get access_condition
     *
     * @return text 
     */
    public function get_access_condition()
    {
        return $this->access_condition;
    }

    /**
     * Set max_attempt
     *
     * @param integer $value
     * @return Quiz
     */
    public function set_max_attempt($value)
    {
        $this->max_attempt = $value;
        return $this;
    }

    /**
     * Get max_attempt
     *
     * @return integer 
     */
    public function get_max_attempt()
    {
        return $this->max_attempt;
    }

    /**
     * Set start_time
     *
     * @param datetime $value
     * @return Quiz
     */
    public function set_start_time($value)
    {
        $this->start_time = $value;
        return $this;
    }

    /**
     * Get start_time
     *
     * @return datetime 
     */
    public function get_start_time()
    {
        return $this->start_time;
    }

    /**
     * Set end_time
     *
     * @param datetime $value
     * @return Quiz
     */
    public function set_end_time($value)
    {
        $this->end_time = $value;
        return $this;
    }

    /**
     * Get end_time
     *
     * @return datetime 
     */
    public function get_end_time()
    {
        return $this->end_time;
    }

    /**
     * Set feedback_type
     *
     * @param integer $value
     * @return Quiz
     */
    public function set_feedback_type($value)
    {
        $this->feedback_type = $value;
        return $this;
    }

    /**
     * Get feedback_type
     *
     * @return integer 
     */
    public function get_feedback_type()
    {
        return $this->feedback_type;
    }

    /**
     * Set expired_time
     *
     * @param integer $value
     * @return Quiz
     */
    public function set_expired_time($value)
    {
        $this->expired_time = $value;
        return $this;
    }

    /**
     * Get expired_time
     *
     * @return integer 
     */
    public function get_expired_time()
    {
        return $this->expired_time;
    }

    /**
     * Set session_id
     *
     * @param integer $value
     * @return Quiz
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
     * Set propagate_neg
     *
     * @param integer $value
     * @return Quiz
     */
    public function set_propagate_neg($value)
    {
        $this->propagate_neg = $value;
        return $this;
    }

    /**
     * Get propagate_neg
     *
     * @return integer 
     */
    public function get_propagate_neg()
    {
        return $this->propagate_neg;
    }

    /**
     * Set review_answers
     *
     * @param integer $value
     * @return Quiz
     */
    public function set_review_answers($value)
    {
        $this->review_answers = $value;
        return $this;
    }

    /**
     * Get review_answers
     *
     * @return integer 
     */
    public function get_review_answers()
    {
        return $this->review_answers;
    }

    /**
     * Set random_by_category
     *
     * @param integer $value
     * @return Quiz
     */
    public function set_random_by_category($value)
    {
        $this->random_by_category = $value;
        return $this;
    }

    /**
     * Get random_by_category
     *
     * @return integer 
     */
    public function get_random_by_category()
    {
        return $this->random_by_category;
    }

    /**
     * Set text_when_finished
     *
     * @param text $value
     * @return Quiz
     */
    public function set_text_when_finished($value)
    {
        $this->text_when_finished = $value;
        return $this;
    }

    /**
     * Get text_when_finished
     *
     * @return text 
     */
    public function get_text_when_finished()
    {
        return $this->text_when_finished;
    }

    /**
     * Set display_category_name
     *
     * @param integer $value
     * @return Quiz
     */
    public function set_display_category_name($value)
    {
        $this->display_category_name = $value;
        return $this;
    }

    /**
     * Get display_category_name
     *
     * @return integer 
     */
    public function get_display_category_name()
    {
        return $this->display_category_name;
    }

    /**
     * Set pass_percentage
     *
     * @param integer $value
     * @return Quiz
     */
    public function set_pass_percentage($value)
    {
        $this->pass_percentage = $value;
        return $this;
    }

    /**
     * Get pass_percentage
     *
     * @return integer 
     */
    public function get_pass_percentage()
    {
        return $this->pass_percentage;
    }
}