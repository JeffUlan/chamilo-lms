<?php
/*
    DOKEOS - elearning and course management software

    For a full list of contributors, see documentation/credits.html

    This program is free software; you can redistribute it and/or
    modify it under the terms of the GNU General Public License
    as published by the Free Software Foundation; either version 2
    of the License, or (at your option) any later version.
    See "documentation/licence.html" more details.

    Contact:
		Dokeos
		Rue des Palais 44 Paleizenstraat
		B-1030 Brussels - Belgium
		Tel. +32 (2) 211 34 56
*/


/**
*	This file generates the ActionScript variables code used by the HotSpot .swf
*	@package dokeos.exercise
* 	@author Toon Keppens
* 	@version $Id: admin.php 10680 2007-01-11 21:26:23Z pcool $
*/




include('exercise.class.php');
include('question.class.php');
include('answer.class.php');
include('../inc/global.inc.php');
// set vars
$questionId    = $_GET['modifyAnswers'];
$objQuestion = Question::read($questionId);

$TBL_ANSWERS   = Database::get_course_table(TABLE_QUIZ_ANSWER);
$documentPath  = api_get_path(SYS_COURSE_PATH).$_course['path'].'/document';

$picturePath   = $documentPath.'/images';
$pictureName   = $objQuestion->selectPicture();
$pictureSize   = getimagesize($picturePath.'/'.$objQuestion->selectPicture());
$pictureWidth  = $pictureSize[0];
$pictureHeight = $pictureSize[1];

$courseLang = $_course['language'];
$courseCode = $_course['sysCode'];
$coursePath = $_course['path'];

// Query db for answers
//$sql = "SELECT id, answer, hotspot_coordinates, hotspot_type, ponderation FROM $TBL_ANSWERS WHERE question_id = '$questionId' ORDER BY id";
//$result = api_sql_query($sql,__FILE__,__LINE__);

// Init
$output = "hotspot_lang=$courseLang&hotspot_image=$pictureName&hotspot_image_width=$pictureWidth&hotspot_image_height=$pictureHeight&courseCode=$coursePath";
$i = 0;
$nmbrTries = 0;


$answers=$_SESSION['tmp_answers'];
$nbrAnswers = count($answers['answer']);

for($i=1;$i <= $nbrAnswers;$i++)
{
   	$output .= "&hotspot_".$i."=true";
	$output .= "&hotspot_".$i."_answer=".$answers['answer'][$i];

	// Square or rectancle
	if ($answers['hotspot_type'][$i] == 'square' )
	{
		$output .= "&hotspot_".$i."_type=square";
	}

	// Circle or ovale
	if ($answers['hotspot_type'][$i] == 'circle')
	{
		$output .= "&hotspot_".$i."_type=circle";
	}

	// Polygon
	if ($answers['hotspot_type'][$i] == 'poly')
	{
		$output .= "&hotspot_".$i."_type=poly";
	}
	// Delineation
	if ($answers['hotspot_type'][$i] == 'delineation')
	{
		$output .= "&hotspot_".$i."_type=delineation";
	}

	// This is a good answer, count + 1 for nmbr of clicks
	if ($answers['weighting'][$i] > 0)
	{
		$nmbrTries++;
	}

	$output .= "&hotspot_".$i."_coord=".$answers['hotspot_coordinates'][$i]."";
}

// Generate empty
$i++;
for ($i; $i <= 12; $i++)
{
	$output .= "&hotspot_".$i."=false";
}

// Output
echo $output."&nmbrTries=".$nmbrTries."&done=done";
?>