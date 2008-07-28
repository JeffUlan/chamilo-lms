<?php
/*
    DOKEOS - elearning and course management software

    For a full list of contributors, see documentation/credits.html

    This program is free software; you can redistribute it and/or
    modify it under the terms of the GNU General Public License
    as published by the Free Software Foundation; either version 2
    of the License, or (at your option) any later version.
    See "documentation/licence.html" more details.

    Contact:
		Dokeos
		Rue du Corbeau, 108
		B-1030 Brussels - Belgium
		info@dokeos.com
*/


/**
*	Exercise list: This script shows the list of exercises for administrators and students.
*	@package dokeos.exercise
*	@author Olivier Brouckaert, original author
*	@author Denes Nagy, HotPotatoes integration
*	@author Wolfgang Schneider, code/html cleanup
* 	@version $Id:exercice.php 12269 2007-05-03 14:17:37Z elixir_julian $
*/


// name of the language file that needs to be included
$language_file='exercice';

require_once('../inc/global.inc.php');
$this_section=SECTION_COURSES;
api_protect_course_script(true);

$show=(isset($_GET['show']) && $_GET['show'] == 'result')?'result':'test'; // moved down to fix bug: http://www.dokeos.com/forum/viewtopic.php?p=18609#18609

/*
-----------------------------------------------------------
	Libraries
-----------------------------------------------------------
*/
require_once('exercise.class.php');
require_once('question.class.php');
require_once('answer.class.php');
require_once(api_get_path(LIBRARY_PATH).'fileManage.lib.php');
require_once(api_get_path(LIBRARY_PATH).'fileUpload.lib.php');
require_once('hotpotatoes.lib.php');
require_once(api_get_path(LIBRARY_PATH).'document.lib.php');
include(api_get_path(LIBRARY_PATH).'mail.lib.inc.php');
include(api_get_path(LIBRARY_PATH).'usermanager.lib.php');

/*
-----------------------------------------------------------
	Constants and variables
-----------------------------------------------------------
*/
$is_allowedToEdit = api_is_allowed_to_edit();
$is_tutor = api_is_allowed_to_edit(true);

$TBL_USER          	    = Database::get_main_table(TABLE_MAIN_USER);
$TBL_DOCUMENT          	= Database::get_course_table(TABLE_DOCUMENT);
$TBL_ITEM_PROPERTY      = Database::get_course_table(TABLE_ITEM_PROPERTY);
$TBL_EXERCICE_QUESTION	= Database::get_course_table(TABLE_QUIZ_TEST_QUESTION);
$TBL_EXERCICES			= Database::get_course_table(TABLE_QUIZ_TEST);
$TBL_QUESTIONS			= Database::get_course_table(TABLE_QUIZ_QUESTION);
$TBL_TRACK_EXERCICES	= Database::get_statistic_table(TABLE_STATISTIC_TRACK_E_EXERCICES);
$TBL_TRACK_HOTPOTATOES	= Database::get_statistic_table(TABLE_STATISTIC_TRACK_E_HOTPOTATOES);
$TBL_TRACK_ATTEMPT		= Database::get_statistic_table(TABLE_STATISTIC_TRACK_E_ATTEMPT);

// document path
$documentPath= api_get_path(SYS_COURSE_PATH).$_course['path']."/document";
// picture path
$picturePath=$documentPath.'/images';
// audio path
$audioPath=$documentPath.'/audio';

// hotpotatoes
$uploadPath = DIR_HOTPOTATOES; //defined in main_api
$exercicePath = api_get_self();
$exfile = explode('/',$exercicePath);
$exfile = strtolower($exfile[sizeof($exfile)-1]);
$exercicePath = substr($exercicePath,0,strpos($exercicePath,$exfile));
$exercicePath = $exercicePath."exercice.php";


// maximum number of exercises on a same page
$limitExPage=50;

// Clear the exercise session
if(isset($_SESSION['objExercise']))		{ api_session_unregister('objExercise');		}
if(isset($_SESSION['objQuestion']))		{ api_session_unregister('objQuestion');		}
if(isset($_SESSION['objAnswer']))		{ api_session_unregister('objAnswer');		}
if(isset($_SESSION['questionList']))	{ api_session_unregister('questionList');	}
if(isset($_SESSION['exerciseResult']))	{ api_session_unregister('exerciseResult');	}

//general POST/GET/SESSION/COOKIES parameters recovery
if ( empty ( $origin ) ) {
    $origin     = $_REQUEST['origin'];
}
if ( empty ($choice ) ) {
    $choice     = $_REQUEST['choice'];
}
if ( empty ( $hpchoice ) ) {
    $hpchoice   = $_REQUEST['hpchoice'];
}
if ( empty ($exerciseId ) ) {
    $exerciseId = Database::escape_string($_REQUEST['exerciseId']);
}
if ( empty ( $file ) ) {
    $file   = Database::escape_string($_REQUEST['file']);
}
$learnpath_id = Database::escape_string($_REQUEST['learnpath_id']);
$learnpath_item_id = Database::escape_string($_REQUEST['learnpath_item_id']);
$page = Database::escape_string($_REQUEST['page']);

if($origin == 'learnpath'){
	$show = 'result';
}
$htmlHeadXtra[]='<style type="text/css">
<!--
a.invisible
{
	color: #999999;
}

a.invisible:visited
{
	color: #999999;
}

a.invisible:active
{
	color: #999999;
}

a.invisible:hover
{
	color: #999999;
}
-->
</style>';

if($_GET['delete']=='delete' && ($is_allowedToEdit || api_is_coach()) && !empty($_GET['did'])){
	$sql='DELETE FROM '.Database :: get_statistic_table(TABLE_STATISTIC_TRACK_E_EXERCICES).' WHERE exe_id = '.(int)$_GET['did'];
	api_sql_query ($sql,__FILE__,__LINE__); 	
	header('Location: exercice.php?cidReq='.htmlentities($_GET['cidReq']).'&show=result');
	exit;
}


