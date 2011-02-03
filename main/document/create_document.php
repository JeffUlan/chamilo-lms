<?php
/* For licensing terms, see /license.txt */

/**
 *	This file allows creating new html documents with an online WYSIWYG html editor.
 *
 *	@package chamilo.document
 */

/*	INIT SECTION */

// Name of the language file that needs to be included
$language_file = array('document', 'gradebook');

require_once '../inc/global.inc.php';

$_SESSION['whereami'] = 'document/create';
$this_section = SECTION_COURSES;

$htmlHeadXtra[] = '<script src="'.api_get_path(WEB_LIBRARY_PATH).'javascript/jquery.js" type="text/javascript" language="javascript"></script>'; //jQuery
$htmlHeadXtra[] = '<script type="text/javascript">
function InnerDialogLoaded()
{
	/*
	var B=new window.frames[0].FCKToolbarButton(\'Templates\',window.frames[0].FCKLang.Templates);
	return B.ClickFrame();
	*/

	var isIE  = (navigator.appVersion.indexOf(\'MSIE\') != -1) ? true : false ;
	var EditorFrame = null ;

	if ( !isIE )
	{
		EditorFrame = window.frames[0] ;
	}
	else
	{
		// For this dynamic page window.frames[0] enumerates frames in a different order in IE.
		// We need a sure method to locate the frame that contains the online editor.
		for ( var i = 0, n = window.frames.length ; i < n ; i++ )
		{
			if ( window.frames[i].location.toString().indexOf(\'InstanceName=content\') != -1 )
			{
				EditorFrame = window.frames[i] ;
			}
		}
	}

	if ( !EditorFrame )
	{
		return null ;
	}

	var B = new EditorFrame.FCKToolbarButton(\'Templates\', EditorFrame.FCKLang.Templates);
	return B.ClickFrame();
};

	var temp=false;
	var temp2=false;
	var use_document_title='.api_get_setting('use_document_title').';
	var load_default_template = '. ((isset($_POST['submit']) || empty($_SERVER['QUERY_STRING'])) ? 'false' : 'true' ) .';

	function launch_templates()
	{
		//document.getElementById(\'frmModel\').style.display="block";
		//document.getElementById(\'content___Frame\').width=\'70%\';
		//window.frames[0].FCKToolbarItems.GetItem("Template").Click;
	}

	function FCKeditor_OnComplete( editorInstance )
	{
		editorInstance.Events.AttachEvent( \'OnSelectionChange\', check_for_title ) ;
		document.getElementById(\'frmModel\').innerHTML = "<iframe style=\'height: 525px; width: 180px;\' scrolling=\'no\' frameborder=\'0\' src=\''.api_get_path(WEB_LIBRARY_PATH).'fckeditor/editor/fckdialogframe.html \'>";
	}

	function check_for_title()
	{
		if(temp){
			// This functions shows that you can interact directly with the editor area
			// DOM. In this way you have the freedom to do anything you want with it.

			// Get the editor instance that we want to interact with.
			var oEditor = FCKeditorAPI.GetInstance(\'content\') ;

			// Get the Editor Area DOM (Document object).
			var oDOM = oEditor.EditorDocument ;

			var iLength ;
			var contentText ;
			var contentTextArray;
			var bestandsnaamNieuw = "";
			var bestandsnaamOud = "";

			// The are two diffent ways to get the text (without HTML markups).
			// It is browser specific.

			if( document.all )		// If Internet Explorer.
			{
				contentText = oDOM.body.innerText ;
			}
			else					// If Gecko.
			{
				var r = oDOM.createRange() ;
				r.selectNodeContents( oDOM.body ) ;
				contentText = r.toString() ;
			}

			var index=contentText.indexOf("/*<![CDATA");
			contentText=contentText.substr(0,index);

			// Compose title if there is none
			contentTextArray = contentText.split(\' \') ;
			var x=0;
			for(x=0; (x<5 && x<contentTextArray.length); x++)
			{
				if(x < 4)
				{
					bestandsnaamNieuw += contentTextArray[x] + \' \';
				}
				else
				{
					bestandsnaamNieuw += contentTextArray[x];
				}
			}

		// comment see FS#3335
		//	if(document.getElementById(\'title_edited\').value == "false")
		//	{
		//		document.getElementById(\'filename\').value = bestandsnaamNieuw;
		//		if(use_document_title){
		//			document.getElementById(\'title\').value = bestandsnaamNieuw;
		//		}
		//	}

		}
		temp=true;
	}

	function trim(s)
	{
	 while(s.substring(0,1) == \' \') {
	  s = s.substring(1,s.length);
	 }
	 while(s.substring(s.length-1,s.length) == \' \') {
	  s = s.substring(0,s.length-1);
	 }
	 return s;
	}

	function check_if_still_empty()
	{
		if(trim(document.getElementById(\'filename\').value) != "")
		{
			document.getElementById(\'title_edited\').value = "true";
		}
	}

	function setFocus(){
	$("#document_title").focus();
		}
	$(window).load(function () {
	  setFocus();
	    });
</script>';

require_once api_get_path(LIBRARY_PATH).'fileUpload.lib.php';
require_once api_get_path(LIBRARY_PATH).'document.lib.php';
require_once api_get_path(LIBRARY_PATH).'groupmanager.lib.php';
require_once api_get_path(LIBRARY_PATH).'formvalidator/FormValidator.class.php';
require_once api_get_path(LIBRARY_PATH).'usermanager.lib.php';
if (isset($_REQUEST['certificate'])) {
	$nameTools = get_lang('CreateCertificate');
} else {
	$nameTools = get_lang('CreateDocument');
}

$nameTools = get_lang('CreateDocument');

/*	Constants and variables */

$dir = isset($_GET['dir']) ? Security::remove_XSS($_GET['dir']) : Security::remove_XSS($_POST['dir']);

/*	MAIN CODE */

if (api_is_in_group()) {
	$group_properties = GroupManager::get_group_properties($_SESSION['_gid']);
}

// Please, do not modify this dirname formatting

if (strstr($dir, '..')) {
	$dir = '/';
}

if ($dir[0] == '.') {
	$dir = substr($dir, 1);
}

if ($dir[0] != '/') {
	$dir = '/'.$dir;
}

if ($dir[strlen($dir) - 1] != '/') {
	$dir .= '/';
}

// Configuration for the FCKEDITOR
$doc_tree= explode('/', $dir);
$count_dir = count($doc_tree) -2; // "2" because at the begin and end there are 2 "/"
// Level correction for group documents.
if (!empty($group_properties['directory']))
{
	$count_dir = $count_dir > 0 ? $count_dir - 1 : 0;
}
$relative_url = '';
for ($i = 0; $i < ($count_dir); $i++) {
	$relative_url .= '../';
}
// We do this in order to avoid the condition in html_editor.php ==> if ($this -> fck_editor->Config['CreateDocumentWebDir']=='' || $this -> fck_editor->Config['CreateDocumentDir']== '')
if ($relative_url== '') {
	$relative_url = '/';
}

$is_allowed_to_edit = api_is_allowed_to_edit(null, true);

$html_editor_config = array(
	'ToolbarSet' => ($is_allowed_to_edit ? 'Documents' :'DocumentsStudent'),
	'Width' => '100%',
	'Height' => '600',
	'FullPage' => true,
	'InDocument' => true,
	'CreateDocumentDir' => $relative_url,
	'CreateDocumentWebDir' => (empty($group_properties['directory']))
		? api_get_path(WEB_COURSE_PATH).$_course['path'].'/document/'
		: api_get_path(WEB_COURSE_PATH).api_get_course_path().'/document'.$group_properties['directory'].'/',
	'BaseHref' => api_get_path(WEB_COURSE_PATH).$_course['path'].'/document'.$dir
);

$filepath = api_get_path(SYS_COURSE_PATH).$_course['path'].'/document'.$dir;

if (!is_dir($filepath)) {
	$filepath = api_get_path(SYS_COURSE_PATH).$_course['path'].'/document/';
	$dir = '/';
}

//I'm in the certification module?
$is_certificate_mode = false;
$is_certificate_array = explode('/',$_GET['dir']);
array_shift($is_certificate_array);
if ($is_certificate_array[0]=='certificates') {
	$is_certificate_mode = true;
}
$to_group_id = 0;

if (!$is_certificate_mode) {
	if (isset ($_SESSION['_gid']) && $_SESSION['_gid'] != '') {
		$req_gid = '&amp;gidReq='.$_SESSION['_gid'];
		$interbreadcrumb[] = array ("url" => "../group/group_space.php?gidReq=".$_SESSION['_gid'], "name" => get_lang('GroupSpace'));
		$noPHP_SELF = true;
		$to_group_id = $_SESSION['_gid'];
		$group = GroupManager :: get_group_properties($to_group_id);
		$path = explode('/', $dir);
		if ('/'.$path[1] != $group['directory']) {
			api_not_allowed(true);
		}
	}
	$interbreadcrumb[] = array ("url" => "./document.php?curdirpath=".urlencode($_GET['dir']).$req_gid, "name" => get_lang('Documents'));
} else {
	$interbreadcrumb[]= array (	'url' => '../gradebook/'.$_SESSION['gradebook_dest'], 'name' => get_lang('Gradebook'));
}

if (!$is_allowed_in_course) {
	api_not_allowed(true);
}
if (!($is_allowed_to_edit || $_SESSION['group_member_with_upload_rights'])) {
	api_not_allowed(true);
}

/*	Header */

event_access_tool(TOOL_DOCUMENT);
$display_dir = $dir;
if (isset ($group)) {
	$display_dir = explode('/', $dir);
	unset ($display_dir[0]);
	unset ($display_dir[1]);
	$display_dir = implode('/', $display_dir);
}

// Create a new form
$form = new FormValidator('create_document','post',api_get_self().'?dir='.Security::remove_XSS(urlencode($_GET['dir'])).'&selectcat='.Security::remove_XSS($_GET['selectcat']));

// form title
$form->addElement('header', '', $nameTools);

if (isset($_REQUEST['certificate'])) {//added condition for certicate in gradebook
	$form->addElement('hidden','certificate','true',array('id'=>'certificate'));
	if (isset($_GET['selectcat']))
		$form->addElement('hidden','selectcat',intval($_GET['selectcat']));

}
$renderer = & $form->defaultRenderer();

// Hidden element with current directory
$form->addElement('hidden', 'dir');
$default['dir'] = $dir;
// Filename

$form->addElement('hidden', 'title_edited', 'false', 'id="title_edited"');

/**
 * Check if a document width the choosen filename allready exists
 */
function document_exists($filename) {
	global $filepath;
	$filename = addslashes(trim($filename));
	$filename = Security::remove_XSS($filename);
	$filename = replace_dangerous_char($filename);
	$filename = disable_dangerous_file($filename);
	return !file_exists($filepath.$filename.'.html');
}

// Change the default renderer for the filename-field to display the dir and extension
/*
$renderer = & $form->defaultRenderer();
*/
//$filename_template = str_replace('{element}', "<tt>$display_dir</tt> {element} <tt>.html</tt>", $renderer->_elementTemplate);
$filename_template = str_replace('{element}', '{element}', $renderer->_elementTemplate); // TODO: What is the point of this statement?
$renderer->setElementTemplate($filename_template, 'filename');

// Initialize group array
$group = array();

// If allowed, add element for document title
if (api_get_setting('use_document_title') == 'true') {
	//$group[]= $form->add_textfield('title', get_lang('Title'),true,'class="input_titles" id="title"');
	// replace the 	add_textfield with this
	$group[]=$form->createElement('text','title',get_lang('Title'),'class="input_titles" id="document_title"');
	//$form->applyFilter('title','trim');
	//$form->addRule('title', get_lang('ThisFieldIsRequired'), 'required');

	// Added by Ivan Tcholakov, 10-OCT-2009.
	$form->addElement('hidden', 'filename', '', array('id' => 'filename'));
	//
} else {
	//$form->add_textfield('filename', get_lang('FileName'),true,'class="input_titles" id="filename" onblur="javascript: check_if_still_empty();"');
	// replace the 	add_textfield with this
	$group[]=$form->createElement('text', 'filename', get_lang('FileName'), 'class="input_titles" id="document_title" onblur="javascript: check_if_still_empty();"');
	//$form->applyFilter('filename','trim');
	//$form->addRule('filename', get_lang('ThisFieldIsRequired'), 'required');
	//$form->addRule('filename', get_lang('FileExists'), 'callback', 'document_exists');

	// Added by Ivan Tcholakov, 10-OCT-2009.
	$form->addElement('hidden', 'title', '', array('id' => 'title'));
	//
}

// Show read-only box only in groups
if (!empty($_SESSION['_gid'])) {
	//$renderer->setElementTemplate('<div class="row"><div class="label"></div><div class="formw">{element}{label}</div></div>', 'readonly');
	$group[]= $form->createElement('checkbox', 'readonly', '', get_lang('ReadOnly'));
}

// Add group to the form
if ($is_certificate_mode)
	$form->addGroup($group, 'filename_group', get_lang('CertificateName') ,'&nbsp;&nbsp;&nbsp;', false);
else
	$form->addGroup($group, 'filename_group', api_get_setting('use_document_title') == 'true' ? get_lang('Title') : get_lang('FileName') ,'&nbsp;&nbsp;&nbsp;', false);

$form->addRule('filename_group', get_lang('ThisFieldIsRequired'), 'required');

if (api_get_setting('use_document_title') == 'true') {
	$form->addGroupRule('filename_group', array(
	  'title' => array(
	    array(get_lang('ThisFieldIsRequired'), 'required'),
	    array(get_lang('FileExists'),'callback', 'document_exists')
	    )
	));
} else {
	$form->addGroupRule('filename_group', array(
	  'filename' => array(
		array(get_lang('ThisFieldIsRequired'), 'required'),
	    array(get_lang('FileExists'),'callback', 'document_exists')
	    )
	));
}

$current_session_id = api_get_session_id();

//$form->addElement('style_submit_button', 'submit', get_lang('SaveDocument'), 'class="save"');

// HTML-editor
$renderer->setElementTemplate('<div class="row"><div class="label" id="frmModel" style="overflow: visible;"></div><div class="formw">{element}</div></div>', 'content');
$form->add_html_editor('content','', false, false, $html_editor_config);
// Comment-field

//$form->addElement('textarea', 'comment', get_lang('Comment'), array ('rows' => 5, 'cols' => 50));
if ($is_certificate_mode)
	$form->addElement('style_submit_button', 'submit', get_lang('CreateCertificate'), 'class="save"');
else
	$form->addElement('style_submit_button', 'submit', get_lang('langCreateDoc'), 'class="save"');

$form->setDefaults($default);

// HTML
/*
$form->addElement('html','<div id="frmModel" style="display:block; height:525px; width:240px; position:absolute; top:115px; left:1px;"></div>');
*/

// If form validates -> save the new document
if ($form->validate()) {
	$values = $form->exportValues();
	$readonly = isset($values['readonly']) ? 1 : 0;

	$values['title'] = addslashes(trim($values['title']));
	$values['title'] = Security::remove_XSS($values['title']);
	$values['title'] = replace_dangerous_char($values['title']);
	$values['title'] = disable_dangerous_file($values['title']);

	$values['filename'] = addslashes(trim($values['filename']));
	$values['filename'] = Security::remove_XSS($values['filename']);
	$values['filename'] = replace_dangerous_char($values['filename']);
	$values['filename'] = disable_dangerous_file($values['filename']);

	if (api_get_setting('use_document_title') != 'true') {
		$values['title'] = $values['filename'];
	} else	{
		$values['filename'] = $values['title'];
	}

	$filename = $values['filename'];
	$title = $values['title'];
	$extension = 'html';

	$content = Security::remove_XSS($values['content'], COURSEMANAGERLOWSECURITY);

	if (strpos($content, '/css/frames.css') === false) {
		$content = str_replace('</head>', '<style> body{margin:10px;}</style> <link rel="stylesheet" href="./css/frames.css" type="text/css" /></head>', $content);
	}
	if ($fp = @fopen($filepath.$filename.'.'.$extension, 'w')) {
		$content = text_filter($content);
		$content = str_replace(api_get_path(WEB_COURSE_PATH), $_configuration['url_append'].'/courses/', $content);
		// change the path of mp3 to absolute
		// first regexp deals with ../../../ urls
		// Disabled by Ivan Tcholakov.
		//$content = preg_replace("|(flashvars=\"file=)(\.+/)+|","$1".api_get_path(REL_COURSE_PATH).$_course['path'].'/document/',$content);
		//second regexp deals with audio/ urls
		// Disabled by Ivan Tcholakov.
		//$content = preg_replace("|(flashvars=\"file=)([^/]+)/|","$1".api_get_path(REL_COURSE_PATH).$_course['path'].'/document/$2/',$content);
		fputs($fp, $content);
		fclose($fp);
		chmod($filepath.$filename.'.'.$extension, api_get_permissions_for_new_files());
		if (!is_dir($filepath.'css')) {
			mkdir($filepath.'css', api_get_permissions_for_new_directories());
			$doc_id = add_document($_course, $dir.'css', 'folder', 0, 'css');
			api_item_property_update($_course, TOOL_DOCUMENT, $doc_id, 'FolderCreated', $_user['user_id'], null, null, null, null, $current_session_id);
			api_item_property_update($_course, TOOL_DOCUMENT, $doc_id, 'invisible', $_user['user_id'], null, null, null, null, $current_session_id);
		}

		if (!is_file($filepath.'css/frames.css')) {
			// Make a copy of the current css for the new document
			copy(api_get_path(SYS_CODE_PATH).'css/'.api_get_setting('stylesheets').'/frames.css', $filepath.'css/frames.css');
			$doc_id = add_document($_course, $dir.'css/frames.css', 'file', filesize($filepath.'css/frames.css'), 'frames.css');
			api_item_property_update($_course, TOOL_DOCUMENT, $doc_id, 'DocumentAdded', $_user['user_id'], null, null, null, null, $current_session_id);
			api_item_property_update($_course, TOOL_DOCUMENT, $doc_id, 'invisible', $_user['user_id'], null, null, null, null, $current_session_id);
		}

		$file_size = filesize($filepath.$filename.'.'.$extension);
		$save_file_path = $dir.$filename.'.'.$extension;

		$document_id = add_document($_course, $save_file_path, 'file', $file_size, $filename, null, $readonly);
		if ($document_id) {
			api_item_property_update($_course, TOOL_DOCUMENT, $document_id, 'DocumentAdded', $_user['user_id'], $to_group_id, null, null, null, $current_session_id);
			// Update parent folders
			item_property_update_on_folder($_course, $_GET['dir'], $_user['user_id']);
			$new_comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';
			$new_title = isset($_POST['title']) ? trim($_POST['title']) : '';
			if ($new_comment || $new_title) {
				$TABLE_DOCUMENT = Database::get_course_table(TABLE_DOCUMENT);
				$ct = '';
				if ($new_comment)
					$ct .= ", comment='$new_comment'";
				if ($new_title)
					$ct .= ", title='$new_title'";
				Database::query("UPDATE $TABLE_DOCUMENT SET".substr($ct, 1)." WHERE id = '$document_id'");
			}
			$dir= substr($dir,0,-1);
			$selectcat = '';
			if (isset($_REQUEST['selectcat']))
				$selectcat = "&selectcat=".Security::remove_XSS($_REQUEST['selectcat']);
			header('Location: document.php?curdirpath='.urlencode($dir).$selectcat);
			exit ();
		} else {
			Display :: display_header($nameTools, 'Doc');
			Display :: display_error_message(get_lang('Impossible'));
			Display :: display_footer();
		}
	} else {
		Display :: display_header($nameTools, 'Doc');
		//api_display_tool_title($nameTools);
		Display :: display_error_message(get_lang('Impossible'));
		Display :: display_footer();
	}
} else {
	Display :: display_header($nameTools, "Doc");
	//api_display_tool_title($nameTools);
	// actions
	if (isset($_REQUEST['certificate'])) {
		$all_information_by_create_certificate=DocumentManager::get_all_info_to_certificate();
		$str_info='';
		foreach ($all_information_by_create_certificate[0] as $info_value) {
			$str_info.=$info_value.'<br/>';
		}
		$create_certificate=get_lang('CreateCertificateWithTags');
		Display::display_normal_message($create_certificate.': <br /><br/>'.$str_info,false);
	}
	echo '<div class="actions">';
	// link back to the documents overview
	if ($is_certificate_mode)
		echo '<a href="document.php?curdirpath='.Security::remove_XSS($_GET['dir']).'&selectcat=' . Security::remove_XSS($_GET['selectcat']).'">'.Display::return_icon('back.png',get_lang('Back').' '.get_lang('To').' '.get_lang('CertificateOverview')).get_lang('Back').' '.get_lang('To').' '.get_lang('CertificateOverview').'</a>';
	else
		echo '<a href="document.php?curdirpath='.Security::remove_XSS($_GET['dir']).'">'.Display::return_icon('back.png',get_lang('Back').' '.get_lang('To').' '.get_lang('DocumentsOverview')).get_lang('BackTo').' '.get_lang('DocumentsOverview').'</a>';
	echo '</div>';
	$form->display();
	Display :: display_footer();
}
