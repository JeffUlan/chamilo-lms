<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @license see /license.txt
 * @author autogenerated
 */
class QuizQuestionRelCategory extends \CourseEntity
{
    /**
     * @return \Entity\Repository\QuizQuestionRelCategoryRepository
     */
     public static function repository(){
        return \Entity\Repository\QuizQuestionRelCategoryRepository::instance();
    }

    /**
     * @return \Entity\QuizQuestionRelCategory
     */
     public static function create(){
        return new self();
    }

    /**
     * @var integer $c_id
     */
    protected $c_id;

    /**
     * @var integer $question_id
     */
    protected $question_id;

    /**
     * @var integer $category_id
     */
    protected $category_id;


    /**
     * Set c_id
     *
     * @param integer $value
     * @return QuizQuestionRelCategory
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
     * Set question_id
     *
     * @param integer $value
     * @return QuizQuestionRelCategory
     */
    public function set_question_id($value)
    {
        $this->question_id = $value;
        return $this;
    }

    /**
     * Get question_id
     *
     * @return integer 
     */
    public function get_question_id()
    {
        return $this->question_id;
    }

    /**
     * Set category_id
     *
     * @param integer $value
     * @return QuizQuestionRelCategory
     */
    public function set_category_id($value)
    {
        $this->category_id = $value;
        return $this;
    }

    /**
     * Get category_id
     *
     * @return integer 
     */
    public function get_category_id()
    {
        return $this->category_id;
    }
}