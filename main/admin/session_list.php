<?php
/* For licensing terms, see /dokeos_license.txt */
$language_file='admin';
$cidReset=true;

include('../inc/global.inc.php');
require_once (api_get_path(LIBRARY_PATH).'formvalidator/FormValidator.class.php');
require_once (api_get_path(LIBRARY_PATH).'sessionmanager.lib.php');

$this_section = SECTION_PLATFORM_ADMIN;
api_protect_admin_script(true);

$htmlHeadXtra[] = '<script language="javascript">

				function selectAll(idCheck,numRows,action) {

					for(i=0;i<numRows;i++) {
						idcheck = document.getElementById(idCheck+"_"+i);
						if (action == "true"){
							idcheck.checked = true;
						} else {
							idcheck.checked = false;
						}
					}

				}

				</script>
		';

$tbl_session=Database::get_main_table(TABLE_MAIN_SESSION);
$tbl_session_category=Database::get_main_table(TABLE_MAIN_SESSION_CATEGORY);
$tbl_session_rel_course=Database::get_main_table(TABLE_MAIN_SESSION_COURSE);
$tbl_session_rel_course_rel_user=Database::get_main_table(TABLE_MAIN_SESSION_COURSE_USER);
$tbl_session_rel_user=Database::get_main_table(TABLE_MAIN_SESSION_USER);
$tbl_user = Database::get_main_table(TABLE_MAIN_USER);

$page=intval($_GET['page']);
$action=$_REQUEST['action'];
$sort=in_array($_GET['sort'],array('name', 'nbr_courses', 'name_category', 'date_start', 'date_end','visibility'))?$_GET['sort']:'name';
$idChecked = $_REQUEST['idChecked'];
$id_category = intval($_REQUEST['id_category']);
$cond_url = '';
if ($action == 'delete') {
	SessionManager::delete_session($idChecked);
	header('Location: '.api_get_self().'?sort='.$sort);
	exit();
}

$interbreadcrumb[]=array("url" => "index.php","name" => get_lang('PlatformAdmin'));

