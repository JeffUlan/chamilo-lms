<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @license see /license.txt
 * @author autogenerated
 */
class Survey extends \CourseEntity
{
    /**
     * @return \Entity\Repository\SurveyRepository
     */
     public static function repository(){
        return \Entity\Repository\SurveyRepository::instance();
    }

    /**
     * @return \Entity\Survey
     */
     public static function create(){
        return new self();
    }

    /**
     * @var integer $c_id
     */
    protected $c_id;

    /**
     * @var integer $survey_id
     */
    protected $survey_id;

    /**
     * @var string $code
     */
    protected $code;

    /**
     * @var text $title
     */
    protected $title;

    /**
     * @var text $subtitle
     */
    protected $subtitle;

    /**
     * @var string $author
     */
    protected $author;

    /**
     * @var string $lang
     */
    protected $lang;

    /**
     * @var date $avail_from
     */
    protected $avail_from;

    /**
     * @var date $avail_till
     */
    protected $avail_till;

    /**
     * @var string $is_shared
     */
    protected $is_shared;

    /**
     * @var string $template
     */
    protected $template;

    /**
     * @var text $intro
     */
    protected $intro;

    /**
     * @var text $surveythanks
     */
    protected $surveythanks;

    /**
     * @var datetime $creation_date
     */
    protected $creation_date;

    /**
     * @var integer $invited
     */
    protected $invited;

    /**
     * @var integer $answered
     */
    protected $answered;

    /**
     * @var text $invite_mail
     */
    protected $invite_mail;

    /**
     * @var text $reminder_mail
     */
    protected $reminder_mail;

    /**
     * @var string $mail_subject
     */
    protected $mail_subject;

    /**
     * @var string $anonymous
     */
    protected $anonymous;

    /**
     * @var text $access_condition
     */
    protected $access_condition;

    /**
     * @var boolean $shuffle
     */
    protected $shuffle;

    /**
     * @var boolean $one_question_per_page
     */
    protected $one_question_per_page;

    /**
     * @var string $survey_version
     */
    protected $survey_version;

    /**
     * @var integer $parent_id
     */
    protected $parent_id;

    /**
     * @var integer $survey_type
     */
    protected $survey_type;

    /**
     * @var integer $show_form_profile
     */
    protected $show_form_profile;

    /**
     * @var text $form_fields
     */
    protected $form_fields;

    /**
     * @var integer $session_id
     */
    protected $session_id;


    /**
     * Set c_id
     *
     * @param integer $value
     * @return Survey
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
     * Set survey_id
     *
     * @param integer $value
     * @return Survey
     */
    public function set_survey_id($value)
    {
        $this->survey_id = $value;
        return $this;
    }

    /**
     * Get survey_id
     *
     * @return integer 
     */
    public function get_survey_id()
    {
        return $this->survey_id;
    }

