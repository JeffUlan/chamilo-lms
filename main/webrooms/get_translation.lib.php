<?php
/**
 * Library for language translation from Dokeos language files to XML for videoconference
 * @uses main_api.lib.php for api_get_path() 
 */
/**
 * This function reads a Dokeos language file and transforms it into XML, 
 * then returns the XML string to the caller. 
 */
function get_language_file_as_xml($language='english')
{
	$path = api_get_path(SYS_LANG_PATH).$language.'/';
	if(!is_dir($path) or !is_readable($path))
	{ 
		if($language != 'english')
		{
			return get_language_file_as_xml('english');
		}
		else
		{
			return '';
		}
	}
	error_log('Analysing path '.$path);
	$file = $path.'videoconf.inc.php';
	if(!is_file($file) or !is_readable($file))
	{
		if($language != 'english')
		{
			return get_language_file_as_xml('english');
		}
		else
		{
			return '';
		}
	}
	$list = file($file);
	$xml = '';
	foreach ( $list as $line )
	{
		if(substr($line,0,1)=='$')
		{
			$items = array();
			$match = preg_match('/^\$([^\s]*)\s*=\s*"(.*)";$/',$line,$items);
			if($match)
			{
				//todo: The following conversion should only happen for old language files (encoded in ISO-8859-1).
				$string = iconv('ISO-8859-1','UTF-8',$items[2]);
				$xml .= '<labelfield><labelid>'.$items[1].'</labelid><labelvalue>'.$string.'</labelvalue></labelfield>'."\n";
			}
		}
	}
	if(empty($xml) && $language!='english')
	{
		return get_language_file_as_xml('english');
	}
	return $xml;
}
?>