if ($show=='result' && $_REQUEST['comments']=='update' && ($is_allowedToEdit || $is_tutor))
{
	$id  = $_GET['exeid'];
	$emailid = $_GET['emailid'];
	$test  = $_GET['test'];
	$from = $_SESSION['_user']['mail'];
	$from_name = $_SESSION['_user']['firstName']." ".$_SESSION['_user']['lastName'];
	$url = api_get_path(WEB_CODE_PATH).'exercice/exercice.php?'.api_get_cidreq().'&show=result';

	foreach ($_POST as $key=>$v)
	{
		$keyexp = explode('_',$key);
		if ($keyexp[0] == "marks")
		{
			$sql = "SELECT question from $TBL_QUESTIONS where id = '".Database::escape_string($keyexp[1])."'";
			$result =api_sql_query($sql, __FILE__, __LINE__);
			$ques_name = mysql_result($result,0,"question");

			$query = "UPDATE $TBL_TRACK_ATTEMPT SET marks = '".Database::escape_string($v)."' 
						WHERE question_id = '".Database::escape_string($keyexp[1])."' 
						AND exe_id='".Database::escape_string($id)."'";
			api_sql_query($query, __FILE__, __LINE__);

			$qry = 'SELECT sum(marks) as tot
					FROM '.$TBL_TRACK_ATTEMPT.' where exe_id = '.intval($id).'
					GROUP BY question_id';

			$res = api_sql_query($qry,__FILE__,__LINE__);
			$tot = mysql_result($res,0,'tot');

			$totquery = "update $TBL_TRACK_EXERCICES set exe_result = '".Database::escape_string($tot)."' where exe_Id='".Database::escape_string($id)."'";
			api_sql_query($totquery, __FILE__, __LINE__);

		}
		else
		{
		  $query = "UPDATE $TBL_TRACK_ATTEMPT SET teacher_comment = '".Database::escape_string($v)."' 
		  			WHERE question_id = '".Database::escape_string($keyexp[1])."' 
		  			AND exe_id = '".Database::escape_string($id)."'";
		   api_sql_query($query, __FILE__, __LINE__);
		}

	}

	$qry = 'SELECT DISTINCT question_id, marks
			FROM '.$TBL_TRACK_ATTEMPT.' where exe_id = '.intval($id).'
			GROUP BY question_id';

	$res = api_sql_query($qry,__FILE__,__LINE__);
	$tot = 0;
	while($row = Database::fetch_array($res,'ASSOC'))
	{
		$tot += $row ['marks'];
	}

	$totquery = "UPDATE $TBL_TRACK_EXERCICES SET exe_result = '".Database::escape_string($tot)."' WHERE exe_Id='".Database::escape_string($id)."'";

	api_sql_query($totquery, __FILE__, __LINE__);
	$subject = get_lang('ExamSheetVCC');
	$htmlmessage = '<html>'.
				'<head>' .
				'<style type="text/css">' .
				'<!--' .
				'.body{' .
				'font-family: Verdana, Arial, Helvetica, sans-serif;' .
				'font-weight: Normal;' .
				'color: #000000;' .
				'}' .
				'.style8 {font-family: Verdana, Arial, Helvetica, sans-serif; font-weight: bold; color: #006699; }' .
				'.style10 {' .
				'	font-family: Verdana, Arial, Helvetica, sans-serif;' .
				'	font-size: 12px;' .
				'	font-weight: bold;' .
				'}' .
				'.style16 {font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 12px; }' .
				'-->' .
				'</style>' .
				'</head>' .
				'<body>' .
				'<div>' .
				'  <p>'.get_lang('DearStudentEmailIntroduction').'</p>' .
				'  <p class="style10"> '.get_lang('AttemptVCC').' </p>' .
				'  <table width="417">' .
				'    <tr>' .
				'      <td width="229" valign="top" bgcolor="E5EDF8">&nbsp;&nbsp;<span class="style10">'.get_lang('Question').'</span></td>' .
				'      <td width="469" valign="top" bgcolor="#F3F3F3"><span class="style16">#ques_name#</span></td>' .
				'    </tr>' .
				'    <tr>' .
				'      <td width="229" valign="top" bgcolor="E5EDF8">&nbsp;&nbsp;<span class="style10">'.get_lang('Exercice').'</span></td>' .
				'       <td width="469" valign="top" bgcolor="#F3F3F3"><span class="style16">#test#</span></td>' .
				'    </tr>' .
				'  </table>' .
				'  <p>'.get_lang('ClickLinkToViewComment').' <a href="#url#">#url#</a><br />' .
				'    <br />' .
				'  '.get_lang('Regards').' </p>' .
				'  </div>' .
				'  </body>' .
				'  </html>';
	$message = '<p>'.sprintf(get_lang('AttemptVCCLong'),$test).' <A href="#url#">#url#</A></p><br />';
	$mess= str_replace("#test#",$test,$message);
	//$message= str_replace("#ques_name#",$ques_name,$mess);
	$message = str_replace("#url#",$url,$mess);
	$mess = stripslashes($message);
	$headers  = " MIME-Version: 1.0 \r\n";
	$headers .= "User-Agent: Dokeos/1.6";
	$headers .= "Content-Transfer-Encoding: 7bit";
	$headers .= 'From: '.$from_name.' <'.$from.'>' . "\r\n";
	$headers="From:$from_name\r\nReply-to: $to\r\nContent-type: text/html; charset=".($charset?$charset:'ISO-8859-15');
	//mail($emailid, $subject, $mess,$headers);

	api_mail_html($emailid, $emailid, $subject, $mess, $from_name, $from);
	

	if(in_array($origin, array('tracking_course','user_course'))){
		//Redirect to the reporting		
		header('location: ../mySpace/myStudents.php?origin='.$origin.'&student='.$_GET['student'].'&details=true&course='.$_GET['course']);
	}
}