    /**
     * Set code
     *
     * @param string $value
     * @return Survey
     */
    public function set_code($value)
    {
        $this->code = $value;
        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function get_code()
    {
        return $this->code;
    }

    /**
     * Set title
     *
     * @param text $value
     * @return Survey
     */
    public function set_title($value)
    {
        $this->title = $value;
        return $this;
    }

    /**
     * Get title
     *
     * @return text 
     */
    public function get_title()
    {
        return $this->title;
    }

    /**
     * Set subtitle
     *
     * @param text $value
     * @return Survey
     */
    public function set_subtitle($value)
    {
        $this->subtitle = $value;
        return $this;
    }

    /**
     * Get subtitle
     *
     * @return text 
     */
    public function get_subtitle()
    {
        return $this->subtitle;
    }

    /**
     * Set author
     *
     * @param string $value
     * @return Survey
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
     * Set lang
     *
     * @param string $value
     * @return Survey
     */
    public function set_lang($value)
    {
        $this->lang = $value;
        return $this;
    }

    /**
     * Get lang
     *
     * @return string 
     */
    public function get_lang()
    {
        return $this->lang;
    }

    /**
     * Set avail_from
     *
     * @param date $value
     * @return Survey
     */
    public function set_avail_from($value)
    {
        $this->avail_from = $value;
        return $this;
    }

    /**
     * Get avail_from
     *
     * @return date 
     */
    public function get_avail_from()
    {
        return $this->avail_from;
    }

    /**
     * Set avail_till
     *
     * @param date $value
     * @return Survey
     */
    public function set_avail_till($value)
    {
        $this->avail_till = $value;
        return $this;
    }

    /**
     * Get avail_till
     *
     * @return date 
     */
    public function get_avail_till()
    {
        return $this->avail_till;
    }

    /**
     * Set is_shared
     *
     * @param string $value
     * @return Survey
     */
    public function set_is_shared($value)
    {
        $this->is_shared = $value;
        return $this;
    }

    /**
     * Get is_shared
     *
     * @return string 
     */
    public function get_is_shared()
    {
        return $this->is_shared;
    }

    /**
     * Set template
     *
     * @param string $value
     * @return Survey
     */
    public function set_template($value)
    {
        $this->template = $value;
        return $this;
    }

    /**
     * Get template
     *
     * @return string 
     */
    public function get_template()
    {
        return $this->template;
    }

    /**
     * Set intro
     *
     * @param text $value
     * @return Survey
     */
    public function set_intro($value)
    {
        $this->intro = $value;
        return $this;
    }

    /**
     * Get intro
     *
     * @return text 
     */
    public function get_intro()
    {
        return $this->intro;
    }

    /**
     * Set surveythanks
     *
     * @param text $value
     * @return Survey
     */
    public function set_surveythanks($value)
    {
        $this->surveythanks = $value;
        return $this;
    }

    /**
     * Get surveythanks
     *
     * @return text 
     */
    public function get_surveythanks()
    {
        return $this->surveythanks;
    }

    /**
     * Set creation_date
     *
     * @param datetime $value
     * @return Survey
     */
    public function set_creation_date($value)
    {
        $this->creation_date = $value;
        return $this;
    }

    /**
     * Get creation_date
     *
     * @return datetime 
     */
    public function get_creation_date()
    {
        return $this->creation_date;
    }

    /**
     * Set invited
     *
     * @param integer $value
     * @return Survey
     */
    public function set_invited($value)
    {
        $this->invited = $value;
        return $this;
    }

    /**
     * Get invited
     *
     * @return integer 
     */
    public function get_invited()
    {
        return $this->invited;
    }

    /**
     * Set answered
     *
     * @param integer $value
     * @return Survey
     */
    public function set_answered($value)
    {
        $this->answered = $value;
        return $this;
    }

    /**
     * Get answered
     *
     * @return integer 
     */
    public function get_answered()
    {
        return $this->answered;
    }

    /**
     * Set invite_mail
     *
     * @param text $value
     * @return Survey
     */
    public function set_invite_mail($value)
    {
        $this->invite_mail = $value;
        return $this;
    }

    /**
     * Get invite_mail
     *
     * @return text 
     */
    public function get_invite_mail()
    {
        return $this->invite_mail;
    }

    /**
     * Set reminder_mail
     *
     * @param text $value
     * @return Survey
     */
    public function set_reminder_mail($value)
    {
        $this->reminder_mail = $value;
        return $this;
    }

    /**
     * Get reminder_mail
     *
     * @return text 
     */
    public function get_reminder_mail()
    {
        return $this->reminder_mail;
    }

    /**
     * Set mail_subject
     *
     * @param string $value
     * @return Survey
     */
    public function set_mail_subject($value)
    {
        $this->mail_subject = $value;
        return $this;
    }

    /**
     * Get mail_subject
     *
     * @return string 
     */
    public function get_mail_subject()
    {
        return $this->mail_subject;
    }

    /**
     * Set anonymous
     *
     * @param string $value
     * @return Survey
     */
    public function set_anonymous($value)
    {
        $this->anonymous = $value;
        return $this;
    }

    /**
     * Get anonymous
     *
     * @return string 
     */
    public function get_anonymous()
    {
        return $this->anonymous;
    }

    /**
     * Set access_condition
     *
     * @param text $value
     * @return Survey
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
     * Set shuffle
     *
     * @param boolean $value
     * @return Survey
     */
    public function set_shuffle($value)
    {
        $this->shuffle = $value;
        return $this;
    }

    /**
     * Get shuffle
     *
     * @return boolean 
     */
    public function get_shuffle()
    {
        return $this->shuffle;
    }

    /**
     * Set one_question_per_page
     *
     * @param boolean $value
     * @return Survey
     */
    public function set_one_question_per_page($value)
    {
        $this->one_question_per_page = $value;
        return $this;
    }

    /**
     * Get one_question_per_page
     *
     * @return boolean 
     */
    public function get_one_question_per_page()
    {
        return $this->one_question_per_page;
    }

    /**
     * Set survey_version
     *
     * @param string $value
     * @return Survey
     */
    public function set_survey_version($value)
    {
        $this->survey_version = $value;
        return $this;
    }

    /**
     * Get survey_version
     *
     * @return string 
     */
    public function get_survey_version()
    {
        return $this->survey_version;
    }

    /**
     * Set parent_id
     *
     * @param integer $value
     * @return Survey
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
     * Set survey_type
     *
     * @param integer $value
     * @return Survey
     */
    public function set_survey_type($value)
    {
        $this->survey_type = $value;
        return $this;
    }

    /**
     * Get survey_type
     *
     * @return integer 
     */
    public function get_survey_type()
    {
        return $this->survey_type;
    }

    /**
     * Set show_form_profile
     *
     * @param integer $value
     * @return Survey
     */
    public function set_show_form_profile($value)
    {
        $this->show_form_profile = $value;
        return $this;
    }

    /**
     * Get show_form_profile
     *
     * @return integer 
     */
    public function get_show_form_profile()
    {
        return $this->show_form_profile;
    }

    /**
     * Set form_fields
     *
     * @param text $value
     * @return Survey
     */
    public function set_form_fields($value)
    {
        $this->form_fields = $value;
        return $this;
    }

    /**
     * Get form_fields
     *
     * @return text 
     */
    public function get_form_fields()
    {
        return $this->form_fields;
    }

    /**
     * Set session_id
     *
     * @param integer $value
     * @return Survey
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
}