<?php
/* For licensing terms, see /chamilo_license.txt */

/**
==============================================================================
*	This script displays the footer that is below (almost)
*	every Chamilo web page.
*
*	@package dokeos.include
==============================================================================
*/

/**** display of tool_navigation_menu according to admin setting *****/
require_once (api_get_path(LIBRARY_PATH).'course.lib.php');

if (api_get_setting('show_navigation_menu') != 'false') {

   $course_id = api_get_course_id();
   if (!empty($course_id) && ($course_id != -1)) {
   		if ( api_get_setting('show_navigation_menu') != 'icons') {
	    	echo '</div> <!-- end #center -->';
    		echo '</div> <!-- end #centerwrap -->';
		}
      	require_once api_get_path(INCLUDE_PATH).'tool_navigation_menu.inc.php';
      	show_navigation_menu();
   }
}
/***********************************************************************/
?>
 <div class="clear">&nbsp;</div> <!-- 'clearing' div to make sure that footer stays below the main and right column sections -->
</div> <!-- end of #main" started at the end of banner.inc.php -->

<div class="push"></div>
</div> <!-- end of #wrapper section -->

<div id="footer"> <!-- start of #footer section -->
<div id="bottom_corner"></div>
<div class="copyright">
<?php
global $_configuration;
if (api_get_setting('show_administrator_data')=='true') {
	// Platform manager
	echo '<div align="right">', get_lang('Manager'), ' : ', Display::encrypted_mailto_link(api_get_setting('emailAdministrator'), api_get_person_name(api_get_setting('administratorName'), api_get_setting('administratorSurname'))).'</div>';
}

echo get_lang("Platform"), ' <a href="http://www.chamilo.org" target="_blank">Chamilo ', $_configuration['dokeos_version'], '</a> &copy; ', date('Y');
// Server mode indicator.
if (api_is_platform_admin()) {
	if (api_get_setting('server_type') == 'test') {
		echo ' <a href="'.api_get_path(WEB_CODE_PATH).'admin/settings.php?category=Platform#server_type">';
		echo '<span style="background-color: white; color: red; border: 1px solid red;">&nbsp;Test&nbsp;server&nbsp;mode&nbsp;</span></a>';
	}
}
?>
</div>

<?php
/*
-----------------------------------------------------------------------------
	Plugins for footer section
-----------------------------------------------------------------------------
*/
api_plugin('footer');

echo '<div class="footer_emails">';

if (api_get_setting('show_tutor_data')=='true') {
	// course manager
	$id_course=api_get_course_id();
	$id_session=api_get_session_id();
	if (isset($id_course) && $id_course!=-1) {
		echo '<span id="platformmanager">';
		if ($id_session!=0){
			$coachs_email=CourseManager::get_email_of_tutor_to_session($id_session,$id_course);
			$email_link = array();
			foreach ($coachs_email as $coach_email) {				
				foreach ($coach_email as $email=>$username) {
					$email_link[] = Display::encrypted_mailto_link($email,$username);
				}
			}	
			if (count($coachs_email)>1){
				$bar='<br />';
				echo get_lang('Coachs').' : <ul>';
				echo '<li>'.implode("<li>",$email_link);
			}  elseif(count($coachs_email)==1) {
				echo get_lang('Coach').' : ';
				echo implode("&nbps;",$email_link);
			}  elseif(count($coachs_email)==0) {
				echo '';
			} 							
		}
		echo '</ul></span>';
	}
	echo '<br>';
}

$class='';

if (api_get_setting('show_teacher_data')=='true') {	
	if (api_get_setting('show_tutor_data')=='false'){
		$class='platformmanager';
	} else {
		$class='coursemanager';
	}
	// course manager
	$id_course=api_get_course_id();
	if (isset($id_course) && $id_course!=-1) {
		echo '<span id="'.$class.'">';
		$mail=CourseManager::get_emails_of_tutors_to_course($id_course);
		if (!empty($mail)) {
			if (count($mail)>1){
				echo get_lang('Teachers').' : <ul>';
				foreach ($mail as $value=>$key) {
					foreach ($key as $email=>$name){
						echo '<li>'.Display::encrypted_mailto_link($email,$name).'</li>';
					}
				}
				echo '</ul>';
			} else {
				echo get_lang('Teacher').' : ';
				foreach ($mail as $value=>$key) {
					foreach ($key as $email=>$name){
						echo Display::encrypted_mailto_link($email,$name).'<br />';
					}
				}
			}

		}

		echo '</span>';
	}
}
echo '</div>';

?>&nbsp;
</div> <!-- end of #footer -->
</body>
</html>