
<?php
$langFile = 'survey';

$temp = $_REQUEST['temp'];
require ('../inc/global.inc.php');
require_once (api_get_path(LIBRARY_PATH)."/surveymanager.lib.php");
$status = surveymanager::get_status();
if($status==5)
{
api_protect_admin_script();
}
$cidReq = $_REQUEST['cidReq'];
require_once (api_get_path(LIBRARY_PATH)."/course.lib.php");
$ques = $_REQUEST['ques'];
$ans = $_REQUEST['ans'];
$answers = explode("|",$ans);
$count = count($answers);
$qtype = $_REQUEST['qtype'];

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>White Template</title>
<link href="../survey/css_white/style.css" rel="stylesheet" type="text/css">
<style type="text/css">
<!--
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
.style5 {font-size: 12px; font-weight: bold; font-family: Arial, Helvetica, sans-serif;}
-->
</style>
</head>

<body>

<table width="100%" cellpadding="0" cellspacing="0">
<tr>
    <td height="85" colspan="2" align=left><img src="../cssimg/logo_ifa.gif" border=0></td>
</tr>
  <tr>
    <td width="66%" bgcolor="#999999">&nbsp;&nbsp;<span class="text">My Portal &gt; My Organisation</span></td>
    <td width="34%" align="right" bgcolor="#949A9C"><span class="text"></span>&nbsp;</td>
  </tr>
  <tr>
    <td width="66%" bgcolor="#F7EBEF" colspan="2">&nbsp;&nbsp;<span class="text"></span></td>
    
  </tr>
  <tr>
    <td width="66%" bgcolor="949A9C">&nbsp;&nbsp;<span class="text">My Portal &gt; Course Home &gt; Survey</span></td>
    <td width="34%" align="right" bgcolor="#949A9C"><span class="text"></span>&nbsp;</td>
  </tr>

</table>
<table width="100%" cellpadding="0" cellspacing="0">
  <tr bgcolor="#FFFFFF">
    <td colspan="2" align="center" valign="top"><br>
      <br>
      <table width="600" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="23" height="21"><img src="../survey/images_white/top-1.gif" width="23" height="21"></td>
          <td height="21" background="../survey/images_white/top-2.gif">&nbsp;</td>
          <td width="20" height="21"><img src="../survey/images_white/top-3.gif" width="20" height="21"></td>
        </tr>
        <tr>
		<?
		switch ($qtype)
		{
		case "Yes/No":
		{?>
		<td height="39" background="../survey/images_white/left.gif">&nbsp;</td>
          <td><strong>Question: </strong><br><? echo $ques;?><br><br><strong>Answers: </strong><br>
		 <textarea cols="50" rows="3" disabled='true'><?echo $answers[0];?></textarea>
		 <input name="radiobutton" type="radio" value="radiobutton"><br><textarea cols="50" rows="3" disabled='true'><?echo $answers[1];?></textarea>
		 <input name="radiobutton" type="radio" value="radiobutton"></td>
          <td background="../survey/images_white/right.gif">&nbsp;</td>
		<?
		break;
		}
		case "Multiple Choice (multiple answer)":
		{?>
		<td height="39" background="../survey/images_white/left.gif">&nbsp;</td>
        <td><strong>Question: </strong><br><? echo $ques;?><br><br><strong>Answers: </strong><br>
		<?
		$i=0;
		for($p=1;$p<$count;$i++,$p++)
		{
		?>
		<textarea cols="50" rows="3" disabled='true'><? echo $answers[$i]; ?></textarea>
		<input type="checkbox" name="checkbox" value="checkbox"><br>
		<?
		}
		?>
		</td>
        <td background="../survey/images_white/right.gif">&nbsp;</td>
		<?
		break;
		}
		case "Multiple Choice (single answer)":
		{?>
		<td height="39" background="../survey/images_white/left.gif">&nbsp;</td>
        <td><strong>Question: </strong><br><? echo $ques;?><br><br><strong>Answers: </strong><br>
		<?
		$i=0;
		for($p=1;$p<$count;$i++,$p++)
		{
		?>
		<textarea cols="50" rows="3" disabled='true'><? echo $answers[$i]; ?></textarea>
		<input name="radiobutton" type="radio" value="radiobutton"><br>
		<?
		}
		?>
		</td>
        <td background="../survey/images_white/right.gif">&nbsp;</td>
		<?
		break;
		}
		case "Open":
		{?>
		<td height="39" background="../survey/images_white/left.gif">&nbsp;</td>
        <td><strong>Question: </strong><br><? echo $ques;?><br><br><strong>Answer: </strong><br>
		<TEXTAREA  style="WIDTH: 100%" name="defaultext" rows=3 cols=60>
        </TEXTAREA> 	
		</td>
        <td background="../survey/images_white/right.gif">&nbsp;</td>
		<?
		break;
		}
		case "Numbered":
		{?>
		<td height="39" background="../survey/images_white/left.gif">&nbsp;</td>
        <td><strong>Question: </strong><br><? echo $ques;?><br><br><strong>Answers: </strong><br>
		<?
		$i=0;
		for($p=1;$p<$count;$i++,$p++)
		{
		?>
		<textarea cols="50" rows="3" disabled='true'><? echo $answers[$i]; ?></textarea>
		<select>
		<option value="not applicable">Not Applicable</option>
		<option value="$i">1</option>
		<option value="$i">2</option>
		<option value="$i">3</option>
		<option value="$i">4</option>
		<option value="$i">5</option>
		<option value="$i">6</option>
		<option value="$i">7</option>
		<option value="$i">8</option>
		<option value="$i">9</option>
		<option value="$i">10</option>
		</select><br>
		<?
		}
		?>
		</td>
        <td background="../survey/images_white/right.gif">&nbsp;</td>
		<?
		break;		
		}
		}
		
		?>
          
        </tr>
        <tr>
          <td background="../survey/images_white/left.gif">&nbsp;</td>
          <td><p>&nbsp;</p>
              </td>
          <td background="../survey/images_white/right.gif">&nbsp;</td>
        </tr>
        <tr>
          <td><img src="../survey/images_white/bottom-1.gif" width="23" height="21"></td>
          <td background="../survey/images_white/bottom-2.gif">&nbsp;</td>
          <td><img src="../survey/images_white/bottom-3.gif" width="20" height="21"></td>
        </tr>
      </table>
      <p><br>
        <br>
      </p>
    <p>&nbsp;</p>
    <p>&nbsp;           </p></td>
  </tr>
  <tr align="right" bgcolor="#942039">
    <td height="30" align="left" bgcolor="#999999" class="bg-4">&nbsp;&nbsp;<span class="text">Manager : </span><span class="text-2">user admin</span> </td>
    <td height="30" bgcolor="#999999" class="bg-4"><span class="text">Platform</span> <span class="text-2">Dokeos 1.6.3</span> <span class="text">&copy; 2006&nbsp;&nbsp;</span></td>
  </tr>
</table>
</body>
</html>
