<?php
/* For licensing terms, see /license.txt */

/**
 *	Functions and main code for the download folder feature
 *
 *	@package chamilo.document
 */

$path = $_GET['path'];

// Prevent some stuff
if (empty($path)) {
	$path = '/';
}

// Check to see if they want to download an existing folder
if (($path != '/') && (!DocumentManager::get_document_id($_course, $path))) {
	$path = '/';
}

//zip library for creation of the zipfile
include api_get_path(LIBRARY_PATH).'pclzip/pclzip.lib.php';

// We need this path to clean it out of the zip file
// I'm not using dirname as it gives too much problems (cfr.)
$remove_dir = ($path != '/') ? substr($path, 0, strlen($path) - strlen(basename($path))) : '/';

// Place to temporarily stash the zipfiles
$temp_zip_dir = $sys_course_path.$_course['path']."/temp";
// Create the temp dir if it doesn't exist or do a cleanup befor creating the zipfile

if (!is_dir($temp_zip_dir)) {
	mkdir($temp_zip_dir, api_get_permissions_for_new_directories());
}
// Cleanup: check the temp dir for old files and delete them
else {
	$handle = opendir($temp_zip_dir);
	while (false !== ($file = readdir($handle))) {
		if ($file != '.' && $file != '..') {
			// The "age" of the file in hours
			$diff = (time() - filemtime("$temp_zip_dir/$file"))/60/60;
			// Delete files older than 4 hours
			if ($diff > 4) unlink("$temp_zip_dir/$file");
		}
	}
    closedir($handle);
}

// Create zipfile of given directory
$temp_zip_file = $temp_zip_dir.'/'.md5(time()).'.zip';
$zip_folder = new PclZip($temp_zip_file);
$doc_table = Database::get_course_table(TABLE_DOCUMENT);
$prop_table = Database::get_course_table(TABLE_ITEM_PROPERTY);
// Put the files in the zip
// 2 possibilities: Admins get all files and folders in the selected folder (except for the deleted ones)
// Normal users get only visible files that are in visible folders

