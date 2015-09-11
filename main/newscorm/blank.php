<?php
/* For licensing terms, see /license.txt */
/**
 * Script that displays a blank page (with later a message saying why)
 * @package chamilo.learnpath
 * @author Yannick Warnier <ywarnier@beeznest.org>
 */
// Flag to allow for anonymous user - needs to be set before global.inc.php.
$use_anonymous = true;
require_once '../inc/global.inc.php';
Display::display_reduced_header();
$htmlHeadXtra[] = "
<style>
body { background: none;}
</style>
";

if (isset($_GET['error'])) {
    switch ($_GET['error']){
        case 'document_deleted':
            echo '<br /><br />';
            Display::display_error_message(get_lang('DocumentHasBeenDeleted'));
            break;
        case 'prerequisites':
            echo '<br /><br />';
            Display::display_warning_message(get_lang('LearnpathPrereqNotCompleted'));
            break;
        case 'document_not_found':
            echo '<br /><br />';
            Display::display_warning_message(get_lang('FileNotFound'));
            break;
        case 'reached_one_attempt':
            echo '<br /><br />';
            Display::display_warning_message(get_lang('ReachedOneAttempt'));
            break;
        case 'x_frames_options':
            if (isset($_SESSION['x_frame_source'])) {
                $src = $_SESSION['x_frame_source'];
                echo Display::return_message(
                    Display::url($src, $src, ['target' => '_blank']),
                    'normal',
                    false
                );
                unset($_SESSION['x_frame_source']);
            }
            break;
        default:
            break;
    }
} elseif (isset($_GET['msg']) && $_GET['msg'] == 'exerciseFinished') {
    echo '<br /><br />';
    Display::display_normal_message(get_lang('ExerciseFinished'));
}
?>
</body>
</html>
