<?php //$id: $
/**
 * Script allowing simple edition of learnpath information (title, description, etc)
 * @package dokeos.learnpath
 * @author Yannick Warnier <ywarnier@beeznest.org>
*/

require_once (api_get_path(LIBRARY_PATH).'formvalidator/FormValidator.class.php');

$show_description_field = false; //for now
$nameTools = get_lang("Doc");
event_access_tool(TOOL_LEARNPATH);

if (! $is_allowed_in_course) api_not_allowed();

$interbreadcrumb[]= array ("url"=>"lp_controller.php?action=list", "name"=> get_lang("_learning_path"));
$interbreadcrumb[]= array ("url"=>api_get_self()."?action=admin_view&lp_id=$learnpath_id", "name" => $_SESSION['oLP']->get_name());

Display::display_header(null,'Path');

//Page subtitle
echo '<h4>'.get_lang('_edit_learnpath').'</h4>';

$fck_attribute['Width'] = '400px';
$fck_attribute['Height'] = '150px';
$fck_attribute['ToolbarSet'] = 'Comment';

$defaults=array();
$form = new FormValidator('form1', 'post', 'lp_controller.php');

//Title
$form -> addElement('text', 'lp_name', ucfirst(get_lang('_title')));

//Encoding
$encoding_select = &$form->addElement('select', 'lp_encoding', get_lang('Charset'));
$encodings = array('UTF-8','ISO-8859-1','ISO-8859-15','cp1251','cp1252','KOI8-R','BIG5','GB2312','Shift_JIS','EUC-JP');
foreach($encodings as $encoding){
	if($encoding == $_SESSION['oLP']->encoding){
  		$s_selected_encoding = $encoding;
  	}
  	$encoding_select->addOption($encoding,$encoding);
}


//Origin
$origin_select = &$form->addElement('select', 'lp_maker', get_lang('Origin'));
$lp_orig = $_SESSION['oLP']->get_maker();

include('content_makers.inc.php');
foreach($content_origins as $origin){
	if($lp_orig == $origin){
		$s_selected_origin = $origin;
	}
	$origin_select->addOption($origin,$origin);
}


//Content proximity
$content_proximity_select = &$form->addElement('select', 'lp_proximity', get_lang('ContentProximity'));
$lp_prox = $_SESSION['oLP']->get_proximity();
if($lp_prox != 'local'){
	$s_selected_proximity = 'remote';
}else{
	$s_selected_proximity = 'local';
}
$content_proximity_select->addOption(get_lang('Local'), 'local');
$content_proximity_select->addOption(get_lang('Remote'), 'remote');


if (api_get_setting('allow_course_theme') == 'true')
{		
	$mycourselptheme=api_get_course_setting('allow_learning_path_theme');
	if (!empty($mycourselptheme) && $mycourselptheme!=-1 && $mycourselptheme== 1) 
	{			
		//LP theme picker				
		$theme_select = &$form->addElement('select_theme', 'lp_theme', get_lang('Theme'));
		$form->applyFilter('lp_theme', 'trim');
		
		$s_theme = $_SESSION['oLP']->get_theme();
		$theme_select ->setSelected($s_theme); //default	
	}	
}

//Author
//$form -> addElement('text', 'lp_author', ucfirst(get_lang('Author')));
//$form->add_html_editor('lp_author', get_lang('Author')); 

$form->addElement('html_editor','lp_author',get_lang('Author')); 

// LP image	
$form->add_progress_bar();
if( strlen($_SESSION['oLP']->get_preview_image() ) > 0)
{
	$show_preview_image='<img src='.api_get_path(WEB_COURSE_PATH).api_get_course_path().'/upload/learning_path/images/'.$_SESSION['oLP']->get_preview_image().'>';
	$div = '<div class="row">
	<div class="label">'.get_lang('ImagePreview').'</div>
	<div class="formw">	
	'.$show_preview_image.'
	</div>
	</div>';	
	$form->addElement('html', $div .'<br/>');	
	$form->addElement('checkbox', 'remove_picture', null, get_lang('DelImage'));	
}

$form->addElement('file', 'lp_preview_image', ($_SESSION['oLP']->get_preview_image() != '' ? get_lang('UpdateImage') : get_lang('AddImage')));

	$div = '<div class="row">
	<div class="label"></div>
	<div class="formw">	'.get_lang('ImageWillResizeMsg').'
	</div>
	</div>';
$form->addElement('html', $div);

$form->addRule('lp_preview_image', get_lang('OnlyImagesAllowed'), 'mimetype', array('image/gif', 'image/jpeg', 'image/png'));


//default values
$content_proximity_select -> setSelected($s_selected_proximity);
$origin_select -> setSelected($s_selected_origin);
$encoding_select -> setSelected($s_selected_encoding);
$defaults['lp_name']=$_SESSION['oLP']->get_name();
$defaults['lp_author']=$_SESSION['oLP']->get_author();

//Submit button
$form->addElement('submit', 'Submit', get_lang('Ok'));

//Hidden fields
$form->addElement('hidden', 'action', 'update_lp');
$form->addElement('hidden', 'lp_id', $_SESSION['oLP']->get_id());

$form->setDefaults($defaults);
$form -> display();
Display::display_footer();
?>