<?php // $Id: slideshowoptions.php 9246 2006-09-25 13:24:53Z bmol $ 
/*
==============================================================================
	Dokeos - elearning and course management software

	Copyright (c) 2004 Dokeos S.A.
	Copyright (c) 2003 Ghent University (UGent)
	Copyright (c) 2001 Universite catholique de Louvain (UCL)

	For a full list of contributors, see "credits.txt".
	The full license can be read in "license.txt".

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	See the GNU General Public License for more details.

	Contact: Dokeos, 181 rue Royale, B-1000 Brussels, Belgium, info@dokeos.com
==============================================================================
*/
/**
============================================================================== 
*	@author Patrick Cool
*	@package dokeos.document
============================================================================== 
*/

/*
==============================================================================
Developped by Patrick Cool
patrick.cool@UGent.be
Ghent University
May 2004
http://icto.UGent.be

Please bear in mind that this is only an beta release. 
I wrote this quite quick and didn't think too much about it in advance. 
It is not perfect at all but it is workable and usefull (I think)
Do not consider this as a powerpoint replacement, although it has
the same starting point. 
==============================================================================
*/
/*
==============================================================================
Description:
	This is a plugin for the documents tool. It looks for .jpg, .jpeg, .gif, .png
	files (since these are the files that can be viewed in a browser) and creates
	a slideshow with it by allowing to go to the next/previous image.
	You can also have a quick overview (thumbnail view) of all the images in 
	that particular folder.
	Maybe it is important to notice that each slideshow is folder based. Only
	the images of the chosen folder are shown. 
	
	On this page the options of the slideshow can be set: maintain the original file
	or resize the file to a given width.
==============================================================================

*/
// including the language file
$langFile = "slideshow";

include('../inc/global.inc.php');

$path = $_GET['curdirpath'];
$pathurl = urlencode($path);

// breadcrumb navigation
$url="document.php?curdirpath=".$pathurl;
$originaltoolname=get_lang('Documents');
$interbreadcrumb[]= array ("url"=>$url, "name"=>$originaltoolname );

$url="slideshow.php?curdirpath=".$pathurl;
$originaltoolname=get_lang('lang_slideshow');
$interbreadcrumb[]= array ("url"=>$url, "name"=>$originaltoolname );

// because $nametools uses $_SERVER['PHP_SELF'] for the breadcrumbs instead of $_SERVER['REQUEST_URI'], I had to 
// bypass the $nametools thing and use <b></b> tags in the $interbreadcrump array
$url="slideshowoptions.php?curdirpath=".$pathurl;
$originaltoolname="<b>".get_lang('_slideshow_options')."</b>";
$interbreadcrumb[]= array ("url"=>$url, "name"=>$originaltoolname );

Display::display_header($originalToolName,"Doc");

// can't remember why I put this here. This is probably obsolete code 
// loading the slides from the session
//$image_files_only = $_SESSION["image_files_only"]; 

// calculating the current slide, next slide, previous slide and the number of slides
/*if ($slide_id)
	{
	$slide=$slide_id;
	}
else
	{ 
	$slide=0;  
	}
$previous_slide=$slide-1;
$next_slide=$slide+1;
$total_slides=count($image_files_only); 
*/
?>
<style type="text/css">
<!--
.disabled_input {
	background-color: #cccccc;
}
.enabled_input {
	background-color: #ffffff;
}
-->
</style>

<script language="JavaScript" type="text/JavaScript">
<!--


function enableresizing() { //v2.0
	document.options.width.disabled=false; 
	document.options.width.className='enabled_input'; 
	document.options.height.disabled=false;
	document.options.height.className='enabled_input'; 
}
function disableresizing() { //v2.0
	document.options.width.disabled=true; 
	document.options.width.className='disabled_input'; 
	document.options.height.disabled=true;
	document.options.height.className='disabled_input'; 
}

//-->
</script>

<p></p>
<h2 style="margin-top: 0; margin-bottom: 0"><?php echo get_lang('_slideshow_options'); ?></h2>
<form action="slideshow.php?curdirpath=<?php echo $pathurl; ?>" method="post" name="options" id="options">
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td valign="top"><input class="checkbox" name="radio_resizing" type="radio" onClick="disableresizing()" value="noresizing" 
	  <?php 
	  $image_resizing=$_SESSION["image_resizing"]; 
	  if($image_resizing=="noresizing" or $image_resizing=="")
	  	{
		echo " checked";
		}
		?>
	  ></td>
      <td><?php echo get_lang('_no_resizing');?><br><?php echo get_lang('_no_resizing_comment');?>
          </td>
    </tr>
    <tr>
      <td valign="top"><input class="checkbox" name="radio_resizing" type="radio" onClick="enableresizing()" value="resizing"
	  <?php 
	  $image_resizing=$_SESSION["image_resizing"]; 
	  if($image_resizing=="resizing")
	  	{
		echo " checked";
		$width=$_SESSION["image_resizing_width"];
		$height=$_SESSION["image_resizing_height"];
		} 
		?>></td>
      <td><?php echo get_lang('_resizing');?><br><?php echo get_lang('_resizing_comment');?><br>
        <?php echo get_lang('_width');?>: 
        <input name="width" type="text" id="width" 
		<?php 
		if ($image_resizing=="resizing")
			{
			echo " value='".$width."'";
			echo " class=\"enabled_input\"";
			}
		else
			{echo " class=\"disabled_input\""; }
		?>>        
        <br>
        <?php echo get_lang('_height');?>: 
        <input name="height" type="text" id="height"
		<?php 
		if ($image_resizing=="resizing")
			{
			echo " value='".$height."'";
			echo " class=\"enabled_input\"";
			}
		else
			{echo " class=\"disabled_input\""; }
		?>
		></td>
    </tr>
    <tr>
      <td valign="top">&nbsp;</td>
      <td><input type="submit" name="Submit" value="<?php echo get_lang('Ok'); ?>"></td>
    </tr>
  </table>

</form>
<?php
Display::display_footer();
?>
