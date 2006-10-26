<?php

/*
 * Created on 18 October 2006 by Elixir Interactive http://www.elixir-interactive.com
 */
 
ob_start(); 

$langFile = array ('registration', 'index','trad4all', 'tracking', 'admin');
$cidReset=true;
require ('../inc/global.inc.php');

$this_section = "session_my_space";

$nameTools= get_lang('Teachers');

api_block_anonymous_users();
$interbreadcrumb[] = array ("url" => "index.php", "name" => get_lang('MySpace'));
Display :: display_header($nameTools);

api_display_tool_title($nameTools);

$tbl_course = Database :: get_main_table(MAIN_COURSE_TABLE);
$tbl_user = Database :: get_main_table(MAIN_USER_TABLE);
$tbl_session = Database :: get_main_table(MAIN_SESSION_TABLE);
$tbl_session_course = Database :: get_main_table(MAIN_SESSION_COURSE_TABLE);
$tbl_session_rel_user = Database :: get_main_table(MAIN_SESSION_USER_TABLE);


 /*
 ===============================================================================
 	FUNCTION
 ===============================================================================  
 */

function exportCsv($a_header,$a_data)
 {
 	global $archiveDirName;

	$fileName = 'teachers.csv';
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
 * liste nominative avec coordonnées et lien vers les cours et
les stagiaires dont il est le
responsable. 
*/

$sqlFormateurs = "	SELECT user_id,lastname,firstname,email
					FROM $tbl_user
					WHERE status = 1
					ORDER BY lastname ASC
				  ";

$resultFormateurs = api_sql_query($sqlFormateurs);

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
			<th>
				'.get_lang('AdminCourses').'
			</th>
			<th>
				'.get_lang('Students').'
			</th>
		</tr>
  	 ';

$a_header[]=get_lang('Lastname');
$a_header[]=get_lang('Firstname');
$a_header[]=get_lang('Email');

$a_data=array();

if(mysql_num_rows($resultFormateurs)>0){
	
	$i=1;
	
	while($a_formateurs=mysql_fetch_array($resultFormateurs)){
		
		$i_user_id=$a_formateurs["user_id"];
		$s_lastname=$a_formateurs["lastname"];
		$s_firstname=$a_formateurs["firstname"];
		$s_email=$a_formateurs["email"];
		
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
				<th>
					'.get_lang('AdminCourses').'
				</th>
				<th>
					'.get_lang('Students').'
				</th>
			</tr>';
			}
			
		}
		else{
			$s_css_class="row_even";
		}
		
		$i++;
		
		$a_data[$i_user_id]["lastname"]=$s_lastname;
		$a_data[$i_user_id]["firstname"]=$s_firstname;
		$a_data[$i_user_id]["email"]=$s_email;
		
		echo '<tr class="'.$s_css_class.'"><td>'.$s_lastname.'</td><td>'.$s_firstname.'</td><td><a href="mailto:'.$s_email.'">'.$s_email.'</a></td><td><a href="cours.php?user_id='.$i_user_id.'">-></a></td><td><a href="myStudents.php?user_id='.$i_user_id.'">-></a></td></tr>';
		
	}
	
}

//No results
else{
	
	echo '<tr><td colspan="5" "align=center">'.get_lang("NoResults").'</td></tr>';
	
}

echo '</table>';


if(isset($_POST['export'])){
	
	exportCsv($a_header,$a_data);
	
}

echo "<br /><br />";
echo "<form method='post' action='teachers.php'>
		<input type='submit' name='export' value='".get_lang('exportExcel')."'/>
	  <form>";

/*
==============================================================================
	FOOTER
==============================================================================
*/

Display::display_footer();

?>
