<?php
require_once(api_get_path(LIBRARY_PATH).'social.lib.php');
require_once(api_get_path(LIBRARY_PATH).'usermanager.lib.php');
require_once(api_get_path(LIBRARY_PATH).'main_api.lib.php');

class TestSocial extends UnitTestCase{
	/*
	public $social;
	public function TestSocial(){

		$this->UnitTestCase('All main social function tests');
	}

	public function setUp(){
		$this->social = new SocialManager();
	}

	public function tearDown(){
		$this->social = null;
	}
	*/
	//@todo this function was moved to usermanager

	/**
	 * this function has been eliminated of this file, this functionality has been implemented in other file
	 */
/* DEPRECATED
	public function testRegisterFriend(){
		$friend_id=4;
		$my_user_id=1;
		$relation_type=2;
		$res= SocialManager::register_friend ($friend_id,$my_user_id,$relation_type);
		$this->assertTrue(is_null($res));
		$this->assertNull($res);
		//var_dump($res);
	}
	
	
//Remove or deprecate
 	public function testRemovedFriend(){
		$friend_id=4;
		$res = SocialManager::removed_friend($friend_id);
		$this->assertTrue(is_null($res));
		//var_dump($res);
	}
*/ 
	public function testShowListTypeFriends(){
		$res =SocialManager::show_list_type_friends();
		$this->assertTrue($res);
		$this->assertTrue(is_array($res));
		//var_dump($res);
	}

	public function testGetRelationTypeByName (){
		$relation_type_name=1;
		$res = SocialManager::get_relation_type_by_name($relation_type_name);
		if(is_null($res))
		{
			$this->assertNull($res);
			$this->assertTrue(is_null($res));
		} else 
		  {
			$this->assertTrue(is_numeric($res));
			$this->assertTrue($res);
		  }
		//var_dump($res);
	}

	public function testGetRelationBetweenContacts (){
		$user_id=1;
		$user_friend=3;
		$res =SocialManager::get_relation_between_contacts($user_id,$user_friend);
		if(is_numeric($res)){
			$this->assertFalse($res);
			$this->assertTrue(is_numeric($res));
		} else
		  {	
			$this->assertTrue(is_string($res));
			$this->assertTrue($res);
		  }
		//var_dump($res);

	}
/**
 * This function has been DEPRECATED AND REMOVED
 */
/*	DEPRECATED
	public function testGetListIdFriendsByUserId (){
		$user_id = 1;
		$id_group=3;
		$search_name='group';
		$res = SocialManager::get_friends_id($user_id,$id_group,$search_name);
		$this->assertTrue(is_array($res));
		//var_dump($res);
	}
*/
	 
	public function testGetListPathWebByUserId(){
		$user_id=1;
		$id_group=null;
		$search_name=null;
		$res = SocialManager::get_list_path_web_by_user_id($user_id,$id_group,$search_name);
		if(!($res===true)):
		$this->assertTrue(is_array($res));
		endif;
		//var_dump($res);
	}

	public function testGetListWebPathUserInvitationByUserId(){
		$user_id=1;
		$res = SocialManager::get_list_web_path_user_invitation_by_user_id($user_id);
		if(is_array($res))
		$this->assertTrue(is_array($res));
		else
		$this->assertTrue(is_null($res));
		//var_dump($res);
	}

	public function testSendInvitationFriend(){
		$user_id=1;
		$friend_id=3;
		$message_title='hello';
		$message_content='hola';
		$res = SocialManager::send_invitation_friend($user_id,$friend_id,$message_title,$message_content);
		if(!is_null($res)):
		$this->assertTrue(is_bool($res));
		$this->assertTrue(is_bool($res===true || $res === false));
		endif;
		//var_dump($res);
	}

	public function testGetMessageNumberInvitationByUserId(){
		$user_receiver_id=3;
		$res = SocialManager::get_message_number_invitation_by_user_id($user_receiver_id);
		if(is_string($res)):
		$this->assertTrue(is_numeric($res));
		endif;
		//var_dump($res);
	}

	public function testGetListInvitationOfFriendsByUserId(){
		$user_id=1;
		$res = SocialManager::get_list_invitation_of_friends_by_user_id($user_id);
		$this->assertTrue(is_array($res));
		//var_dump($res);
	}

	public function testInvitationAccepted(){
		$user_send_id=1;
		$user_receiver_id=3;
		$res = SocialManager::invitation_accepted($user_send_id,$user_receiver_id);
		$this->assertTrue(is_null($res));
		//var_dump($res);
	}

	public function testInvitationDenied(){
		$user_send_id=1;
		$user_receiver_id=3;
		$res = SocialManager::invitation_denied($user_send_id,$user_receiver_id);
		$this->assertTrue(is_null($res));
		//var_dump($res);
	}

	public function testQualifyFriend(){
		$id_friend_qualify=2;
		$type_qualify=1;
		$res = SocialManager::qualify_friend($id_friend_qualify,$type_qualify);
		$this->assertTrue(is_null($res));
		//var_dump($res);
	}

	public function testSendInvitationFriendUser(){
		global $_course, $charset;
		ob_start();
		$userfriend_id = 1;
		$subject_message = 'test';
		$content_message = 'this message is a test';
		$res = SocialManager::send_invitation_friend_user ($userfriend_id,$subject_message,$content_message);
		ob_end_clean();
		if(is_string($res)){
			$this->assertTrue($res);
			$this->assertTrue(is_string($res));
		} else {
			$this->assertTrue(is_bool($res));
			$this->assertTrue($res === false);
		}
		//var_dump($res);
	}
/*	
	public function TestDeleteCourse(){				
		$code = 'COURSEX';				
		$res = CourseManager::delete_course($code);			
		$path = api_get_path(SYS_PATH).'archive';		
		if ($handle = opendir($path)) {
			while (false !== ($file = readdir($handle))) {				
				if (strpos($file,$code)!==false) {										
					if (is_dir($path.'/'.$file)) {						
						rmdirr($path.'/'.$file);						
					}				
				}				
			}
			closedir($handle);
		}
	}
*/
}
?>
