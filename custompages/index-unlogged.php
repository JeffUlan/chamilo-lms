<?php 
//require_once('main/inc/global.inc.php'); 
require_once('language.php');
if (isset($_GET['loginFailed'])){
  if (isset($_GET['error'])) {
    switch ($_GET['error']) {
    case 'account_expired':
      $error_message = cblue_get_lang('AccountExpired');
      break;
    case 'account_inactive':
      $error_message = cblue_get_lang('AccountInactive');
      break;
    case 'user_password_incorrect':
      $error_message = cblue_get_lang('InvalidId');
      break;
    case 'access_url_inactive':
      $error_message = cblue_get_lang('AccountURLInactive');
      break;
    default : 
      $error_message = cblue_get_lang('InvalidId');
    }
  } else { 
    $error_message = get_lang('InvalidId');
  }
}
?>
<html>
<head>
	<title>Custompage - login</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<!--[if !IE 6]><!-->
	<link rel="stylesheet" type="text/css" href="/custompages/style.css" />
	<!--<![endif]-->
	<!--[if IE 6]>
	<link rel="stylesheet" type="text/css" href="/custompages/style-ie6.css" />
	<![endif]-->

	<script type="text/javascript" src="/custompages/jquery-1.5.1.min.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			if (top.location != location) 
				top.location.href = document.location.href ;

			// Handler pour la touche retour
			$('input').keyup(function(e) { 
				if (e.keyCode == 13) {
					$('#login-form').submit();
				}
			});
		});
	</script>
</head>
<body>
	<div id="backgroundimage">
		<img src="/custompages/images/page-background.png" class="backgroundimage" />
	</div>
	<div id="wrapper">
		<div id="header">
			<img src="/custompages/images/header.png" alt="Logo" />
		</div> <!-- #header -->
		<div id="login-form-box" class="form-box">
		<?php if ($values['reset_password']) {
			echo '<div id="login-form-info" class="form-info">'.cblue_get_lang('your_password_has_been_reset').'</div>';
		}?> 
		<?php if (isset($error_message)) {
			echo '<div id="login-form-info" class="form-error">'.$error_message.'</div>';
		}?> 
			<form id="login-form" class="form" action="/index.php" method="post">
				<div>
        <label for="login">*<?php echo cblue_get_lang('Username');?></label>
					<input name="login" type="text" /><br />
          <label for="password">*<?php echo cblue_get_lang('langPass');?></label>
					<input name="password" type="password" /><br />
				</div>
			</form>
			<div id="login-form-submit" class="form-submit" onclick="document.forms['login-form'].submit();">
      <span><?php echo cblue_get_lang('LoginEnter');?></span>
			</div> <!-- #form-submit -->
			<div id="links">
      <a href="main/auth/lostPassword.php"><?php echo cblue_get_lang('langLostPassword')?></a>
			</div>
		</div> <!-- #form -->
		<div id="footer">
			<img src="/custompages/images/footer.png" />
		</div> <!-- #footer -->
	</div> <!-- #wrapper -->
</body>
</html>
