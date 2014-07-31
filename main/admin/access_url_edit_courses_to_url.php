<?php
/* For licensing terms, see /license.txt */
/**
*	@package chamilo.admin
*	@author Julio Montoya <gugli100@gmail.com>
*/


// name of the language file that needs to be included
$language_file = 'admin';

// resetting the course id
$cidReset = true;

//require_once '../inc/global.inc.php';

$xajax = new xajax();
$xajax -> registerFunction (array('search_courses', 'Accessurleditcoursestourl', 'search_courses'));

// setting the section (for the tabs)
$this_section = SECTION_PLATFORM_ADMIN;

// Access restrictions
api_protect_global_admin_script();
if (!api_get_multiple_access_url()) {
	header('Location: index.php');
	exit;
}


// Database Table Definitions
$tbl_access_url_rel_course = Database :: get_main_table(TABLE_MAIN_ACCESS_URL_REL_COURSE);
$tbl_course 			 = Database :: get_main_table(TABLE_MAIN_COURSE);
$tbl_access_url 		 = Database :: get_main_table(TABLE_MAIN_ACCESS_URL);

// setting breadcrumbs
$tool_name = get_lang('EditCoursesToURL');
$interbreadcrumb[] = array ('url' => 'index.php', 'name' => get_lang('PlatformAdmin'));
$interbreadcrumb[] = array ('url' => 'access_urls.php', 'name' => get_lang('MultipleAccessURLs'));

$add_type = 'multiple';
if(isset($_REQUEST['add_type']) && $_REQUEST['add_type']!='') {
	$add_type = Security::remove_XSS($_REQUEST['add_type']);
}

$access_url_id=1;
if (isset($_REQUEST['access_url_id']) && $_REQUEST['access_url_id']!='') {
	$access_url_id = Security::remove_XSS($_REQUEST['access_url_id']);
}

$xajax -> processRequests();
$htmlHeadXtra[] = $xajax->getJavascript('../inc/lib/xajax/');
$htmlHeadXtra[] = '<script>
function add_user_to_url(code, content) {

	document.getElementById("course_to_add").value = "";
	document.getElementById("ajax_list_courses").innerHTML = "";

	destination = document.getElementById("destination_users");
	destination.options[destination.length] = new Option(content,code);

	destination.selectedIndex = -1;
	sortOptions(destination.options);
}

function send() {
	if (document.formulaire.access_url_id.value != 0) {
		document.formulaire.form_sent.value=0;
		document.formulaire.add_type.value=\''.$add_type.'\';
		document.formulaire.submit();
	}
}

function remove_item(origin) {
	for(var i = 0 ; i<origin.options.length ; i++) {
		if(origin.options[i].selected) {
			origin.options[i]=null;
			i = i-1;
		}
	}
}
</script>';

$form_sent = 0;
$errorMsg = '';
$UserList = $SessionList = array();
$users = $sessions = array();

if (isset($_POST['form_sent']) && $_POST['form_sent']) {
	$form_sent=$_POST['form_sent'];
	$course_list=$_POST['course_list'];

	if (!is_array($course_list)) {
		$course_list=array();
	}

	if ($form_sent == 1) {
		if ($access_url_id==0) {
			header('Location: access_url_edit_users_to_url.php?action=show_message&message='.get_lang('SelectURL'));
		}
		elseif(is_array($course_list) ) {
			UrlManager::update_urls_rel_course($course_list, $access_url_id);
			header('Location: access_urls.php?action=show_message&message='.get_lang('CoursesWereEdited'));
		}
	}
}

Display::display_header($tool_name);

echo '<div class="actions">';
echo Display::url( Display::return_icon('view_more_stats.gif', get_lang('AddUserToURL'),''), api_get_path(WEB_CODE_PATH).'admin/access_url_add_courses_to_url.php');
echo '</div>';

api_display_tool_title($tool_name);

if (isset($_GET['action']) && $_GET['action'] == 'show_message')
	Display :: display_normal_message(Security::remove_XSS(stripslashes($_GET['message'])));

$no_course_list = $course_list = array();
$ajax_search = $add_type == 'unique' ? true : false;

if($ajax_search) {
	$courses = UrlManager::get_url_rel_course_data($access_url_id);
	foreach ($courses as $course) {
		$course_list[$course['c_id']] = $course ;
	}
} else {
	$courses = UrlManager::get_url_rel_course_data();

	foreach ($courses as $course) {
		if ($course['access_url_id'] == $access_url_id) {
			$course_list[$course['c_id']] = $course ;
		}
	}

	$tbl_course = Database :: get_main_table(TABLE_MAIN_COURSE);
	$sql="SELECT code, title, id
	  	  	FROM $tbl_course u
			ORDER BY title, code";
	$result = Database::query($sql);
	$courses = Database::store_result($result);
	$course_list_leys = array_keys($course_list);
	foreach ($courses as $course) {
		if (!in_array($course['id'], $course_list_leys))
			$no_course_list[$course['id']] = $course ;
	}
}


if($add_type == 'multiple') {
	$link_add_type_unique = '<a href="'.api_get_self().'?add_type=unique&access_url_id='.$access_url_id.'">'.get_lang('SessionAddTypeUnique').'</a>';
	$link_add_type_multiple = get_lang('SessionAddTypeMultiple');
} else {
	$link_add_type_unique = get_lang('SessionAddTypeUnique');
	$link_add_type_multiple = '<a href="'.api_get_self().'?add_type=multiple&access_url_id='.$access_url_id.'">'.get_lang('SessionAddTypeMultiple').'</a>';
}

$url_list = UrlManager::get_url_data();

?>

