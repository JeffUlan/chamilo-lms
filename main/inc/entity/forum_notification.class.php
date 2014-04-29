<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @license see /license.txt
 * @author autogenerated
 */
class ForumNotification extends \CourseEntity
{
    /**
     * @return \Entity\Repository\ForumNotificationRepository
     */
     public static function repository(){
        return \Entity\Repository\ForumNotificationRepository::instance();
    }

    /**
     * @return \Entity\ForumNotification
     */
     public static function create(){
        return new self();
    }

    /**
     * @var integer $id
     */
    protected $id;

    /**
     * @var integer $c_id
     */
    protected $c_id;

    /**
     * @var integer $user_id
     */
    protected $user_id;

    /**
     * @var integer $forum_id
     */
    protected $forum_id;

    /**
     * @var integer $thread_id
     */
    protected $thread_id;

    /**
     * @var integer $post_id
     */
    protected $post_id;


    /**
     * Set id
     *
     * @param integer $value
     * @return ForumNotification
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
     * Set c_id
     *
     * @param integer $value
     * @return ForumNotification
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
     * Set user_id
     *
     * @param integer $value
     * @return ForumNotification
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
     * Set forum_id
     *
     * @param integer $value
     * @return ForumNotification
     */
    public function set_forum_id($value)
    {
        $this->forum_id = $value;
        return $this;
    }

    /**
     * Get forum_id
     *
     * @return integer 
     */
    public function get_forum_id()
    {
        return $this->forum_id;
    }

    /**
     * Set thread_id
     *
     * @param integer $value
     * @return ForumNotification
     */
    public function set_thread_id($value)
    {
        $this->thread_id = $value;
        return $this;
    }

    /**
     * Get thread_id
     *
     * @return integer 
     */
    public function get_thread_id()
    {
        return $this->thread_id;
    }

    /**
     * Set post_id
     *
     * @param integer $value
     * @return ForumNotification
     */
    public function set_post_id($value)
    {
        $this->post_id = $value;
        return $this;
    }

    /**
     * Get post_id
     *
     * @return integer 
     */
    public function get_post_id()
    {
        return $this->post_id;
    }
}