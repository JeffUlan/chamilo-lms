<?php
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

	Contact address: Dokeos, 44 rue des palais, B-1030 Brussels, Belgium
	Mail: info@dokeos.com
==============================================================================
*/
/**
==============================================================================
*	@author Thomas Depraetere
*	@author Hugues Peeters
*	@author Christophe Gesche
*	@author Sebastien Piraux
*
*	@package dokeos.tracking
==============================================================================
*/

/*
==============================================================================
		INIT SECTION
==============================================================================
*/
$reqdate = $_REQUEST['reqdate'];
$period = $_REQUEST['period'];
$displayType = $_REQUEST['displayType'];
$langFile = "tracking";
include('../inc/global.inc.php');

$interbreadcrumb[]= array ("url"=>"courseLog.php", "name"=> get_lang('ToolName'));

$nameTools = get_lang('TrafficDetails');

$htmlHeadXtra[] = "<style type='text/css'>
/*<![CDATA[*/
.secLine {background-color : #E6E6E6;}
.content {padding-left : 15px;padding-right : 15px; }
.specialLink{color : #0000FF;}
/*]]>*/
</style>
<style media='print' type='text/css'>
/*<![CDATA[*/
td {border-bottom: thin dashed gray;}
/*]]>*/
</style>";
//@todo use Database library
$TABLETRACK_ACCESS = $_configuration['statistics_database']."`.`track_e_access";
Display::display_header($nameTools,"Tracking");
include(api_get_path(LIBRARY_PATH)."statsUtils.lib.inc.php");

// the variables for the days and the months
// Defining the shorts for the days
$DaysShort = array (get_lang("SundayShort"), get_lang("MondayShort"), get_lang("TuesdayShort"), get_lang("WednesdayShort"), get_lang("ThursdayShort"), get_lang("FridayShort"), get_lang("SaturdayShort"));
// Defining the days of the week to allow translation of the days
$DaysLong = array (get_lang("SundayLong"), get_lang("MondayLong"), get_lang("TuesdayLong"), get_lang("WednesdayLong"), get_lang("ThursdayLong"), get_lang("FridayLong"), get_lang("SaturdayLong"));
// Defining the months of the year to allow translation of the months
$MonthsLong = array (get_lang("JanuaryLong"), get_lang("FebruaryLong"), get_lang("MarchLong"), get_lang("AprilLong"), get_lang("MayLong"), get_lang("JuneLong"), get_lang("JulyLong"), get_lang("AugustLong"), get_lang("SeptemberLong"), get_lang("OctoberLong"), get_lang("NovemberLong"), get_lang("DecemberLong"));

$is_allowedToTrack = $is_courseAdmin;

?>
<h3>
    <?php echo $nameTools ?>
</h3>
<table width="100%" cellpadding="2" cellspacing="3" border="0">
<?php
    if( $is_allowedToTrack && $_configuration['tracking_enabled'])
    {
        if( !isset($reqdate) || $reqdate < 0 || $reqdate > 2149372861 )
                $reqdate = time();
        //** dislayed period
        echo "<tr><td><b>";
            switch($period)
            {
                case "year" :
                    echo date(" Y", $reqdate);
                    break;
                case "month" :
                    echo $MonthsLong[date("n", $reqdate)-1].date(" Y", $reqdate);
                    break;
                // default == day
                default :
                    $period = "day";
                case "day" :
                    echo $DaysLong[date("w" , $reqdate)].date(" d " , $reqdate).$MonthsLong[date("n", $reqdate)-1].date(" Y" , $reqdate);
                    break;
            }
        echo "</b></tr></td>";
        //** menu
        echo "<tr>
                <td>
        ";
        echo "  ".get_lang('PeriodToDisplay')." : [<a href='".$_SERVER['PHP_SELF']."?period=year&reqdate=$reqdate' class='specialLink'>".get_lang('PeriodYear')."</a>]
                [<a href='".$_SERVER['PHP_SELF']."?period=month&reqdate=$reqdate' class='specialLink'>".get_lang('PeriodMonth')."</a>]
                [<a href='".$_SERVER['PHP_SELF']."?period=day&reqdate=$reqdate' class='specialLink'>".get_lang('PeriodDay')."</a>]
                &nbsp;&nbsp;&nbsp;||&nbsp;&nbsp;&nbsp;
                ".get_lang('DetailView')." :
        ";
        switch($period)
        {
            case "year" :
                    //-- if period is "year" display can be by month, day or hour
                    echo "  [<a href='".$_SERVER['PHP_SELF']."?period=$period&reqdate=$reqdate&displayType=month' class='specialLink'>".get_lang('PeriodMonth')."</a>]";
            case "month" :
                    //-- if period is "month" display can be by day or hour
                    echo "  [<a href='".$_SERVER['PHP_SELF']."?period=$period&reqdate=$reqdate&displayType=day' class='specialLink'>".get_lang('PeriodDay')."</a>]";
            case "day" :
                    //-- if period is "day" display can only be by hour
                    echo "  [<a href='".$_SERVER['PHP_SELF']."?period=$period&reqdate=$reqdate&displayType=hour' class='specialLink'>".get_lang('PeriodHour')."</a>]";
                    break;
        }

        echo "&nbsp;&nbsp;&nbsp;||&nbsp;&nbsp;&nbsp;";

        switch($period)
        {
            case "year" :
                // previous and next date must be evaluated
                // 30 days should be a good approximation
                $previousReqDate = mktime(1,1,1,1,1,date("Y",$reqdate)-1);
                $nextReqDate = mktime(1,1,1,1,1,date("Y",$reqdate)+1);
                echo   "
                    [<a href='".$_SERVER['PHP_SELF']."?period=$period&reqdate=$previousReqDate&displayType=$displayType' class='specialLink'>".get_lang('PreviousYear')."</a>]
                    [<a href='".$_SERVER['PHP_SELF']."?period=$period&reqdate=$nextReqDate&displayType=$displayType' class='specialLink'>".get_lang('NextYear')."</a>]
                ";
                break;
            case "month" :
                // previous and next date must be evaluated
                // 30 days should be a good approximation
                $previousReqDate = mktime(1,1,1,date("m",$reqdate)-1,1,date("Y",$reqdate));
                $nextReqDate = mktime(1,1,1,date("m",$reqdate)+1,1,date("Y",$reqdate));
                echo   "
                    [<a href='".$_SERVER['PHP_SELF']."?period=$period&reqdate=$previousReqDate&displayType=$displayType' class='specialLink'>".get_lang('PreviousMonth')."</a>]
                    [<a href='".$_SERVER['PHP_SELF']."?period=$period&reqdate=$nextReqDate&displayType=$displayType' class='specialLink'>".get_lang('NextMonth')."</a>]
                ";
                break;
            case "day" :
                // previous and next date must be evaluated
                $previousReqDate = $reqdate - 86400;
                $nextReqDate = $reqdate + 86400;
                echo   "
                    [<a href='".$_SERVER['PHP_SELF']."?period=$period&reqdate=$previousReqDate&displayType=$displayType' class='specialLink'>".get_lang('PreviousDay')."</a>]
                    [<a href='".$_SERVER['PHP_SELF']."?period=$period&reqdate=$nextReqDate&displayType=$displayType' class='specialLink'>".get_lang('NextDay')."</a>]
                ";
                break;
        }
        echo "
                </td>
              </tr>
        ";
        //**
        // display information about this period
        switch($period)
        {
            // all days
            case "year" :
                $sql = "SELECT UNIX_TIMESTAMP( `access_date` )
                            FROM `$TABLETRACK_ACCESS`
                            WHERE YEAR( `access_date` ) = YEAR( FROM_UNIXTIME( '$reqdate' ) )
                            AND `access_cours_code` = '$_cid'
                            AND `access_tool` IS NULL ";
                if($displayType == "month")
                {
                    $sql .= "ORDER BY UNIX_TIMESTAMP( `access_date`)";
                    $month_array = monthTab($sql);
                    makeHitsTable($month_array,get_lang('PeriodMonth'));
                }
                elseif($displayType == "day")
                {
                    $sql .= "ORDER BY DAYOFYEAR( `access_date`)";
                    $days_array = daysTab($sql);
                    makeHitsTable($days_array,get_lang('PeriodDay'));
                }
                else // by hours by default
                {
                    $sql .= "ORDER BY HOUR( `access_date`)";
                    $hours_array = hoursTab($sql);
                    makeHitsTable($hours_array,get_lang('PeriodHour'));
                }
                break;
            // all days
            case "month" :
                $sql = "SELECT UNIX_TIMESTAMP( `access_date` )
                            FROM `$TABLETRACK_ACCESS`
                            WHERE MONTH(`access_date`) = MONTH (FROM_UNIXTIME( '$reqdate' ) )
                            AND YEAR( `access_date` ) = YEAR( FROM_UNIXTIME( '$reqdate' ) )
                            AND `access_cours_code` = '$_cid'
                            AND `access_tool` IS NULL ";
                if($displayType == "day")
                {
                    $sql .= "ORDER BY DAYOFYEAR( `access_date`)";
                    $days_array = daysTab($sql);
                    makeHitsTable($days_array,get_lang('PeriodDay'));
                }
                else // by hours by default
                {
                    $sql .= "ORDER BY HOUR( `access_date`)";
                    $hours_array = hoursTab($sql);
                    makeHitsTable($hours_array,get_lang('PeriodHour'));
                }
                break;
            // all hours
            case "day"  :
                $sql = "SELECT UNIX_TIMESTAMP( `access_date` )
                            FROM `$TABLETRACK_ACCESS`
                            WHERE DAYOFMONTH(`access_date`) = DAYOFMONTH(FROM_UNIXTIME( '$reqdate' ) )
                            AND MONTH(`access_date`) = MONTH (FROM_UNIXTIME( '$reqdate' ) )
                            AND YEAR( `access_date` ) = YEAR( FROM_UNIXTIME( '$reqdate' ) )
                            AND `access_cours_code` = '$_cid'
                            AND `access_tool` IS NULL
                            ORDER BY HOUR( `access_date` )";
                $hours_array = hoursTab($sql,$reqdate);
                makeHitsTable($hours_array,get_lang('PeriodHour'));
                break;
        }
    }
    else // not allowed to track
    {
        if(!$_configuration['tracking_enabled'])
        {
            echo get_lang('TrackingDisabled');
        }
        else
        {
            api_not_allowed();
        }
    }



?>

</table>

<?php
Display::display_footer();
?>
