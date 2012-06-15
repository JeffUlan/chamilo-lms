<?php

namespace Shibboleth;

/**
 * This file is autogenerated. Do not modifiy it.
 */

/**
 *
 * Model for table user 
 *
 * @license see /license.txt
 * @author Laurent Opprecht <laurent@opprecht.info>, Nicolas Rod for the University of Geneva
 */
class _User
{

    /**
    * Store for User objects. Interact with the database.
    *
    * @return UserStore 
    */
    public static function store()
    {
        static $result = false;
        if (empty($result))
        {
            $result = new UserStore();
        }
        return $result;
    }
        
    /**
     *
     * @return User 
     */
    public static function create($data = null)
    {
        return self::store()->create_object($data);
    }   

    public $user_id = null;    
    public $lastname = null;    
    public $firstname = null;    
    public $username = null;    
    public $password = null;    
    public $auth_source = null;    
    public $shibb_unique_id = null;    
    public $email = null;    
    public $status = null;    
    public $official_code = null;    
    public $phone = null;    
    public $picture_uri = null;    
    public $creator_id = null;    
    public $competences = null;    
    public $diplomas = null;    
    public $openarea = null;    
    public $teach = null;    
    public $productions = null;    
    public $chatcall_user_id = null;    
    public $chatcall_date = null;    
    public $chatcall_text = null;    
    public $language = null;    
    public $registration_date = null;    
    public $expiration_date = null;    
    public $active = null;    
    public $openid = null;    
    public $theme = null;    
    public $hr_dept_id = null;    
    public $shibb_persistent_id = null;    
 
    
    /**
     *
     * @return bool 
     */
    public function save()
    {
        return self::store()->save($this);
    }
    
}

/**
 * Store for User objects. Interact with the database.
 *
 * @copyright (c) 2012 University of Geneva
 * @license GNU General Public License - http://www.gnu.org/copyleft/gpl.html
 * @author Laurent Opprecht <laurent@opprecht.info>
 */
class _UserStore extends Store
{

    /**
     *
     * @return UserStore 
     */
    public static function instance()
    {
        static $result = false;
        if (empty($result))
        {
            $result = new self();
        }
        return $result;
    }
    
    public function __construct()
    {
        parent::__construct('user', '\Shibboleth\User', 'user_id');
    }
    
    /**
     *
     * @return User 
     */
    public function get($w)
    {
        $args = func_get_args();
        $f = array('parent', 'get');
        return call_user_func_array($f, $args);
    }    
    
    /**
     *
     * @return User 
     */
    public function create_object($data)
    {
        return parent::create_object($data);
    }    
    
    /**
     *
     * @return User 
     */
    public function get_by_user_id($value)
    {
        return $this->get(array('user_id' => $value));
    }    
    
    /**
     *
     * @return bool 
     */
    public function user_id_exists($value)
    {
        return $this->exist(array('user_id' => $value));
    }     
    
    /**
     *
     * @return bool 
     */
    public function delete_by_user_id($value)
    {
        return $this->delete(array('user_id' => $value));
    }    
    
    /**
     *
     * @return User 
     */
    public function get_by_username($value)
    {
        return $this->get(array('username' => $value));
    }    
    
    /**
     *
     * @return bool 
     */
    public function username_exists($value)
    {
        return $this->exist(array('username' => $value));
    }     
    
    /**
     *
     * @return bool 
     */
    public function delete_by_username($value)
    {
        return $this->delete(array('username' => $value));
    }    
     
}