if($show!='result')
{
	$nameTools=get_lang('Exercices');
}
else
{
	if($is_allowedToEdit || $is_tutor)
	{
		$nameTools=get_lang('StudentScore');
		$interbreadcrumb[]=array("url" => "exercice.php","name" => get_lang('Exercices'));
	}
	else
	{
		$nameTools=get_lang('YourScore');
		$interbreadcrumb[]=array("url" => "exercice.php","name" => get_lang('Exercices'));
	}
}

// need functions of statsutils lib to display previous exercices scores
include_once(api_get_path(LIBRARY_PATH).'statsUtils.lib.inc.php');

if($is_allowedToEdit && !empty($choice) && $choice == 'exportqti2')
{
	require_once('export/qti2/qti2_export.php');
	$export = export_exercise($exerciseId,true);

	require_once(api_get_path(LIBRARY_PATH).'pclzip/pclzip.lib.php');
	$garbage_path = api_get_path(GARBAGE_PATH);
	$temp_dir_short = uniqid();
	$temp_zip_dir = $garbage_path."/".$temp_dir_short;
	if(!is_dir($temp_zip_dir)) mkdir($temp_zip_dir);
	$temp_zip_file = $temp_zip_dir."/".md5(time()).".zip";
	$temp_xml_file = $temp_zip_dir."/qti2export_".$exerciseId.'.xml';
	file_put_contents($temp_xml_file,$export);
	$zip_folder=new PclZip($temp_zip_file);	
	$zip_folder->add($temp_xml_file, PCLZIP_OPT_REMOVE_ALL_PATH);
	$name = 'qti2_export_'.$exerciseId.'.zip';
	
	//DocumentManager::string_send_for_download($export,true,'qti2export_'.$exerciseId.'.xml');
	DocumentManager::file_send_for_download($temp_zip_file,true,$name);
	unlink($temp_zip_file);
	unlink($temp_xml_file);
	rmdir($temp_zip_dir);
	exit(); //otherwise following clicks may become buggy
}
if(!empty($_POST['export_user_fields']))
{
	switch($_POST['export_user_fields'])
	{
		case 'export_user_fields':
			$_SESSION['export_user_fields'] = true;
			break;
		case 'do_not_export_user_fields':
		default:
			$_SESSION['export_user_fields'] = false;
			break;
	}
}
if(!empty($_POST['export_report']) && $_POST['export_report'] == 'export_report')
{
	if(api_is_platform_admin() || api_is_course_admin() || api_is_course_tutor() || api_is_course_coach())
	{
		$user_id = null;
		if(empty($_SESSION['export_user_fields'])) $_SESSION['export_user_fields'] = false;
		if(!$is_allowedToEdit and !$is_tutor)
		{
			$user_id = api_get_user_id();
		}
		require_once('exercise_result.class.php');
		switch($_POST['export_format'])
		{
			case 'xls':
				$export = new ExerciseResult();
				$export->exportCompleteReportXLS($documentPath, $user_id, $_SESSION['export_user_fields']);
				exit;
				break;
			case 'csv':
			default:
				$export = new ExerciseResult();
				$export->exportCompleteReportCSV($documentPath, $user_id, $_SESSION['export_user_fields']);
				exit;
				break;
		}
	}
	else
	{
		api_not_allowed(true);
	}
}

if ($origin != 'learnpath')
{
	//so we are not in learnpath tool
	Display::display_header($nameTools,"Exercise");
	if(isset($_GET['message']))
	{
		if (in_array($_GET['message'], array('ExerciseEdited')))
		{
			Display::display_confirmation_message(get_lang($_GET['message']));
		}
	}

}
else
{
	echo '<link rel="stylesheet" type="text/css" href="'.api_get_path(WEB_CODE_PATH).'css/default.css"/>';
}

// used for stats
include_once(api_get_path(LIBRARY_PATH).'events.lib.inc.php');

event_access_tool(TOOL_QUIZ);

Display::display_introduction_section(TOOL_QUIZ);


// selects $limitExPage exercises at the same time
$from=$page*$limitExPage;
$sql="SELECT count(id) FROM $TBL_EXERCICES";
$res = api_sql_query($sql,__FILE__,__LINE__);
list($nbrexerc) = Database::fetch_array($res);

HotPotGCt($documentPath,1,$_user['user_id']);

// only for administrator