// Admins are allowed to download invisible files
if (is_allowed_to_edit()) {
	// Folder we want to zip --> no longer used, deleted files are included too like this
 	//$what_to_zip = $sys_course_path.$_course['path'].'/document'.$path;
 	// Creation of the zipped folder
	//$zip_folder->create($what_to_zip ,PCLZIP_OPT_REMOVE_PATH, $sys_course_path.$_course['path'].'/document'.$remove_dir );
	// Set the path that will be used in the query
	if ($path == '/') {
		$querypath = ''; // To prevent ...path LIKE '//%'... in query
	} else {
		$querypath = $path;
	}
	// Search for all files that are not deleted => visibility != 2
	$query = Database::query("SELECT path FROM $doc_table AS docs,$prop_table AS props  WHERE `props`.`tool`='".TOOL_DOCUMENT."' AND `docs`.`id`=`props`.`ref` AND `docs`.`path` LIKE '".$querypath."/%' AND `docs`.`filetype`='file' AND `props`.`visibility`<>'2' AND `props`.`to_group_id`=".$to_group_id."");
	// Add tem to the zip file
	while ($not_deleted_file = Database::fetch_assoc($query)) {
		$zip_folder->add($sys_course_path.$_course['path'].'/document'.$not_deleted_file['path'], PCLZIP_OPT_REMOVE_PATH, $sys_course_path.$_course['path'].'/document'.$remove_dir);
	}
}
// For other users, we need to create a zipfile with only visible files and folders
else {
	if ($path == '/') {
		$querypath = ''; // To prevent ...path LIKE '//%'... in query
	} else {
		$querypath = $path;
	}
	// A big problem: Visible files that are in a hidden folder are included when we do a query for visiblity='v'
	// So... I do it in a couple of steps:
	// 1st: Get all files that are visible in the given path
	$query = Database::query("SELECT path FROM $doc_table AS docs,$prop_table AS props WHERE `props`.`tool`='".TOOL_DOCUMENT."' AND `docs`.`id`=`props`.`ref` AND `docs`.`path` LIKE '".$querypath."/%' AND `props`.`visibility`='1' AND `docs`.`filetype`='file' AND `props`.`to_group_id`=".$to_group_id);
	// Add them to an array
	while ($all_visible_files = Database::fetch_assoc($query)) {
		$all_visible_files_path[] = $all_visible_files['path'];
		//echo "visible files: ".$sys_course_path.$_course['path'].'/document'.$all_visible_files['path']."<br>";
	}
	//echo('<pre>');
	//print_r($all_visible_files_path);
	//echo('</pre>');
	// 2nd: Get all folders that are invisible in the given path
	$query2 = Database::query("SELECT path FROM $doc_table AS docs,$prop_table AS props WHERE `props`.`tool`='".TOOL_DOCUMENT."' AND `docs`.`id`=`props`.`ref` AND `docs`.`path` LIKE '".$querypath."/%' AND `props`.`visibility`<>'1' AND `docs`.`filetype`='folder'");
	// If we get invisible folders, we have to filter out these results from all visible files we found
	if (Database::num_rows($query2) > 0) {
		// Add tem to an array
		while ($invisible_folders = Database::fetch_assoc($query2)) {
		//3rd: Get all files that are in the found invisible folder (these are "invisible" too)
			//echo "<br /><br />invisible folders: ".$sys_course_path.$_course['path'].'/document'.$invisible_folders['path'].'<br />';
			$query3 = Database::query("SELECT path FROM $doc_table AS docs,$prop_table AS props  WHERE `props`.`tool`='".TOOL_DOCUMENT."' AND `docs`.`id`=`props`.`ref` AND `docs`.`path` LIKE '".$invisible_folders['path']."/%' AND `docs`.`filetype`='file' AND `props`.`visibility`='1'");
			// Add tem to an array
			while ($files_in_invisible_folder = Database::fetch_assoc($query3)) {
				$files_in_invisible_folder_path[] = $files_in_invisible_folder['path'];
				//echo '<br /><br />files in invisible folders: '.$sys_course_path.$_course['path'].'/document'.$files_in_invisible_folder['path'].' <b>id '.$files_in_invisible_folder['id'].'</b><br />';
			}
		}
		// Compare the array with visible files and the array with files in invisible folders
		// and keep the difference (= all visible files that are not in an invisible folder)
		$files_for_zipfile = diff((array)$all_visible_files_path, (array)$files_in_invisible_folder_path);
	}
	// No invisible folders found, so all visible files can be added to the zipfile
	else {
		$files_for_zipfile = $all_visible_files_path;
	}
	// Add all files in our final array to the zipfile
	//echo("path to remove from file ".$sys_course_path.$_course['path']."/document".$remove_dir.'<br>');
	//echo('<b>FILES FOR ZIP</b><br />');
	//print_r($files_for_zipfile);
	for ($i = 0; $i < count($files_for_zipfile); $i++) {
		$zip_folder->add($sys_course_path.$_course['path'].'/document'.$files_for_zipfile[$i], PCLZIP_OPT_REMOVE_PATH, $sys_course_path.$_course['path'].'/document'.$remove_dir);
		//echo $sys_course_path.$_course['path'].'/document'.$files_for_zipfile[$i].'<br />';
	}
} // end for other users
//exit;

// Launch event
event_download(($path == '/') ? 'documents.zip (folder)' : basename($path).'.zip (folder)');

// Start download of created file
//send_file_to_client($temp_zip_file, basename(empty($_GET['id']) ? 'documents' : $_GET['id']).'.zip');
$name = ($path=='/') ? 'documents.zip' : basename($path).'.zip';

DocumentManager::file_send_for_download($temp_zip_file, true, $name);
@unlink($temp_zip_file);
exit;

/**
 * Returns the difference between two arrays, as an array of those key/values
 * Use this as array_diff doesn't give the
 *
 * @param array $arr1 first array
 * @param array $arr2 second array
 * @return difference between the two arrays
 */
function diff($arr1, $arr2) {
	$res = array();
	$r = 0;
	foreach ($arr1 as & $av) {
		if (!in_array($av, $arr2)) {
			$res[$r] = $av;
			$r++;
		}
	}
	return $res;
}
