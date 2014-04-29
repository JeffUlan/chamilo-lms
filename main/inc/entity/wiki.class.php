<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @license see /license.txt
 * @author autogenerated
 */
class Wiki extends \CourseEntity
{
    /**
     * @return \Entity\Repository\WikiRepository
     */
     public static function repository(){
        return \Entity\Repository\WikiRepository::instance();
    }

    /**
     * @return \Entity\Wiki
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
     * @var integer $page_id
     */
    protected $page_id;

    /**
     * @var string $reflink
     */
    protected $reflink;

    /**
     * @var string $title
     */
    protected $title;

    /**
     * @var text $content
     */
    protected $content;

    /**
     * @var integer $user_id
     */
    protected $user_id;

    /**
     * @var integer $group_id
     */
    protected $group_id;

    /**
     * @var datetime $dtime
     */
    protected $dtime;

    /**
     * @var integer $addlock
     */
    protected $addlock;

    /**
     * @var integer $editlock
     */
    protected $editlock;

    /**
     * @var integer $visibility
     */
    protected $visibility;

    /**
     * @var integer $addlock_disc
     */
    protected $addlock_disc;

    /**
     * @var integer $visibility_disc
     */
    protected $visibility_disc;

    /**
     * @var integer $ratinglock_disc
     */
    protected $ratinglock_disc;

    /**
     * @var integer $assignment
     */
    protected $assignment;

    /**
     * @var text $comment
     */
    protected $comment;

    /**
     * @var text $progress
     */
    protected $progress;

    /**
     * @var integer $score
     */
    protected $score;

    /**
     * @var integer $version
     */
    protected $version;

    /**
     * @var integer $is_editing
     */
    protected $is_editing;

    /**
     * @var datetime $time_edit
     */
    protected $time_edit;

    /**
     * @var integer $hits
     */
    protected $hits;

    /**
     * @var text $linksto
     */
    protected $linksto;

    /**
     * @var text $tag
     */
    protected $tag;

    /**
     * @var string $user_ip
     */
    protected $user_ip;

    /**
     * @var integer $session_id
     */
    protected $session_id;


    /**
     * Set c_id
     *
     * @param integer $value
     * @return Wiki
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
     * @return Wiki
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
     * Set page_id
     *
     * @param integer $value
     * @return Wiki
     */
    public function set_page_id($value)
    {
        $this->page_id = $value;
        return $this;
    }

    /**
     * Get page_id
     *
     * @return integer 
     */
    public function get_page_id()
    {
        return $this->page_id;
    }

    /**
     * Set reflink
     *
     * @param string $value
     * @return Wiki
     */
    public function set_reflink($value)
    {
        $this->reflink = $value;
        return $this;
    }

    /**
     * Get reflink
     *
     * @return string 
     */
    public function get_reflink()
    {
        return $this->reflink;
    }

    /**
     * Set title
     *
     * @param string $value
     * @return Wiki
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
     * Set content
     *
     * @param text $value
     * @return Wiki
     */
    public function set_content($value)
    {
        $this->content = $value;
        return $this;
    }

    /**
     * Get content
     *
     * @return text 
     */
    public function get_content()
    {
        return $this->content;
    }

    /**
     * Set user_id
     *
     * @param integer $value
     * @return Wiki
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
     * Set group_id
     *
     * @param integer $value
     * @return Wiki
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
     * Set dtime
     *
     * @param datetime $value
     * @return Wiki
     */
    public function set_dtime($value)
    {
        $this->dtime = $value;
        return $this;
    }

    /**
     * Get dtime
     *
     * @return datetime 
     */
    public function get_dtime()
    {
        return $this->dtime;
    }

    /**
     * Set addlock
     *
     * @param integer $value
     * @return Wiki
     */
    public function set_addlock($value)
    {
        $this->addlock = $value;
        return $this;
    }

    /**
     * Get addlock
     *
     * @return integer 
     */
    public function get_addlock()
    {
        return $this->addlock;
    }

    /**
     * Set editlock
     *
     * @param integer $value
     * @return Wiki
     */
    public function set_editlock($value)
    {
        $this->editlock = $value;
        return $this;
    }

    /**
     * Get editlock
     *
     * @return integer 
     */
    public function get_editlock()
    {
        return $this->editlock;
    }

    /**
     * Set visibility
     *
     * @param integer $value
     * @return Wiki
     */
    public function set_visibility($value)
    {
        $this->visibility = $value;
        return $this;
    }

    /**
     * Get visibility
     *
     * @return integer 
     */
    public function get_visibility()
    {
        return $this->visibility;
    }

