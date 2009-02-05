<?php
$language_file = 'registration';
$cidReset = true;

require ('../inc/global.inc.php');
$this_section = SECTION_MYPROFILE;
$_SESSION['this_section']=$this_section;
$language_file = array('registration','messages');
require_once (api_get_path(CONFIGURATION_PATH).'profile.conf.php');
include_once (api_get_path(LIBRARY_PATH).'fileManage.lib.php');
include_once (api_get_path(LIBRARY_PATH).'fileUpload.lib.php');
include_once (api_get_path(LIBRARY_PATH).'image.lib.php');
require_once (api_get_path(LIBRARY_PATH).'usermanager.lib.php');
require_once '../inc/lib/social.lib.php';
$list_path_friends=array();
$list_groups=array();
?>
<div id="id" class="actions">
<?php echo get_lang('MessageInformationGroupsContacts') ?>
</div>
<?php
$user_id=api_get_user_id();
$list_groups=UserFriend::show_list_type_friends();
for ($p=0;$p<count($list_groups);$p++) {
	$list_path_friends=UserFriend::get_list_path_web_by_user_id ($user_id,$list_groups[$p]['id']);
?>
<div align="center" id="<?php echo 'div_group_'.$list_groups[$p]['id']; ?>" >
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td width="100%" height="20" valign="top">

        <table width="100%" border="0" cellpadding="0" cellspacing="0" class="social-title">
          <tr>
            <td width="100%" height="22" valign="top">&nbsp;&nbsp;<?php echo $list_groups[$p]['title']; ?></td>
              </tr>
        </table></td>
        </tr>
      <tr>
	<td valign="top">
<?php
$friend_html='';
$number_of_images=10;
$number_friends=0;
$list_friends_id=array();
$list_friends_dir=array();
$list_friends_file=array();
if (count($list_path_friends)!=0) { 
	for ($z=0;$z<count($list_path_friends['id_friend']);$z++) {
		$list_friends_id[]  = $list_path_friends['id_friend'][$z]['friend_user_id'];
		$list_friends_dir[] = $list_path_friends['path_friend'][$z]['dir'];
		$list_friends_file[]= $list_path_friends['path_friend'][$z]['file'];
	}
	$number_friends= count($list_friends_dir);
	$number_loop   = ($number_friends/$number_of_images);
	$loop_friends  = ceil($number_loop);
	$j=0;
	$friend_html.= '<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFC" style="border-left:1px #B8C8DC solid;border-bottom:1px #B8C8DC solid;border-right:1px #B8C8DC solid;">';		
	for ($k=0;$k<$loop_friends;$k++) {
		$friend_html.='<tr><td valign="top">';
		if ($j==$number_of_images) {
			$number_of_images=$number_of_images*2;
		}
		while ($j<$number_of_images) {
			if ($list_friends_file[$j]<>"") {
				$user_info=api_get_user_info($list_friends_id[$j]);
				$name_user=$user_info['firstName'].' '.$user_info['lastName'];
				$friend_html.='&nbsp;<div class="image-social-content" id=div_'.$list_friends_id[$j].' style="float:left" ><img src="'.$list_friends_dir[$j]."/".$list_friends_file[$j].'" width="90" height="110" style="margin-left:3px ;margin-rigth:3px;margin-top:10px;margin-bottom:3px;" id="imgfriend_'.$list_friends_id[$j].'" title="'.$name_user.'" onclick="qualify_friend(this)"/></div>&nbsp;';	
			}
			$j++;
		}
		$friend_html.='</td></tr>';
	}
	$friend_html.='<br/></table>';
	echo $friend_html; 
}  else {
	$friend_html.= '<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFC" style="border-left:1px #B8C8DC solid;border-bottom:1px #B8C8DC solid;border-right:1px #B8C8DC solid;">';		
	$friend_html.='<tr><td valign="top">&nbsp;&nbsp;&nbsp;'.get_lang('Message');
	$friend_html.='</td></tr>';
	$friend_html.='<br/></table>';
	echo $friend_html;
}
?>
        </td>
        </tr>
    </table></td>
  </tr>
</table><br/>
<?php
}
?>
</div>