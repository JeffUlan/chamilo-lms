<?php
// Conditional login
// Used to implement the loading of custom pages
// 2011, Noel Dieschburg <noel@cblue.be>

class ConditionalLogin {

    public static function check_conditions($user) {
        if (file_exists(api_get_path(SYS_PATH).'main/auth/conditional_login/conditional_login.php')) {
            include_once api_get_path(SYS_PATH).'main/auth/conditional_login/conditional_login.php';
            if (isset($dc_conditions)){
                foreach ($dc_conditions as $dc_condition) { 
                    if (isset($dc_condition['conditional_function']) && $dc_condition['conditional_function']($user)) {
                        $_SESSION['conditional_login']['uid'] = $user['user_id'];
                        $_SESSION['conditional_login']['can_login'] = false;
                        header("Location:". $dc_condition['url']);
                        exit();
                    }
                }
            }
        }
    }

	public static function login() {
		require_once api_get_path(LIBRARY_PATH).'loginredirection.lib.php';
	    $_SESSION['conditional_login']['can_login'] = true;
		LoginRedirection::redirect();
	    exit();
	}
}