if (isset ($_GET['search']) && $_GET['search'] == 'advanced') {

	$interbreadcrumb[] = array ("url" => 'session_list.php', "name" => get_lang('SessionList'));
	$tool_name = get_lang('SearchASession');
	Display :: display_header($tool_name);

	$form = new FormValidator('advanced_search','get');
	$form->addElement('header', '', $tool_name);
	$active_group = array();
	$active_group[] = $form->createElement('checkbox','active','',get_lang('Active'));
	$active_group[] = $form->createElement('checkbox','inactive','',get_lang('Inactive'));
	$form->addGroup($active_group,'',get_lang('ActiveSession'),'<br/>',false);

	$form->addElement('style_submit_button', 'submit',get_lang('Search'),'class="search"');
	$defaults['active'] = 1;
	$defaults['inactive'] = 1;
	$form->setDefaults($defaults);
	$form->display();

} else {

	$limit=20;
	$from=$page * $limit;
	$where = '';
	//if user is crfp admin only list its sessions
	if(!api_is_platform_admin()) {
		$where = 'WHERE session_admin_id='.intval($_user['user_id']);
		$where .= (empty($_REQUEST['keyword']) ? "" : " AND s.name LIKE '%".addslashes($_REQUEST['keyword'])."%'");
	} else {
		$where .= (empty($_REQUEST['keyword']) ? "" : " WHERE s.name LIKE '%".addslashes($_REQUEST['keyword'])."%'");
	}
	if (isset($_REQUEST['active']) && isset($_REQUEST['inactive'] )) {
		// if both are set we search all sessions
		$cond_url = '&amp;active='.Security::remove_XSS($_REQUEST['active']);
		$cond_url .= '&amp;inactive='.Security::remove_XSS($_REQUEST['inactive']);
	} else {
		if (isset($_REQUEST['active'])) {
			if (empty($where)) {
				$where .= ' WHERE ( (s.date_start <= CURDATE() AND s.date_end >= CURDATE()) OR s.date_start="0000-00-00" ) ';	
			} else {
				$where .= ' AND ( (s.date_start <= CURDATE() AND s.date_end >= CURDATE()) OR s.date_start="0000-00-00" ) ';
			}
			$cond_url = '&amp;active='.Security::remove_XSS($_REQUEST['active']);
		}
		if (isset($_REQUEST['inactive'])) {
			if (empty($where)) {
				$where .= ' WHERE ( (s.date_start > CURDATE() OR s.date_end < CURDATE()) AND s.date_start<>"0000-00-00" ) ';
			} else {
				$where .= ' AND ( (s.date_start > CURDATE() OR s.date_end < CURDATE()) AND s.date_start<>"0000-00-00" ) ';
			}	
			$cond_url = '&amp;inactive='.Security::remove_XSS($_REQUEST['inactive']);
		}
	}
	if(isset($_GET['id_category'])){
		$where.= (empty($where))? ' AND ' : ' WHERE ';
		$id_category = Security::remove_XSS($id_category); 
		$where.= ' session_category_id = "'.$id_category.'" ';
		$cond_url.= '&amp;id_category='.$id_category;	
	}
	$sort = ($sort != "name_category")?  's.'.$sort : 'category_name';
	$query = "SELECT s.id, s.name, s.nbr_courses, s.date_start, s.date_end, u.firstname, u.lastname , sc.name as category_name, s.visibility
			 FROM $tbl_session s 
			 	LEFT JOIN  $tbl_session_category sc ON s.session_category_id = sc.id 
			 	INNER JOIN $tbl_user u ON s.id_coach = u.user_id 
			 $where 
			 ORDER BY $sort ";
	//query which allows me to get a record without taking into account the page
	$query_rows = "SELECT count(*) as total_rows 
			 FROM $tbl_session s 
			 	LEFT JOIN  $tbl_session_category sc ON s.session_category_id = sc.id
			 	INNER JOIN $tbl_user u ON s.id_coach = u.user_id 
			 $where ";

//filtering the session list by access_url
	if ($_configuration['multiple_access_urls'] == true){
		$table_access_url_rel_session= Database::get_main_table(TABLE_MAIN_ACCESS_URL_REL_SESSION);	
		$access_url_id = api_get_current_access_url_id();
		if ($access_url_id != -1) {
			$where.= " AND ar.access_url_id = $access_url_id ";
			$query = "SELECT s.id, s.name, s.nbr_courses, s.date_start, s.date_end, u.firstname, u.lastname , sc.name as category_name , s.visibility
			 FROM $tbl_session s 
			 	LEFT JOIN  $tbl_session_category sc ON s.session_category_id = sc.id
			 	INNER JOIN $tbl_user u ON s.id_coach = u.user_id 
				INNER JOIN $table_access_url_rel_session ar ON ar.session_id = s.id
			 $where 
			 ORDER BY $sort LIMIT $from,".($limit+1);

			$query_rows = "SELECT count(*) as total_rows 
			 FROM $tbl_session s 
			 	LEFT JOIN  $tbl_session_category sc ON s.session_category_id = sc.id  
			 	INNER JOIN $tbl_user u ON s.id_coach = u.user_id 
			 	INNER JOIN $table_access_url_rel_session ar ON ar.session_id = s.id 
			 $where ";
		}
	}

	$result_rows = Database::query($query_rows,__FILE__,__LINE__);
	$recorset = Database::fetch_array($result_rows);
	$num = $recorset['total_rows'];
	$result=Database::query($query,__FILE__,__LINE__);
	$Sessions=Database::store_result($result);
	$nbr_results=sizeof($Sessions);
	$tool_name = get_lang('SessionList');
	Display::display_header($tool_name);
	//api_display_tool_title($tool_name);

    if (!empty($_GET['warn'])) {
        Display::display_warning_message(urldecode($_GET['warn']),false);
    }
    if(isset($_GET['action'])) {
        Display::display_normal_message(stripslashes($_GET['message']),false);
    }
	?>
	<div class="actions">
	<?php

	echo '<div style="float:right;">
		<a href="'.api_get_path(WEB_CODE_PATH).'admin/add_many_session_to_category.php">'.Display::return_icon('view_more_stats.gif',get_lang('AddSessionsInCategories')).get_lang('AddSessionsInCategories').'</a>
		<a href="'.api_get_path(WEB_CODE_PATH).'admin/session_add.php">'.Display::return_icon('view_more_stats.gif',get_lang('AddSession')).get_lang('AddSession').'</a>									
	  </div>';
	?>
	<form method="POST" action="session_list.php">
		<input type="text" name="keyword" value="<?php echo Security::remove_XSS($_GET['keyword']); ?>"/>
		<button class="search" type="submit" name="name" value="<?php echo get_lang('Search') ?>"><?php echo get_lang('Search') ?></button>
		<a href="session_list.php?search=advanced"><?php echo get_lang('AdvancedSearch'); ?></a>
		</form>
	<form method="post" action="<?php echo api_get_self(); ?>?action=delete&sort=<?php echo $sort; ?>" onsubmit="javascript:if(!confirm('<?php echo get_lang('ConfirmYourChoice'); ?>')) return false;">
	 </div><br />

	<div align="left">
	<?php
	if(count($Sessions)==0 && isset($_POST['keyword'])) {
		echo get_lang('NoSearchResults');
		echo '	</div>';
	} else {
		if($num>$limit){
			if($page) {
			?>
			<a href="<?php echo api_get_self(); ?>?page=<?php echo $page-1; ?>&sort=<?php echo $sort; ?>&keyword=<?php echo $_REQUEST['keyword']; ?><?php echo @$cond_url; ?>"><?php echo get_lang('Previous'); ?></a>
			<?php
			} else {
				echo get_lang('Previous');
			}
			?>
			|
			<?php
			if($nbr_results > $limit) {
				?>
				<a href="<?php echo api_get_self(); ?>?page=<?php echo $page+1; ?>&sort=<?php echo $sort; ?>&keyword=<?php echo $_REQUEST['keyword']; ?><?php echo @$cond_url; ?>"><?php echo get_lang('Next'); ?></a>
				<?php
			} else {
				echo get_lang('Next');
			}
		}
		?>
	</div>
		<br />
		<table class="data_table" width="100%">
		<tr>
		  <th>&nbsp;</th>
		  <th><a href="<?php echo api_get_self(); ?>?sort=name"><?php echo get_lang('NameOfTheSession'); ?></a></th>
		  <th><a href="<?php echo api_get_self(); ?>?sort=nbr_courses"><?php echo get_lang('NumberOfCourses'); ?></a></th>
		  <th><a href="<?php echo api_get_self(); ?>?sort=name_category<?php echo $cond_url; ?>"><?php echo get_lang('SessionCategoryName'); ?></a></th>
		  <th><a href="<?php echo api_get_self(); ?>?sort=date_start"><?php echo get_lang('StartDate'); ?></a></th>
		  <th><a href="<?php echo api_get_self(); ?>?sort=date_end"><?php echo get_lang('EndDate'); ?></a></th>
		  <th><a href="<?php echo api_get_self(); ?>?sort=coach_name"><?php echo get_lang('Coach'); ?></a></th>
		  <th><a href="<?php echo api_get_self(); ?>?sort=visibility<?php echo $cond_url; ?>"><?php echo get_lang('Visibility'); ?></a></th>
		  <th><?php echo get_lang('Actions'); ?></th>
		</tr>

		<?php
		$i=0;
		$x=0;
		foreach ($Sessions as $key=>$enreg) {
			if($key == $limit) {
				break;
			}
			$sql = 'SELECT COUNT(course_code) FROM '.$tbl_session_rel_course.' WHERE id_session='.intval($enreg['id']);

		  	$rs = Database::query($sql, __FILE__, __LINE__);
		  	list($nb_courses) = Database::fetch_array($rs);

		?>

		<tr class="<?php echo $i?'row_odd':'row_even'; ?>">
		  <td><input type="checkbox" id="idChecked_<?php echo $x; ?>" name="idChecked[]" value="<?php echo $enreg['id']; ?>"></td>
	      <td><a href="resume_session.php?id_session=<?php echo $enreg['id']; ?>"><?php echo api_htmlentities($enreg['name'],ENT_QUOTES,$charset); ?></a></td>
	      <td><a href="session_course_list.php?id_session=<?php echo $enreg['id']; ?>"><?php echo $nb_courses; ?> cours</a></td>
	      <td><?php echo api_htmlentities($enreg['category_name'],ENT_QUOTES,$charset); ?></td>
	      <td><?php echo api_htmlentities($enreg['date_start'],ENT_QUOTES,$charset); ?></td>
	      <td><?php echo api_htmlentities($enreg['date_end'],ENT_QUOTES,$charset); ?></td>
	      <td><?php echo api_htmlentities(api_get_person_name($enreg['firstname'], $enreg['lastname']),ENT_QUOTES,$charset); ?></td>
		  <td><?php
		  
		  switch (intval($enreg['visibility'])) {
				case SESSION_VISIBLE_READ_ONLY: //1
					echo get_lang('ReadOnly');
				break;
				case SESSION_VISIBLE:			//2
					echo get_lang('Visible');
				break;
				case SESSION_INVISIBLE:			//3
					echo api_ucfirst(get_lang('Invisible'));
				break;				 
		  }
		   
		  
		  ?></td>
		  <td>
			<a href="add_users_to_session.php?page=session_list.php&id_session=<?php echo $enreg['id']; ?>"><?php Display::display_icon('add_user_big.gif', get_lang('SubscribeUsersToSession')); ?></a>
			<a href="add_courses_to_session.php?page=session_list.php&id_session=<?php echo $enreg['id']; ?>"><?php Display::display_icon('course_add.gif', get_lang('SubscribeCoursesToSession')); ?></a>
			<a href="session_edit.php?page=session_list.php&id=<?php echo $enreg['id']; ?>"><?php Display::display_icon('edit.gif', get_lang('Edit')); ?></a>
			<a href="<?php echo api_get_self(); ?>?sort=<?php echo $sort; ?>&action=delete&idChecked=<?php echo $enreg['id']; ?>" onclick="javascript:if(!confirm('<?php echo get_lang('ConfirmYourChoice'); ?>')) return false;"><?php Display::display_icon('delete.gif', get_lang('Delete')); ?></a>
		  </td>
		</tr>

		<?php
			$i=$i ? 0 : 1;
			$x++;
		}

		unset($Sessions);

		?>

		</table>

		<br />

		<div align="left">

		<?php

		if($num>$limit) {
		if($page)
			{
			?>

			<a href="<?php echo api_get_self(); ?>?page=<?php echo $page-1; ?>&sort=<?php echo $sort; ?>&keyword=<?php echo $_REQUEST['keyword']; ?><?php echo @$cond_url; ?>"><?php echo get_lang('Previous'); ?></a>

			<?php
			}
			else
			{
				echo get_lang('Previous');
			}
			?>

			|

			<?php
			if($nbr_results > $limit)
			{
			?>

			<a href="<?php echo api_get_self(); ?>?page=<?php echo $page+1; ?>&sort=<?php echo $sort; ?>&keyword=<?php echo $_REQUEST['keyword']; ?><?php echo @$cond_url; ?>"><?php echo get_lang('Next'); ?></a>

			<?php
			}
			else
			{
				echo get_lang('Next');
			}
		}
		?>

		</div>

		<br />
		<a href="javascript: void(0);" onclick="javascript: selectAll('idChecked',<?php echo $x; ?>,'true');return false;"><?php echo get_lang('SelectAll') ?></a>&nbsp;-&nbsp;
		<a href="javascript: void(0);" onclick="javascript: selectAll('idChecked',<?php echo $x; ?>,'false');return false;"><?php echo get_lang('UnSelectAll') ?></a>
		<select name="action">
		<option value="delete"><?php echo get_lang('DeleteSelectedSessions'); ?></option>
		</select>
		<button class="save" type="submit" name="name" value="<?php echo get_lang('Ok') ?>"><?php echo get_lang('Ok') ?></button>
		<?php } ?>
	</table>
<?php
}
Display::display_footer();
?>