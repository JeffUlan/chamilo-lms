<?php //$id: $
/**
 * Script that displays an error message when no content could be loaded
 * @package dokeos.learnpath 
 * @author Yannick Warnier <ywarnier@beeznest.org>
 */
/**
 * Nothing very interesting
 */
$debug = 0;
if($debug>0){error_log('New lp - In lp_content.php',0);}
if(empty($lp_controller_touched)){
	if($debug>0){error_log('New lp - In lp_content.php - Redirecting to lp_controller',0);}
	header('location: lp_controller.php?action=content&lp_id='.$_REQUEST['lp_id'].'&item_id='.$_REQUEST['item_id']);
}
$_SESSION['oLP']->error = '';
$lp_type = $_SESSION['oLP']->get_type();
$lp_item_id = $_SESSION['oLP']->get_current_item_id();

/**
 * Get a link to the corresponding document
 */
$src = '';
if($debug>0){error_log('New lp - In lp_content.php - Looking for file url',0);}
switch($lp_type){
	case 1:
		$_SESSION['oLP']->stop_previous_item();
		$prereq_check = $_SESSION['oLP']->prerequisites_match($lp_item_id);
		if($prereq_check === true){
			$src = $_SESSION['oLP']->get_link('http',$lp_item_id);
			$_SESSION['oLP']->start_current_item(); //starts time counter manually if asset
		}else{
			$src = 'blank.php?error=prerequisites';
		}		
		break;
	case 2:
	case 3:
		//save old if asset
		$_SESSION['oLP']->stop_previous_item(); //save status manually if asset
		$prereq_check = $_SESSION['oLP']->prerequisites_match($lp_item_id);
		if($prereq_check === true){
			$src = $_SESSION['oLP']->get_link('http',$lp_item_id);
			$_SESSION['oLP']->start_current_item(); //starts time counter manually if asset
		}else{
			$src = 'blank.php';
		}
		break;
	case 4:
		break;
}
if($debug>0){error_log('New lp - In lp_content.php - File url is '.$src,0);}
$_SESSION['oLP']->set_previous_item($lp_item_id);

// Define the 'doc.inc.php' as language file
$nameTools = $_SESSION['oLP']->get_name();
$interbreadcrumb[]= array ("url"=>"./lp_list.php", "name"=> get_lang('Doc'));
//update global setting to avoid displaying right menu
$save_setting = get_setting("show_navigation_menu");
global $_setting;
$_setting['show_navigation_menu'] = false;
if($debug>0){error_log('New LP - In lp_content.php - Loading '.$src,0);}
header("Location: $src");
?>