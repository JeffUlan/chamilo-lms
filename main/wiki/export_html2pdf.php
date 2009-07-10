<?php
/*
==============================================================================
	Dokeos - elearning and course management software

	Copyright (c) 2004-2009 Dokeos SPRL

	For a full list of contributors, see "credits.txt".
	The full license can be read in "license.txt".

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	See the GNU General Public License for more details.

	Contact address: Dokeos, rue du Corbeau, 108, B-1030 Brussels, Belgium
	Mail: info@dokeos.com
==============================================================================
*/

/**
*   Export html to pdf
* 	@Author Juan Carlos Raña <herodoto@telefonica.net>
* 	
*/

 
include("../inc/global.inc.php");
api_block_anonymous_users();

require('../inc/lib/html2pdf/html2pdf.class.php');

$contentPDF=stripslashes(api_html_entity_decode($_POST['contentPDF'], ENT_QUOTES, $charset)); 
$titlePDF=stripslashes(api_html_entity_decode($_POST['titlePDF'], ENT_QUOTES, $charset)); 

ob_start();//activate Output -Buffer
echo $contentPDF;
$htmlbuffer=ob_get_contents();// Store Output-Buffer in one variable
ob_end_clean();// delete Output-Buffer

/////bridge to  dokeos lang
	@ $langhtml2pdf = Database :: get_language_isocode($language_interface);

	// Some code translations are needed.
	$langhtml2pdf = strtolower(str_replace('_', '-', $langhtml2pdf));
	if (empty ($langhtml2pdf))
	{
		$langhtml2pdf = 'en';
	}
	switch ($langhtml2pdf)
	{
		case 'uk':
			$langhtml2pdf = 'ukr';
			break;
		case 'pt':
			$langhtml2pdf = 'pt_pt';
			break;
		case 'pt-br':
			$langhtml2pdf = 'pt_br';
			break;
		// Code here other noticed exceptions.
	}

	// Checking for availability of a corresponding language file.
	if (!file_exists(api_get_path(SYS_PATH).'main/inc/lib/html2pdf/langues/'.$langhtml2pdf.'.txt'))
	{
		// If there was no language file, use the english one.
		$langhtml2pdf = 'en';
	}

////

//$script = "
//var rep = app.response('Your name');
//app.alert('Hello '+rep);
//";

$html2pdf = new HTML2PDF('P','A4',$langhtml2pdf, array(30,25,30,25));//array (margin left, margin top, margin right, margin bottom)
$html2pdf->pdf->SetMyFooter( 'page','','','' );//page, date, time, form
$html2pdf->pdf->SetDisplayMode('real');
//$html2pdf->pdf->IncludeJS($script);
//$html2pdf->pdf->IncludeJS("print(true);");
//$html2pdf->pdf->IncludeJS("app.alert('Generated by Dokeos to PDF');");
//$html2pdf->pdf->SetProtection(array('print'), 'guest');//add a password sample: guest
$html2pdf->pdf->SetAuthor('Wiki Dokeos');
$html2pdf->pdf->SetTitle($titlePDF);
$html2pdf->pdf->SetSubject('Exported from Dokeos Wiki');
$html2pdf->pdf->SetKeywords('Dokeos Wiki');
$html2pdf->WriteHTML(utf8_decode($htmlbuffer));
$html2pdf->Output($titlePDF.'.pdf', 'D');
?>