<?php

ob_start();

/*
 * Created on 18 October 2006 by Elixir Interactive http://www.elixir-interactive.com
 */
$langFile = array ('registration', 'index','trad4all', 'tracking');
$cidReset=true;
require ('../inc/global.inc.php');

$this_section = "session_my_space";

$nameTools= get_lang('Administrators');

api_block_anonymous_users();
$interbreadcrumb[] = array ("url" => "index.php", "name" => get_lang('MySpace'));
Display :: display_header($nameTools);

api_display_tool_title($nameTools);

// Database Table Definitions
$tbl_course 			= Database :: get_main_table(TABLE_MAIN_COURSE);
$tbl_user 				= Database :: get_main_table(TABLE_MAIN_USER);
$tbl_session 			= Database :: get_main_table(TABLE_MAIN_SESSION);
$tbl_session_course 	= Database :: get_main_table(TABLE_MAIN_SESSION_COURSE);
$tbl_session_rel_user 	= Database :: get_main_table(TABLE_MAIN_SESSION_USER);
$tbl_admin				= Database :: get_main_table(TABLE_MAIN_ADMIN);

/*
 ===============================================================================
 	FUNCTION
 ===============================================================================  
 */
 
 function exportCsv($a_header,$a_data)
 {
 	global $archiveDirName;

	$fileName = 'administrators.csv';
	$archivePath = api_get_path(SYS_PATH).$archiveDirName.'/';
	$archiveURL = api_get_path(WEB_CODE_PATH).'course_info/download.php?archive=';
	
	if(!$open = fopen($archivePath.$fileName,'w+'))
	{
		$message = get_lang('noOpen');
	}
	else
	{
		$info = '';
		
		foreach($a_header as $header)
		{
			$info .= $header.';';
		}
		$info .= "\r\n";
		
		
		foreach($a_data as $data)
		{
			foreach($data as $infos)
			{
				$info .= $infos.';';
			}
			$info .= "\r\n";
		}
		
		fwrite($open,$info);
		fclose($open);
		chmod($fileName,0777);
		
		header("Location:".$archiveURL.$fileName);
	}
	
	return $message;
 }


/**
 * MAIN PART
 */

/*
 * liste nominative avec coordonn�es et lien vers les cours et
les stagiaires dont il est le
responsable. 
 */

$sqlAdmins = "	SELECT user.user_id,lastname,firstname,email
					FROM $tbl_user as user, $tbl_admin as admin
					WHERE admin.user_id=user.user_id
					ORDER BY lastname ASC
				  ";

$resultAdmins = api_sql_query($sqlAdmins);

echo '<table class="data_table">
	 	<tr>
			<th>
				'.get_lang('Lastname').'
			</th>
			<th>
				'.get_lang('Firstname').'
			</th>
			<th>
				'.get_lang('Email').'
			</th>
		</tr>
  	 ';

$a_header[]=get_lang('Lastname');
$a_header[]=get_lang('Firstname');
$a_header[]=get_lang('Email');

if(mysql_num_rows($resultAdmins)>0){
	
	while($a_admins=mysql_fetch_array($resultAdmins)){
		
		$i_user_id=$a_admins["user_id"];
		$s_lastname=$a_admins["lastname"];
		$s_firstname=$a_admins["firstname"];
		$s_email=$a_admins["email"];
		
		if($i%2==0){
			$s_css_class="row_odd";
			
			if($i%20==0 && $i!=0){
				echo '<tr>
				<th>
					'.get_lang('Lastname').'
				</th>
				<th>
					'.get_lang('Firstname').'
				</th>
				<th>
					'.get_lang('Email').'
				</th>
			</tr>';
			}
			
		}
		else{
			$s_css_class="row_even";
		}
		
		$i++;
		
		echo "<tr class=".$s_css_class."><td>$s_lastname</td><td>$s_firstname</td><td><a href='mailto:".$s_email."'>$s_email</a></td></tr>";
		
		$a_data[$i_user_id]["lastname"]=$s_lastname;
		$a_data[$i_user_id]["firstname"]=$s_firstname;
		$a_data[$i_user_id]["email"]=$s_email;
		
	}
	
}

//No results
else{
	
	echo '<tr><td colspan="3" "align=center">'.get_lang("NoResults").'</td></tr>';
	
}

echo '</table>';


if(isset($_POST['export'])){
	
	exportCsv($a_header,$a_data);
	
}

echo "<br /><br />";
echo "<form method='post' action='admin.php'>
		<input type='submit' name='export' value='".get_lang('exportExcel')."'/>
	  <form>";

/*
==============================================================================
	FOOTER
==============================================================================
*/

Display::display_footer();
?>
