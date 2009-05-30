<?php //$Id: calendar.php 21101 2009-05-30 14:56:54Z iflorespaz $
/*
==============================================================================
	Dokeos - elearning and course management software

	Copyright (c) 2004-2008 Dokeos SPRL
	Copyright (c) 2003-2005 Ghent University (UGent)
	Copyright (c) various contributors

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

// name of the language file that needs to be included
$language_file = 'agenda';
// including the claroline global 
require_once '../inc/global.inc.php';

//session
if(isset($_GET['id_session']))
	$_SESSION['id_session'] = Security::remove_XSS($_GET['id_session']);

// the variables for the days and the months
// Defining the shorts for the days
$DaysShort = array(get_lang("SundayShort"), get_lang("MondayShort"), get_lang("TuesdayShort"), get_lang("WednesdayShort"), get_lang("ThursdayShort"), get_lang("FridayShort"), get_lang("SaturdayShort")); 
// Defining the days of the week to allow translation of the days
$DaysLong = array(get_lang("SundayLong"), get_lang("MondayLong"), get_lang("TuesdayLong"), get_lang("WednesdayLong"), get_lang("ThursdayLong"), get_lang("FridayLong"), get_lang("SaturdayLong")); 
// Defining the months of the year to allow translation of the months
$MonthsLong = array(get_lang("JanuaryLong"), get_lang("FebruaryLong"), get_lang("MarchLong"), get_lang("AprilLong"), get_lang("MayLong"), get_lang("JuneLong"), get_lang("JulyLong"), get_lang("AugustLong"), get_lang("SeptemberLong"), get_lang("OctoberLong"), get_lang("NovemberLong"), get_lang("DecemberLong")); 

?>
<html>
<head>
<title>Calendar</title>
<style type="text/css">
@import "<?php echo api_get_path(WEB_CODE_PATH); ?>css/<?php echo api_get_setting('stylesheets'); ?>/default.css";
.data_table th
{
	font-size: 10px;
}
.data_table td
{
	font-size: 10px;
	width: 25px;
	height: 25px;
}
table.calendar td
{

	background-color: #f5f5f5;	
	text-align: center;
}
.data_table td.selected
{
	border: 1px solid #ff0000; 
	background-color: #FFCECE;
}
.data_table td a
{
	width: 25px;
	height: 25px;
	text-decoration: none;
}
.data_table td a:hover
{
	background-color: #ffff00;
}
</style>
<script language="JavaScript" type="text/javascript">
<!--
    /* added 2004-06-10 by Michael Keck
     *       we need this for Backwards-Compatibility and resolving problems
     *       with non DOM browsers, which may have problems with css 2 (like NC 4)
    */
    var isDOM      = (typeof(document.getElementsByTagName) != 'undefined'
                      && typeof(document.createElement) != 'undefined')
                   ? 1 : 0;
    var isIE4      = (typeof(document.all) != 'undefined'
                      && parseInt(navigator.appVersion) >= 4)
                   ? 1 : 0;
    var isNS4      = (typeof(document.layers) != 'undefined')
                   ? 1 : 0;
    var capable    = (isDOM || isIE4 || isNS4)
                   ? 1 : 0;
    // Uggly fix for Opera and Konqueror 2.2 that are half DOM compliant
    if (capable) {
        if (typeof(window.opera) != 'undefined') {
            var browserName = ' ' + navigator.userAgent.toLowerCase();
            if ((browserName.indexOf('konqueror 7') == 0)) {
                capable = 0;
            }
        } else if (typeof(navigator.userAgent) != 'undefined') {
            var browserName = ' ' + navigator.userAgent.toLowerCase();
            if ((browserName.indexOf('konqueror') > 0) && (browserName.indexOf('konqueror/3') == 0)) {
                capable = 0;
            }
        } // end if... else if...
    } // end if
//-->
</script>
<script type="text/javascript" src="tbl_change.js"></script>
<script type="text/javascript">
<!--
var month_names = new Array(
<?php
foreach($MonthsLong as $index => $month)
{
	echo '"'.$month.'",';
}
?>
"");
var day_names = new Array(
<?php
foreach($DaysShort as $index => $day)
{
	echo '"'.$day.'",';
}
?>
"");
//-->
</script>
</head>
<body onLoad="initCalendar();">
<div id="calendar_data"></div>
<div id="clock_data"></div>
</body>
</html>
