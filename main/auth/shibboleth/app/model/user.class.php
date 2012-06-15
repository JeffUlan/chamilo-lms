<?php

namespace Shibboleth;

require_once dirname(__FILE__) . '/scaffold/user.class.php';

/**
 * A Chamilo user. Model for the User table.
 * 
 * Should be moved to the core. It only exists because it is not available through
 * the API.
 * 
 * The _User objet is generated by the scaffolder. User inherits from it to allow
 * modifications without touching the generated file. Don't modify _User as
 * it may change in the future. Instead add modifications to this class.
 * 
 * @license see /license.txt
 * @author Laurent Opprecht <laurent@opprecht.info>, Nicolas Rod for the University of Geneva
 */
class User extends _User
{
    
}

/**
 * Store for User objects. Interact with the database. Allows to save and retrieve
 * user objects. 
 * 
 * Should be moved to the core. It only exists because it is not available through
 * the API.
 *  
 * The _UserStore objet is generated by the scaffolder. This class inherits from it to allow
 * modifications without touching the generated file. Don't modify the _ object as
 * it may change in the future. Instead add modifications to this class.
 *
 * @copyright (c) 2012 University of Geneva
 * @license GNU General Public License - http://www.gnu.org/copyleft/gpl.html
 * @author Laurent Opprecht <laurent@opprecht.info>
 */
class UserStore extends _UserStore
{

    function __construct()
    {
        parent::__construct();
        ShibbolethUpgrade::update();
    }

    /**
     *
     * @param string $id
     * @return User
     */
    public function get_by_shibboleth_id($id)
    {
        return $this->get(array('shibb_unique_id' => $id));
    }
    
    public function shibboleth_id_exists($id)
    {
        return $this->exist(array('shibb_unique_id' => $id));
    }

    /**
     *
     * @param User $object 
     */
    protected function before_save($object)
    {
        $object->username = $object->username ? $object->username : $this->generate_username();
        $object->password = $object->password ? $object->password : api_generate_password();
        $object->language = $object->language ? $object->language : $this->default_language();
    }
    
    function default_language()
    {
        return api_get_setting('platformLanguage');
    }

    function generate_username()
    {
        $result = uniqid('s', true);
        $result = str_replace('.', '', $result);
        while ($this->username_exists($result))
        {
            $result = uniqid('s', true);
            $result = str_replace('.', '', $result);
        }
        return $result;
    }

}