<div style="text-align: left;">
	<?php echo $link_add_type_unique ?>&nbsp;|&nbsp;<?php echo $link_add_type_multiple ?>
</div>
<br /><br />
<form name="formulaire" method="post" action="<?php echo api_get_self(); ?>" style="margin:0px;" <?php if($ajax_search){echo ' onsubmit="valide();"';}?> >
<?php echo get_lang('SelectUrl').' : '; ?>
<select name="access_url_id" onchange="javascript:send();">
<option value="0">-- <?php echo get_lang('SelectUrl')?> -- </option>
	<?php
	$url_selected='';
	foreach ($url_list as $url_obj) {
		$checked = '';
		if (!empty($access_url_id)) {
			if ($url_obj['id']==$access_url_id) {
			$checked = 'selected=true';
			$url_selected = $url_obj['url'];
			}
		}
		if ($url_obj['active']==1) {
			?>
				<option <?php echo $checked;?> value="<?php echo $url_obj['id']; ?>"> <?php echo $url_obj['url']; ?></option>
			<?php
		}
	}
	?>
</select>
<br /><br />
<input type="hidden" name="form_sent" value="1" />
<input type="hidden" name="add_type" value = "<?php echo $add_type ?>" />

<?php
if(!empty($errorMsg)) {
	Display::display_normal_message($errorMsg); //main API
}

?>

<table border="0" cellpadding="5" cellspacing="0" width="100%">

<!-- Users -->
<tr>
  <td align="center"><b><?php echo get_lang('CourseListInPlatform') ?> :</b>
  </td>
  <td></td>
  <td align="center"><b><?php echo get_lang('CourseListIn').' '.$url_selected; ?></b></td>
</tr>

<tr>
  <td align="center">
  <div id="content_source">
  	  <?php
  	  if($ajax_search) {
  	  	?>
		<input type="text" id="course_to_add" onkeyup="xajax_search_courses(this.value,document.formulaire.access_url_id.options[document.formulaire.access_url_id.selectedIndex].value)" />
		<div id="ajax_list_courses"></div>
		<?php
  	  } else {
  	  ?>
	  <select id="origin_users" name="no_course_list[]" multiple="multiple" size="15" style="width:380px;">
		<?php
		foreach($no_course_list as $no_course) {
		?>
			<option value="<?php echo $no_course['id']; ?>"><?php echo $no_course['title'].' ('.$no_course['code'].')'; ?></option>
		<?php
		}
		unset($no_course_list);
		?>
	  </select>
	<?php
  	  }
  	 ?>
  </div>
  </td>
  <td width="10%" valign="middle" align="center">
  <?php
  if($ajax_search) {
	?>
	<button class="arrowl" type="button" onclick="remove_item(document.getElementById('destination_users'))" ></button>
  	<?php
  } else {
  	?>
	<button class="arrowr" type="button" onclick="moveItem(document.getElementById('origin_users'), document.getElementById('destination_users'))" ></button>
	<br /><br />
	<button class="arrowl" type="button" onclick="moveItem(document.getElementById('destination_users'), document.getElementById('origin_users'))" ></button>
	<?php
  }
  ?>
	<br /><br /><br /><br /><br /><br />
  </td>
  <td align="center">
  <select id="destination_users" name="course_list[]" multiple="multiple" size="15" style="width:380px;">

<?php

foreach ($course_list as $course) {
?>
	<option value="<?php echo $course['c_id']; ?>"><?php echo $course['title'].' ('.$course['code'].')'; ?></option>
<?php
}
unset($course_list);
?>

  </select></td>
</tr>
<tr>
	<td colspan="3" align="center">
		<br />
		<?php
		if(isset($_GET['add']))
			echo '<button class="save" onclick="valide()" >'.get_lang('AddCoursesToURL').'</button>';
		else
			echo '<button class="save" onclick="valide()" >'.get_lang('EditCoursesToURL').'</button>';
		?>
	</td>
</tr>
</table>
</form>

<script>

function valide(){
	var options = document.getElementById('destination_users').options;
	for (i = 0 ; i<options.length ; i++)
		options[i].selected = true;
	document.forms.formulaire.submit();
}


function loadUsersInSelect(select){

	var xhr_object = null;

	if(window.XMLHttpRequest) // Firefox
		xhr_object = new XMLHttpRequest();
	else if(window.ActiveXObject) // Internet Explorer
		xhr_object = new ActiveXObject("Microsoft.XMLHTTP");
	else  // XMLHttpRequest non supporté par le navigateur
	alert("Votre navigateur ne supporte pas les objets XMLHTTPRequest...");

	xhr_object.open("POST", "loadUsersInSelect.ajax.php");
	xhr_object.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	nosessionUsers = makepost(document.getElementById('origin_users'));
	sessionUsers = makepost(document.getElementById('destination_users'));
	nosessionClasses = makepost(document.getElementById('origin_classes'));
	sessionClasses = makepost(document.getElementById('destination_classes'));
	xhr_object.send("nosessionusers="+nosessionUsers+"&sessionusers="+sessionUsers+"&nosessionclasses="+nosessionClasses+"&sessionclasses="+sessionClasses);

	xhr_object.onreadystatechange = function() {
		if(xhr_object.readyState == 4) {
			document.getElementById('content_source').innerHTML = result = xhr_object.responseText;
			//alert(xhr_object.responseText);
		}
	}
}

function makepost(select){
	var options = select.options;
	var ret = "";
	for (i = 0 ; i<options.length ; i++)
		ret = ret + options[i].value +'::'+options[i].text+";;";

	return ret;
}
</script>
<?php
Display::display_footer();
