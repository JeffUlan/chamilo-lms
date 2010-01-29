<?php

$tempfilename = $_REQUEST['filename'].'.pdf';
$opname = $_REQUEST['opname'];
$dest = $_REQUEST['dest'];
	if ($tempfilename && file_exists('../tmp/'.$tempfilename)) {
		header("Pragma: ");
		header("Cache-Control: private");
		header("Content-transfer-encoding: binary\n");
		if ($dest=='I') {
			header('Content-Type: application/pdf');
			header('Content-disposition: inline; filename='.$opname);
		}

		else if ($dest=='D') {
			if(isset($_SERVER['HTTP_USER_AGENT']) and strpos($_SERVER['HTTP_USER_AGENT'],'MSIE')) {
				if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on') {
					header('HTTP/1.1 200 OK');
					header('Status: 200 OK');
					header('Pragma: anytextexeptno-cache', true);
					header("Cache-Control: public, must-revalidate");
				} 
				else {
					header('Cache-Control: public, must-revalidate');
					header('Pragma: public');
				}
				header('Content-Type: application/force-download');
			} 
			else {
				header('Content-Type: application/octet-stream');
			}
			header('Content-disposition: attachment; filename='.$opname);
		}
		$filesize = filesize('../tmp/'.$tempfilename);
		header("Content-length:".$filesize);
		$fd=fopen('../tmp/'.$tempfilename,'r');
		fpassthru($fd);
		fclose($fd);
		unlink('../tmp/'.$tempfilename);
		// ====================== DELETE OLD FILES FIRST - Housekeeping =========================================
		// Clear any files in directory that are >24 hrs old
		$interval = 86400;
		if ($handle = opendir('../tmp')) {
		   while (false !== ($file = readdir($handle))) { 
			if (((filemtime('../tmp/'.$file)+$interval) < time()) && ($file != "..") && ($file != ".")) { 
				unlink('../tmp/'.$file); 
			}
		   }
		   closedir($handle); 
		}
		exit;
	}
?>