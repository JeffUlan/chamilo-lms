<?php // $Id: reservation.php,v 1.34 2006/05/10 12:30:38 sjacobs Exp $
/*
==============================================================================
    Dokeos - elearning and course management software

    Copyright (c) 2004-2008 Dokeos S.A.
    Copyright (c) Sebastien Jacobs (www.spiritual-coder.com)
    Copyright (c) Kristof Van Steenkiste 

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
    ---------------------------------------------------------------------
                Reservation-manager (add, edit & delete)
    ---------------------------------------------------------------------
 */
require_once ("./rsys.php");

Rsys :: protect_script('reservation');
$tool_name = get_lang('Booking');

///$interbreadcrumb[] = array ("url" => 'reservation.php', "name" => get_lang('Booking'));


Display :: display_header($tool_name);
api_display_tool_title($tool_name);


echo '<div class="actions">';
///		echo '<a href="m_reservation.php?action=add"><img src="../img/view_more_stats.gif" border="0" alt="" title="'.get_lang('AddNewReservationPeriod').'"/>'.get_lang('AddNewReservationPeriod').'</a>';
//echo '&nbsp;&nbsp;&nbsp;<a href="m_reservation.php?action=overviewsubscriptions">'.get_lang('OverviewReservedPeriods').'</a>';
echo '<div style="float: right;"><a href="mysubscriptions.php">'.Display::return_icon('file_txt.gif',get_lang('BookingListView'),array('width'=>'22')).'&nbsp;'.get_lang('GoToListView').'</a></div>';
echo '<a href="m_item.php?view=calendar">'.Display::return_icon('cube.png',get_lang('Resources')).'&nbsp;'.get_lang('Resources').'</a>';		
echo '&nbsp;&nbsp;<a href="m_reservation.php?view=calendar">'.Display::return_icon('calendar_day.gif',get_lang('BookingPeriods')).'&nbsp;'.get_lang('BookingPeriods').'</a>';
echo '&nbsp;&nbsp;<a href="m_reservation.php?action=add&view=calendar">'.Display::return_icon('calendar_add.gif',get_lang('BookIt')).'&nbsp;'.get_lang('BookIt').'</a>';
		
if (api_is_platform_admin()) 
{
	//echo '&nbsp;&nbsp;<a href="m_category.php">'.Display::return_icon('settings.gif',get_lang('Configuration')).'&nbsp;'.get_lang('Configuration').'</a>';
}
			
echo '</div><br />';

		
		
$gogogo=false;
// Get resolution of user
if((empty($_SESSION['swidth'])||empty($_SESSION['sheight']))&&(empty($_GET['swidth'])||empty($_GET['sheight']))){
?>
<script type="text/javascript">
window.location.href='reservation.php?sheight='+screen. height+'&swidth='+screen.width;
</script>
<?php   
}elseif((empty($_SESSION['swidth']))){
    $_SESSION['swidth']=$_GET['swidth'];
    $_SESSION['sheight']=$_GET['sheight'];
    $gogogo=true;   
}else $gogogo=true;

echo '<div style="float: left;"><form id="cat_form" action="reservation.php" method="get"><input type="hidden" name="cat" value="'.$_GET['cat'].'" /><div style="float: left;">'.get_lang('Category').': <select name="cat" onchange="this.form.submit();"><option value="0">'.get_lang('NONE').'</option>';
$cats = Rsys :: get_category_with_items();

if(count($cats)>0){
    foreach ($cats as $cat)
	   echo '<option value="'.$cat['id'].'"'. ($cat['id'] == $_GET['cat'] ? ' selected="selected"' : '').'>'.$cat['name'].'</option>';
}

echo '</select></div></form></div>';


if ($gogogo&&!empty($_GET['cat'])) {
	$itemlist = Rsys :: get_cat_items($_GET['cat']);
    echo '<div style="float: left;">';
	if (count($itemlist) != 0) {
	echo '<form id="item_form" action="reservation.php?cat='.$_GET['cat'].'&amp;item=" method="get">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="hidden" name="cat" value="'.$_GET['cat'].'" />'.get_lang('Item').': <select name="item" onchange="this.form.submit();"><option value="0">'.get_lang('NONE').'</option>';
		foreach ($itemlist as $id => $item)
			echo '<option value="'.$id.'"'. ($id == $_GET['item'] ? ' selected="selected"' : '').'>'.$item.'</option>';
		echo '</select></form>';
	}else{
		echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.get_lang('NoItemsReservation');	
	}
    echo '</div>';
	if(!empty($_GET['item'])){
		$calendar = new rCalendar();
		$time=Rsys::mysql_datetime_to_array($_GET['date'].' 00:00:00');
		ob_start();
        echo '<div style="float: left; margin-right: 10px">';
		if(isset($_GET['changemonth'])){
			echo $calendar->get_mini_month(intval($time['month']),intval($time['year']),"&amp;cat=".$_GET['cat']."&amp;item=".$_GET['item']."&amp;changemonth=yes",$_GET['item']);
		}
		else
			echo $calendar->get_mini_month(date('m'),date('Y'),"&amp;cat=".$_GET['cat']."&amp;item=".$_GET['item'],$_GET['item']);
        echo '</div><div style="float: left" >';
        
        switch($_SESSION['swidth']){
            case '640': $week_scale= 170;break;
            case '1024': $week_scale=130;break;
            case '1152': $week_scale=110;break;
            case '1280': $week_scale=94;break;
            case '1600': $week_scale=70;break;
            case '1792': $week_scale=60;break;
            case '1800': $week_scale=50;break;
            case '1920': $week_scale=40;break;
            case '2048': $week_scale=30;break;
            default: $week_scale= 150; // 800x600    
        }
		if(isset($_GET['date'])){
			echo $calendar->get_week_view(intval($time['day']),intval($time['month']), $time['year'],$_GET['item'], $week_scale);
		}else
			echo $calendar->get_week_view(intval(date('d')), intval(date('m')), intval(date('Y')), $_GET['item'], $week_scale);
		echo '</div>';
       $buffer=ob_get_contents();
       ob_end_clean();
       function getBlock($color){
            return '<img src="../img/px_'.$color.'.gif" alt="" style="border:1px solid #000;height: 10px;width: 10px;vertical-align:top;margin-left:10px" />';
       }
       $legend=getBlock('green').' '.ucfirst(get_lang('Reservation')).' '.getBlock('blue').' '.get_lang('TimePicker').' '.getBlock('orange').' '.get_lang('OutPeriod').' '.getBlock('red').' '.get_lang('Reserved').' '.getBlock('grey').' '.get_lang('NoReservations').' '.getBlock('black').' '.get_lang('Blackout');
       echo '<br /><br /><div style="text-align:right; border-bottom: 2px dotted #666; margin: 0 0 0.2em 0; padding: 0.2em;clear:both;font-family: Verdana,sans-serif;font-size: 1.2em;color:#666;font-weight:bold">'.$GLOBALS['weekstart'].' - '.$GLOBALS['weekend'].'</div>'.$buffer.'<div style="clear:both;">&nbsp;</div><div style="background-color:#EEE;padding: 0.5em;font-family:Verdana;sans-serif;font-size:10px;text-align:center">'.$legend.'</div>';
	}
}
Display :: display_footer();
?>