if($is_allowedToEdit)
{

	if(!empty($choice))
	{
		// construction of Exercise
		$objExerciseTmp=new Exercise();

		if($objExerciseTmp->read($exerciseId))
		{
			switch($choice)
			{
				case 'delete':	// deletes an exercise
								$objExerciseTmp->delete();
								Display::display_confirmation_message(get_lang('ExerciseDeleted'));
								break;
				case 'enable':  // enables an exercise
								$objExerciseTmp->enable();
								$objExerciseTmp->save();

								// "WHAT'S NEW" notification: update table item_property (previously last_tooledit)
								api_item_property_update($_course, TOOL_QUIZ, $exerciseId, "QuizAdded", $_user['user_id']);

								Display::display_confirmation_message(get_lang('VisibilityChanged'));

								break;
				case 'disable': // disables an exercise
								$objExerciseTmp->disable();
								$objExerciseTmp->save();
								Display::display_confirmation_message(get_lang('VisibilityChanged'));
								break;
				case 'disable_results' : //disable the results for the learners
								$objExerciseTmp->disable_results();
								$objExerciseTmp->save();
								Display::display_confirmation_message(get_lang('ResultsDisabled'));
								break;
				case 'enable_results' : //disable the results for the learners
								$objExerciseTmp->enable_results();
								$objExerciseTmp->save();
								Display::display_confirmation_message(get_lang('ResultsEnabled'));
								break;
			}
		}

		// destruction of Exercise
		unset($objExerciseTmp);
	}

	//$sql="SELECT id,title,type,active FROM $TBL_EXERCICES ORDER BY title LIMIT $from,".($limitExPage+1);
	//$result=api_sql_query($sql,__FILE__,__LINE__);


	if(!empty($hpchoice))
	{
		switch($hpchoice)
		{
				case 'delete':	// deletes an exercise
					$imgparams = array();
					$imgcount = 0;
					GetImgParams($file,$documentPath,$imgparams,$imgcount);
					$fld = GetFolderName($file);
					for($i=0;$i < $imgcount;$i++)
					{
							my_delete($documentPath.$uploadPath."/".$fld."/".$imgparams[$i]);
							update_db_info("delete", $uploadPath."/".$fld."/".$imgparams[$i]);
					}

					if ( my_delete($documentPath.$file))
					{
						update_db_info("delete", $file);
					}
					my_delete($documentPath.$uploadPath."/".$fld."/");
					break;
				case 'enable':  // enables an exercise
					$newVisibilityStatus = "1"; //"visible"
                    $query = "SELECT id FROM $TBL_DOCUMENT WHERE path='".Database::escape_string($file)."'";
                    $res = api_sql_query($query,__FILE__,__LINE__);
                    $row = Database::fetch_array($res, 'ASSOC');
                    api_item_property_update($_course, TOOL_DOCUMENT, $row['id'], 'visible', $_user['user_id']);
                    //$dialogBox = get_lang('ViMod');

							break;
				case 'disable': // disables an exercise
					$newVisibilityStatus = "0"; //"invisible"
                    $query = "SELECT id FROM $TBL_DOCUMENT WHERE path='".Database::escape_string($file)."'";
                    $res = api_sql_query($query,__FILE__,__LINE__);
                    $row = Database::fetch_array($res, 'ASSOC');
                    api_item_property_update($_course, TOOL_DOCUMENT, $row['id'], 'invisible', $_user['user_id']);
					#$query = "UPDATE $TBL_DOCUMENT SET visibility='$newVisibilityStatus' WHERE path=\"".$file."\""; //added by Toon
					#api_sql_query($query,__FILE__,__LINE__);
					//$dialogBox = get_lang('ViMod');
					break;
				default:
					break;
		}
	}

	if($show == 'test')
	{
		$sql="SELECT id,title,type,active,description, results_disabled FROM $TBL_EXERCICES WHERE active<>'-1' ORDER BY title LIMIT ".(int)$from.",".(int)($limitExPage+1);
		$result=api_sql_query($sql,__FILE__,__LINE__);
	}
}
// only for students
elseif($show == 'test')
{
	$sql="SELECT id,title,type,description, results_disabled FROM $TBL_EXERCICES WHERE active='1' ORDER BY title LIMIT ".(int)$from.",".(int)($limitExPage+1);
	$result=api_sql_query($sql,__FILE__,__LINE__);
}