    /**
     * Set addlock_disc
     *
     * @param integer $value
     * @return Wiki
     */
    public function set_addlock_disc($value)
    {
        $this->addlock_disc = $value;
        return $this;
    }

    /**
     * Get addlock_disc
     *
     * @return integer 
     */
    public function get_addlock_disc()
    {
        return $this->addlock_disc;
    }

    /**
     * Set visibility_disc
     *
     * @param integer $value
     * @return Wiki
     */
    public function set_visibility_disc($value)
    {
        $this->visibility_disc = $value;
        return $this;
    }

    /**
     * Get visibility_disc
     *
     * @return integer 
     */
    public function get_visibility_disc()
    {
        return $this->visibility_disc;
    }

    /**
     * Set ratinglock_disc
     *
     * @param integer $value
     * @return Wiki
     */
    public function set_ratinglock_disc($value)
    {
        $this->ratinglock_disc = $value;
        return $this;
    }

    /**
     * Get ratinglock_disc
     *
     * @return integer 
     */
    public function get_ratinglock_disc()
    {
        return $this->ratinglock_disc;
    }

    /**
     * Set assignment
     *
     * @param integer $value
     * @return Wiki
     */
    public function set_assignment($value)
    {
        $this->assignment = $value;
        return $this;
    }

    /**
     * Get assignment
     *
     * @return integer 
     */
    public function get_assignment()
    {
        return $this->assignment;
    }

    /**
     * Set comment
     *
     * @param text $value
     * @return Wiki
     */
    public function set_comment($value)
    {
        $this->comment = $value;
        return $this;
    }

    /**
     * Get comment
     *
     * @return text 
     */
    public function get_comment()
    {
        return $this->comment;
    }

    /**
     * Set progress
     *
     * @param text $value
     * @return Wiki
     */
    public function set_progress($value)
    {
        $this->progress = $value;
        return $this;
    }

    /**
     * Get progress
     *
     * @return text 
     */
    public function get_progress()
    {
        return $this->progress;
    }

    /**
     * Set score
     *
     * @param integer $value
     * @return Wiki
     */
    public function set_score($value)
    {
        $this->score = $value;
        return $this;
    }

    /**
     * Get score
     *
     * @return integer 
     */
    public function get_score()
    {
        return $this->score;
    }

    /**
     * Set version
     *
     * @param integer $value
     * @return Wiki
     */
    public function set_version($value)
    {
        $this->version = $value;
        return $this;
    }

    /**
     * Get version
     *
     * @return integer 
     */
    public function get_version()
    {
        return $this->version;
    }

    /**
     * Set is_editing
     *
     * @param integer $value
     * @return Wiki
     */
    public function set_is_editing($value)
    {
        $this->is_editing = $value;
        return $this;
    }

    /**
     * Get is_editing
     *
     * @return integer 
     */
    public function get_is_editing()
    {
        return $this->is_editing;
    }

    /**
     * Set time_edit
     *
     * @param datetime $value
     * @return Wiki
     */
    public function set_time_edit($value)
    {
        $this->time_edit = $value;
        return $this;
    }

    /**
     * Get time_edit
     *
     * @return datetime 
     */
    public function get_time_edit()
    {
        return $this->time_edit;
    }

    /**
     * Set hits
     *
     * @param integer $value
     * @return Wiki
     */
    public function set_hits($value)
    {
        $this->hits = $value;
        return $this;
    }

    /**
     * Get hits
     *
     * @return integer 
     */
    public function get_hits()
    {
        return $this->hits;
    }

    /**
     * Set linksto
     *
     * @param text $value
     * @return Wiki
     */
    public function set_linksto($value)
    {
        $this->linksto = $value;
        return $this;
    }

    /**
     * Get linksto
     *
     * @return text 
     */
    public function get_linksto()
    {
        return $this->linksto;
    }

    /**
     * Set tag
     *
     * @param text $value
     * @return Wiki
     */
    public function set_tag($value)
    {
        $this->tag = $value;
        return $this;
    }

    /**
     * Get tag
     *
     * @return text 
     */
    public function get_tag()
    {
        return $this->tag;
    }

    /**
     * Set user_ip
     *
     * @param string $value
     * @return Wiki
     */
    public function set_user_ip($value)
    {
        $this->user_ip = $value;
        return $this;
    }

    /**
     * Get user_ip
     *
     * @return string 
     */
    public function get_user_ip()
    {
        return $this->user_ip;
    }

    /**
     * Set session_id
     *
     * @param integer $value
     * @return Wiki
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