if($show == 'test'){

	$nbrExercises=Database::num_rows($result);

	echo '<table border="0" align="center" cellpadding="2" cellspacing="2" width="100%">'.
		'<tr>';

	if (($is_allowedToEdit) and ($origin != 'learnpath'))
	{
		echo '<td width="50%" nowrap="nowrap">'.
			'<img src="../img/new_test.gif" alt="new test" align="absbottom">&nbsp;<a href="exercise_admin.php?'.api_get_cidreq().'">'.get_lang('NewEx').'</a>'.
			' | <img src="../img/jqz.jpg" alt="HotPotatoes" valign="ABSMIDDLE">&nbsp;<a href="hotpotatoes.php">'.get_lang('ImportHotPotatoesQuiz').'</a>'.
			'</td>'.
			'<td width="50%" align="right">';
	}
	else
	{
		echo '<td align="right">';
	}

	//get HotPotatoes files (active and inactive)
	$res = api_sql_query ("SELECT *
					FROM $TBL_DOCUMENT
					WHERE
					path LIKE '".Database::escape_string($uploadPath)."/%/%'",__FILE__,__LINE__);
	$nbrTests = Database::num_rows($res);
	$res = api_sql_query ("SELECT *
					FROM $TBL_DOCUMENT d, $TBL_ITEM_PROPERTY ip
					WHERE  d.id = ip.ref
					AND ip.tool = '".TOOL_DOCUMENT."'
					AND d.path LIKE '".Database::escape_string($uploadPath)."/%/%'
					AND ip.visibility='1'", __FILE__,__LINE__);
	$nbrActiveTests = Database::num_rows($res);

	if($is_allowedToEdit)
	{//if user is allowed to edit, also show hidden HP tests
		$nbrHpTests = $nbrTests;
	}else
	{
		$nbrHpTests = $nbrActiveTests;
	}
	$nbrNextTests = $nbrexerc-$nbrHpTests-(($page*$limitExPage));


	//show pages navigation link for previous page
	if($page)
	{
		echo "<a href=\"".api_get_self()."?".api_get_cidreq()."&page=".($page-1)."\">&lt;&lt; ",get_lang("PreviousPage")."</a> | ";
	}
	elseif($nbrExercises+$nbrNextTests > $limitExPage)
	{
		echo "&lt;&lt; ",get_lang("PreviousPage")." | ";
	}

	//show pages navigation link for previous page
	if($nbrExercises+$nbrNextTests > $limitExPage)
	{
		echo "<a href=\"".api_get_self()."?".api_get_cidreq()."&page=".($page+1)."\">&gt;&gt; ",get_lang("NextPage")."</a>";

	}
	elseif($page)
	{
		echo get_lang("NextPage") . " &gt;&gt;";
	}

	echo '</td>',
			'</tr>',
			'</table>';

?>
<table class="data_table">
  <?php
	if (($is_allowedToEdit) and ($origin != 'learnpath'))
	{
	?>
  <tr class="row_odd">
    <th colspan="2"><?php echo get_lang("ExerciseName");?></th>
     <th><?php echo get_lang("Description");?></th>
	 <th><?php echo get_lang('Export');?></th>
	 <th><?php echo get_lang("Modify");?></th>

  </tr>
  <?php
	}
  else
	{
	 ?> <tr bgcolor="#e6e6e6">
     <th><?php echo get_lang("ExerciseName");?></th>
     <th><?php echo get_lang("Description");?></th>
	 <th><?php echo get_lang("State");?></th>

  </tr>
	<?php }

	// show message if no HP test to show
	if(!($nbrExercises+$nbrHpTests) )
	{
	?>
  <tr>
    <td <?php echo ($is_allowedToEdit?'colspan="5"':'colspan="3"'); ?>><?php echo get_lang("NoEx"); ?></td>
  </tr>
  <?php
	}

	$i=1;

	// while list exercises

	if ($origin != 'learnpath') 
	{
  		//avoid sending empty parameters
  		$myorigin = (empty($origin)?'':'&origin='.$origin);
  		$mylpid = (empty($learnpath_id)?'':'&learnpath_id='.$learnpath_id);
  		$mylpitemid = (empty($learnpath_item_id)?'':'&learnpath_item_id='.$learnpath_item_id);
		while($row=Database::fetch_array($result))
		{

			if($i%2==0) $s_class="row_odd"; else $s_class="row_even";
			echo '<tr class="'.$s_class.'">'."\n";

			// prof only
			if($is_allowedToEdit)
			{
				?>
		  <td width="27%" colspan="2">
		  <table border="0" cellpadding="0" cellspacing="0" width="100%">
		    <tr>
		      <td width="30" align="left"><img src="../img/quiz.gif"></td>
		      <td width="15" valign="left" align="center"><?php echo ($i+($page*$limitExPage)).'.'; ?></td>
		      <?php $row['title']=api_parse_tex($row['title']); ?>
		      <td>
		      	<a href="exercice_submit.php?<?php echo api_get_cidreq().$myorigin.$mylpid.$mylpitemid; ?>&exerciseId=<?php echo $row['id']; ?>" <?php if(!$row['active']) echo 'class="invisible"'; ?>><?php echo $row['title']; ?></a>
		      </td>
		    </tr>
		  </table>
		  </td>
		  <td width="8%" align="center"> <?php
		  $exid = $row['id'];
		  $sqlquery = "SELECT count(*) FROM $TBL_EXERCICE_QUESTION WHERE exercice_id = '".Database::escape_string($exid)."'";
		  $sqlresult =api_sql_query($sqlquery);
		  $rowi = mysql_result($sqlresult,0);
		  echo $rowi.' '.strtolower(get_lang(($rowi>1?'Questions':'Question'))).'</td>';
  		  echo '<td width="5%" align="center"><a href="exercice.php?choice=exportqti2&exerciseId='.$row['id'].'"><img src="../img/export.png" border="0" title="IMS/QTI" /></a></td>';
  		  ?>
	       <td width="12%" align="center">
	       <a href="exercise_admin.php?modifyExercise=yes&exerciseId=<?php echo $row['id']; ?>"> <img src="../img/edit.gif" border="0" title="<?php echo htmlentities(get_lang('Modify'),ENT_QUOTES,$charset); ?>" alt="<?php echo htmlentities(get_lang('Modify'),ENT_QUOTES,$charset); ?>" /></a>
	       <a href="admin.php?exerciseId=<?php echo $row['id']; ?>"><img src="../img/wizard_small.gif" border="0" title="<?php echo htmlentities(get_lang('Build'),ENT_QUOTES,$charset); ?>" alt="<?php echo htmlentities(get_lang('Build'),ENT_QUOTES,$charset); ?>" /></a>
	       <a href="exercice.php?choice=delete&exerciseId=<?php echo $row['id']; ?>" onclick="javascript:if(!confirm('<?php echo addslashes(htmlentities(get_lang('AreYouSureToDelete'),ENT_QUOTES,$charset)); echo " ".$row['title']; echo "?"; ?>')) return false;"> <img src="../img/delete.gif" border="0" alt="<?php echo htmlentities(get_lang('Delete'),ENT_QUOTES,$charset); ?>" /></a>
	    <?php
				// if active
				if($row['active'])
				{
					?>
      <a href="exercice.php?choice=disable&page=<?php echo $page; ?>&exerciseId=<?php echo $row['id']; ?>"> <img src="../img/visible.gif" border="0" alt="<?php echo htmlentities(get_lang('Deactivate'),ENT_QUOTES,$charset); ?>" /></a>
    <?php
				}
				// else if not active
				else
				{
					?>
      <a href="exercice.php?choice=enable&page=<?php echo $page; ?>&exerciseId=<?php echo $row['id']; ?>"> <img src="../img/invisible.gif" border="0" alt="<?php echo htmlentities(get_lang('Activate'),ENT_QUOTES,$charset); ?>" /></a>
    <?php
				}
				
				if($row['results_disabled'])
					echo '<a href="exercice.php?choice=enable_results&page='.$page.'&exerciseId='.$row['id'].'" title="'.get_lang('EnableResults').'" alt="'.get_lang('EnableResults').'"><img src="../img/lp_quiz_na.gif" border="0" alt="'.htmlentities(get_lang('EnableResults'),ENT_QUOTES,$charset).'" /></a>';
				else
					echo '<a href="exercice.php?choice=disable_results&page='.$page.'&exerciseId='.$row['id'].'" title="'.get_lang('DisableResults').'" alt="'.get_lang('DisableResults').'"><img src="../img/lp_quiz.gif" border="0" alt="'.htmlentities(get_lang('DisableResults'),ENT_QUOTES,$charset).'" /></a>';
				
				echo "</td>";
				echo "</tr>\n";

			}
			// student only
			else
			{
				?>
      <td width="40%"><table border="0" cellpadding="0" cellspacing="0" width="100%">
          <tr>
            <td width="20" valign="top" align="right"><?php echo ($i+($page*$limitExPage)).'.'; ?></td>
            <td width="1">&nbsp;</td>
            <?php $row['title']=api_parse_tex($row['title']);?>
            <td><a href="exercice_submit.php?<?php echo api_get_cidreq().$myorigin.$mylpid.$myllpitemid; ?>&exerciseId=<?php echo $row['id']; ?>"><?php echo $row['title']; ?></a></td>

			</tr>
    </table></td>
	 <td align="center"> <?php
  $exid = $row['id'];
  $sqlquery = "SELECT count(*) FROM $TBL_EXERCICE_QUESTION WHERE exercice_id = '".Database::escape_string($exid)."'";
  $sqlresult =api_sql_query($sqlquery);
  $rowi = mysql_result($sqlresult,0);
  echo ($rowi>1?get_lang('Questions'):get_lang('Question')); ?> </td>

	<td align="center"><?php
		$eid = $row['id'];
	$uid= api_get_user_id();
    //this query might be improved later on by ordering by the new "tms" field rather than by exe_id
	$qry = "select * from $TBL_TRACK_EXERCICES where exe_exo_id = '".Database::escape_string($eid)."' and exe_user_id = '".Database::escape_string($uid)."' and exe_cours_id = '".api_get_course_id()."' ORDER BY exe_id DESC";	
	$qryres = api_sql_query($qry);
	$num = Database::num_rows($qryres);
    if($num>0)
    {
    	$row = Database::fetch_array($qryres);
    	$percentage = 0;
    	if($row['exe_weighting'] != 0)
    	{
    		$percentage = ($row['exe_result']/$row['exe_weighting'])*100;
    	}
		echo get_lang('Attempted').' ('.get_lang('Score').':';
		printf("%1.2f\n",$percentage);
		echo " %)";
	}
	else
	{
		echo get_lang('NotAttempted');
	}
	?></td>
  </tr>
 
  <?php
			}

			// skips the last exercise, that is only used to know if we have or not to create a link "Next page"
			if($i == $limitExPage)
			{
				break;
			}

			$i++;
			
		}	// end while()

		$ind = $i;


		if (($from+$limitExPage-1)>$nbrexerc)
		{
			if($from>$nbrexerc)
			{
				$from = $from - $nbrexerc;
			  	$to = $limitExPage;
			}
			else
			{
				$to = $limitExPage-($nbrexerc-$from);
				$from = 0;
			}
		}
		else{
			$to = $limitExPage;
		}
		
		if($is_allowedToEdit)
		{
			$sql = "SELECT d.path as path, d.comment as comment, ip.visibility as visibility
				FROM $TBL_DOCUMENT d, $TBL_ITEM_PROPERTY ip
							WHERE   d.id = ip.ref AND ip.tool = '".TOOL_DOCUMENT."' AND
							 (d.path LIKE '%htm%')
							AND   d.path  LIKE '".Database::escape_string($uploadPath)."/%/%' LIMIT ".(int)$from.",".(int)$to; // only .htm or .html files listed
		}
		else
		{
			$sql = "SELECT d.path as path, d.comment as comment, ip.visibility as visibility
				FROM $TBL_DOCUMENT d, $TBL_ITEM_PROPERTY ip
								WHERE d.id = ip.ref AND ip.tool = '".TOOL_DOCUMENT."' AND
								 (d.path LIKE '%htm%')
								AND   d.path  LIKE '".Database::escape_string($uploadPath)."/%/%' AND ip.visibility='1' LIMIT ".(int)$from.",".(int)$to;
		}

		$result = api_sql_query ($sql,__FILE__,__LINE__);
		
		while($row = Database::fetch_array($result, 'ASSOC'))
		{
			$attribute['path'      ][] = $row['path'      ];
			$attribute['visibility'][] = $row['visibility'];
			$attribute['comment'   ][] = $row['comment'   ];
		}
		$nbrActiveTests = 0;
		if(is_array($attribute['path']))
		{
			while(list($key,$path) = each($attribute['path']))
			{
				list($a,$vis)=each($attribute['visibility']);
				if (strcmp($vis,"1")==0)
				{ $active=1;}
				else
				{ $active=0;}
				echo "<tr>\n";

				$title = GetQuizName($path,$documentPath);
				if ($title =='')
				{
					$title = GetFileName($path);
				}
				// prof only
				if($is_allowedToEdit)
				{
					/************/
					?>
  <td width="27%" colspan="2">
  <table border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr>
      <td width="30" align="left"><img src="../img/jqz.jpg" alt="HotPotatoes" /></td>
	   <td width="15" align="center"><?php echo ($ind+($page*$limitExPage)).'.'; ?></td>
       <td><a href="showinframes.php?file=<?php echo $path?>&cid=<?php echo $_course['official_code'];?>&uid=<?php echo $_user['user_id'];?>" <?php if(!$active) echo 'class="invisible"'; ?>><?php echo $title?></a></td>
    </tr>
  </table></td>
  <td></td><td></td>
      <td width="12%" align="center"><a href="adminhp.php?hotpotatoesName=<?php echo $path; ?>"> <img src="../img/edit.gif" border="0" alt="<?php echo htmlentities(get_lang('Modify'),ENT_QUOTES,$charset); ?>" /></a>
       <img src="../img/wizard_gray_small.gif" border="0" title="<?php echo htmlentities(get_lang('Build'),ENT_QUOTES,$charset); ?>" alt="<?php echo htmlentities(get_lang('Build'),ENT_QUOTES,$charset); ?>" />
  <a href="<?php echo $exercicePath; ?>?hpchoice=delete&file=<?php echo $path; ?>" onclick="javascript:if(!confirm('<?php echo addslashes(htmlentities(get_lang('AreYouSure'),ENT_QUOTES,$charset).$title."?"); ?>')) return false;"><img src="../img/delete.gif" border="0" alt="<?php echo htmlentities(get_lang('Delete'),ENT_QUOTES,$charset); ?>" /></a>
    <?php
					// if active
					if($active)
					{
						$nbrActiveTests = $nbrActiveTests + 1;
						?>
      <a href="<?php echo $exercicePath; ?>?hpchoice=disable&page=<?php echo $page; ?>&file=<?php echo $path; ?>"><img src="../img/visible.gif" border="0" alt="<?php echo htmlentities(get_lang('Deactivate'),ENT_QUOTES,$charset); ?>" /></a>
    <?php
					}
					// else if not active
					else
					{
						?>
    <a href="<?php echo $exercicePath; ?>?hpchoice=enable&page=<?php echo $page; ?>&file=<?php echo $path; ?>"><img src="../img/invisible.gif" border="0" alt="<?php echo htmlentities(get_lang('Activate'),ENT_QUOTES,$charset); ?>" /></a>
    <?php
					}
					echo '<img src="../img/lp_quiz_na.gif" border="0" alt="" />';
				/****************/
				?></td>
      <?php }
				// student only
				else
				{
					if ($active==1)
					{
						$nbrActiveTests = $nbrActiveTests + 1;
						?>
    <td width="40%"><table border="0" cellpadding="0" cellspacing="0" width="100%">

        <td width="20" align="right"><?php echo ($ind+($page*$limitExPage)).'.'; ?><!--<img src="../img/jqz.jpg" alt="HotPotatoes" />--></td>
       <td width="1">&nbsp;</td>
        <td><a href="showinframes.php?<?php echo api_get_cidreq()."&file=".$path."&cid=".$_course['official_code']."&uid=".$_user['user_id'].'"'; if(!$active) echo 'class="invisible"'; ?>"><?php echo $title;?></a></td>

     </tr>
    </table></td>
  </tr>
  <?php
					}
				}
				?>
  <?php
				if($ind == $limitExPage)
				{
					break;
				}
				if($is_allowedToEdit)
				{
					$ind++;
				}
				else
				{
					if ($active==1)
					{
						$ind++;
					}
				}
			}
		}


	} //end if ($origin != 'learnpath') {
	?>
</table>
<?php
}else{
	if($origin != 'learnpath'){
		echo '<a href="'.api_add_url_param($_SERVER['REQUEST_URI'],'show=test').'">&lt;&lt; '.get_lang('Back').'</a>';
	}
}// end if($show == 'test')

/*****************************************/
/* Exercise Results (uses tracking tool) */
/*****************************************/

// if tracking is enabled
if($_configuration['tracking_enabled'])
{

	if($show == 'result')
	{


		// the form
		if(api_is_platform_admin() || api_is_course_admin() || api_is_course_tutor() || api_is_course_coach())
		{
			echo '<form id="form1a" name="form1a" method="post" action="'.api_get_self().'?show='.Security::remove_XSS($_GET['show']).'">';
			echo '<input type="hidden" name="export_report" value="export_report">';
			echo '<input type="hidden" name="export_format" value="csv">';
			echo '</form>';
			echo '<form id="form1b" name="form1b" method="post" action="'.api_get_self().'?show='.Security::remove_XSS($_GET['show']).'">';
			echo '<input type="hidden" name="export_report" value="export_report">';
			echo '<input type="hidden" name="export_format" value="xls">';
			echo '</form>';
			echo '<form id="form1c" name="form1c" method="post" action="'.api_get_self().'?show='.Security::remove_XSS($_GET['show']).'">';
			if($_SESSION['export_user_fields']==false)
			{
				$alt = get_lang('ExportWithUserFields');
				echo '<input type="hidden" name="export_user_fields" value="export_user_fields">';
			}
			else
			{
				$alt = get_lang('ExportWithoutUserFields');
				echo '<input type="hidden" name="export_user_fields" value="do_not_export_user_fields">';
			}
			echo '</form>';
			echo '<a class="quiz_export_link" href="#" onclick="document.form1a.submit();"><img align="absbottom" src="'.api_get_path(WEB_IMG_PATH).'excel.gif" alt="'.get_lang('ExportAsCSV').'">&nbsp;'.get_lang('ExportAsCSV').'</a>';
			echo '<a class="quiz_export_link" href="#" onclick="document.form1b.submit();"><img align="absbottom" src="'.api_get_path(WEB_IMG_PATH).'excel.gif" alt="'.get_lang('ExportAsXLS').'">&nbsp;'.get_lang('ExportAsXLS').'</a>';
			echo '<a class="quiz_export_link" href="#" onclick="document.form1c.submit();"><img align="absbottom" src="'.api_get_path(WEB_IMG_PATH).'synthese_view.gif" alt="'.$alt.'">&nbsp;'.$alt.'</a>';
			echo '<br /><br />';		
		}
		?>

		<table class="data_table">
		 <tr class="row_odd">
		  <?php if($is_allowedToEdit || $is_tutor): ?>
		  <th><?php echo get_lang("User"); ?></th><?php endif; ?>
		  <th><?php echo get_lang("Exercice"); ?></th>
		  <th><?php echo get_lang("Date"); ?></th>
		  <th><?php echo get_lang("Result"); ?></th>
		  <th><?php echo (($is_allowedToEdit||$is_tutor)?get_lang("CorrectTest"):get_lang("ViewTest")); ?></th>


		 </tr>

		<?php
		if($is_allowedToEdit || $is_tutor)
		{
			//get all results (ourself and the others) as an admin should see them
			//AND exe_user_id <> $_user['user_id']  clause has been removed
			$sql="SELECT CONCAT(lastname,' ',firstname),ce.title, te.exe_result ,
						te.exe_weighting, UNIX_TIMESTAMP(te.exe_date),te.exe_id,email
				  FROM $TBL_EXERCICES AS ce , $TBL_TRACK_EXERCICES AS te, $TBL_USER AS user
				  WHERE te.exe_exo_id = ce.id AND user_id=te.exe_user_id AND te.exe_cours_id='".Database::escape_string($_cid)."'
				  ORDER BY te.exe_cours_id ASC, ce.title ASC, te.exe_date ASC";

			$hpsql="SELECT CONCAT(tu.lastname,' ',tu.firstname), tth.exe_name,
						tth.exe_result , tth.exe_weighting, UNIX_TIMESTAMP(tth.exe_date)
					FROM $TBL_TRACK_HOTPOTATOES tth, $TBL_USER tu
					WHERE  tu.user_id=tth.exe_user_id AND tth.exe_cours_id = ".Database::escape_string($_cid)."'
					ORDER BY tth.exe_cours_id ASC, tth.exe_date ASC";

		}
		else
		{ // get only this user's results
			  $sql="SELECT '',ce.title, te.exe_result , te.exe_weighting, UNIX_TIMESTAMP(te.exe_date),te.exe_id
				  FROM $TBL_EXERCICES AS ce , $TBL_TRACK_EXERCICES AS te
				  WHERE te.exe_exo_id = ce.id AND te.exe_user_id='".$_user['user_id']."' AND te.exe_cours_id=".Database::escape_string($_cid)."'  AND results_disabled=0
				  ORDER BY te.exe_cours_id ASC, ce.title ASC, te.exe_date ASC";

			$hpsql="SELECT '',exe_name, exe_result , exe_weighting, UNIX_TIMESTAMP(exe_date)
					FROM $TBL_TRACK_HOTPOTATOES
					WHERE exe_user_id = '".$_user['user_id']."' AND exe_cours_id = ".Database::escape_string($_cid)."'
					ORDER BY exe_cours_id ASC, exe_date ASC";

		}
		
		$results=getManyResultsXCol($sql,7);
		$hpresults=getManyResultsXCol($hpsql,5);

		$NoTestRes = 0;
		$NoHPTestRes = 0;
		//Print the results of tests
		if(is_array($results))
		{
			$sizeof = sizeof($results);
			for($i = 0; $i < $sizeof; $i++)
			{
				$id = $results[$i][5];
				$mailid = $results[$i][6];
				$user = $results[$i][0];
				$test = $results[$i][1];
				$dt = strftime($dateTimeFormatLong,$results[$i][4]);
				$res = $results[$i][2];
				echo '<tr';
				if($i%2==0) echo 'class="row_odd"'; else echo 'class="row_even"';
				echo '>';
				if($is_allowedToEdit || $is_tutor)
				{
					$user = $results[$i][0];
					echo '<td>'.$user.'</td>';
				}
				echo '<td>'.$test.'</td>';
				echo '<td>'.format_locale_date(get_lang('dateTimeFormatLong'),$results[$i][4]).'</td>';
		  		echo '<td>'.round(($res/($results[$i][3]!=0?$results[$i][3]:1))*100).'% ('.$res.' / '.$results[$i][3].')</td>';
				echo '<td>'.(($is_allowedToEdit||$is_tutor)?"<a href='exercise_show.php?user=$user&dt=$dt&res=$res&id=$id&email=$mailid'>".get_lang('Edit').'</a>'.' - '.'<a href="exercice.php?cidReq='.htmlentities($_GET['cidReq']).'&show=result&delete=delete&did='.$id.'" onclick="javascript:if(!confirm(\''.sptrinf(get_lang('DeleteAttempt'),$user,$dt).'\')) return false;">'.get_lang('Delete').'</a>':"<a href='exercise_show.php?dt=$dt&res=$res&id=$id'>".get_lang('Show').'</a>').'</td>';
				echo '</tr>';
			}
		}
		else
		{
				$NoTestRes = 1;
		}
		// Print the Result of Hotpotatoes Tests
		if(is_array($hpresults))
		{
			$sizeof = sizeof($hpresults);
			for($i = 0; $i < $sizeof; $i++)
			{
				$title = GetQuizName($hpresults[$i][1],$documentPath);
				if ($title =='')
				{
					$title = GetFileName($hpresults[$i][1]);
				}
				echo '<tr>';
				if($is_allowedToEdit)
				{
					echo '<td class="content">'.$hpresults[$i][0].'</td>';
				}
				echo '<td class="content">'.$title.'</td>';
				echo '<td class="content">'.strftime($dateTimeFormatLong,$hpresults[$i][4]).'</td>';
				echo '<td class="content">'.round(($hpresults[$i][2]/($hpresults[$i][3]!=0?$hpresults[$i][3]:1))*100).'% ('.$hpresults[$i][2].' / '.$hpresults[$i][3].')</td>';
				echo '<td></td>'; //there is no possibility to edit the results of a Hotpotatoes test
				echo '</tr>';
			}
		}
		else
		{
			$NoHPTestRes = 1;
		}



		if ($NoTestRes==1 && $NoHPTestRes==1)
		{
		?>

		 <tr>
		  <td colspan="5"><?php echo get_lang("NoResult"); ?></td>
		 </tr>

		<?php
		}

		?>

		</table>

		<?php
	}else{

		echo '<p><img src="'.api_get_path(WEB_IMG_PATH).'show_test_results.gif" align="absbottom">&nbsp;<a href="'.api_add_url_param($_SERVER['REQUEST_URI'],'show=result').'">'.get_lang("Results").' &gt;&gt;</a></p>';

	}// end if($show == 'result')

}// end if tracking is enabled

if ($origin != 'learnpath') { //so we are not in learnpath tool
	Display::display_footer();
} 
else 
{
	?>
	<link rel="stylesheet" type="text/css" href="<?php echo $clarolineRepositoryWeb ?>css/default.css" />
	<?php
}
?>
