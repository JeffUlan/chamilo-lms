<?php
/* For licensing terms, see /license.txt */

/**
 * View (MVC patter) for courses categories
 * @author Christian Fasanando <christian1827@gmail.com> - Beeznest
 * @package chamilo.auth
 */

if (isset($_REQUEST['action']) && Security::remove_XSS($_REQUEST['action']) !== 'subscribe') {
    $stok = Security::get_token();
} else {
    $stok = $_SESSION['sec_token'];
}

$showCourses = CoursesAndSessionsCatalog::showCourses();
$showSessions = CoursesAndSessionsCatalog::showSessions();
$pageCurrent = isset($pageCurrent) ? $pageCurrent :
    isset($_GET['pageCurrent']) ? intval($_GET['pageCurrent']) :
        1;
$pageLength = isset($pageLength) ? $pageLength :
    isset($_GET['pageLength']) ? intval($_GET['pageLength']) :
        10;
$pageTotal = intval(ceil(intval($countCoursesInCategory) / $pageLength));
$cataloguePagination = $pageTotal > 1 ?
    getCataloguePagination($pageCurrent, $pageLength, $pageTotal) :
    '';
$search_term = isset($search_term) ? $search_term :null;

if ($showSessions && isset($_POST['date'])) {
    $date = $_POST['date'];
} else {
    $date = date('Y-m-d');
}

$userInfo = api_get_user_info();
$code = isset($code) ? $code : null;

?>
<script>
    $(document).ready( function() {
        $('.star-rating li a').on('click', function(event) {
            var id = $(this).parents('ul').attr('id');
            $('#vote_label2_' + id).html("<?php echo get_lang('Loading'); ?>");
            $.ajax({
                url: $(this).attr('data-link'),
                success: function(data) {
                    $("#rating_wrapper_"+id).html(data);
                    if(data == 'added') {
                        //$('#vote_label2_' + id).html("{'Saved'|get_lang}");
                    }
                    if(data == 'updated') {
                        //$('#vote_label2_' + id).html("{'Saved'|get_lang}");
                    }
                }
            });
        });

        /*$('.courses-list-btn').toggle(function (e) {
            e.preventDefault();

            var $el = $(this);
            var sessionId = getSessionId(this);

            $el.children('img').remove();
            $el.prepend('<?php echo Display::display_icon('nolines_minus.gif'); ?>');

            $.ajax({
                url: '<?php echo api_get_path(WEB_AJAX_PATH) . 'course.ajax.php' ?>',
                type: 'GET',
                dataType: 'json',
                data: {
                    a: 'display_sessions_courses',
                    session: sessionId
                },
                success: function (response){
                    var $container = $el.prev('.course-list');
                    var $courseList = $('<ul>');
                    $.each(response, function (index, course) {
                        $courseList.append('<li><div><strong>' + course.name + '</strong><br>' + course.coachName + '</div></li>');
                    });

                    $container.append($courseList).show(250);
                }
            });
        }, function (e) {
            e.preventDefault();
            var $el = $(this);
            var $container = $el.prev('.course-list');
            $container.hide(250).empty();
            $el.children('img').remove();
            $el.prepend('<?php echo Display::display_icon('nolines_plus.gif'); ?>');
        });*/

        var getSessionId = function (el) {
            var parts = el.id.split('_');
            return parseInt(parts[1], 10);
        };

        <?php if ($showSessions) { ?>
        $('#date').datepicker({
            dateFormat: 'yy-mm-dd'
        });
        <?php } ?>
    });
</script>

<div class="row">
    <div class="col-md-3">
        <div class="panel panel-default">
            <div class="panel-heading"><?php echo get_lang('Search'); ?></div>
            <div class="panel-body">
                <?php if ($showCourses) { ?>
                <?php if (!isset($_GET['hidden_links']) || intval($_GET['hidden_links']) != 1) { ?>
                    <form class="form-search" method="post" action="<?php echo getCourseCategoryUrl(1, $pageLength, 'ALL', 0, 'subscribe'); ?>">
                        <fieldset>
                            <input type="hidden" name="sec_token" value="<?php echo $stok; ?>">
                            <input type="hidden" name="search_course" value="1" />
                            <div class="control-group">
                                <div class="controls">
                                    <div class="input-append">
                                        <input type="text" name="search_term" value="<?php echo (empty($_POST['search_term']) ? '' : api_htmlentities(Security::remove_XSS($_POST['search_term']))); ?>" />
                                        <div class="btn-group">
                                        <button class="btn btn-default btn-sm" type="submit">
                                            <i class="fa fa-search"></i> <?php echo get_lang('Search'); ?>
                                        </button>
                                        <?php
                                        $hidden_links = 0;
                                        } else {
                                            $hidden_links = 1;
                                        }

                                        /* Categories will only show down to 4 levels, if you want more,
                                         * you will have to patch the following code. We don't recommend
                                         * it, as this can considerably slow down your system
                                         */
                                        if (!empty($browse_course_categories)) {
                                        echo '<a class="btn btn-default btn-sm" href="'.api_get_self().'?action=display_random_courses">'.get_lang('RandomPick').'</a>';
                                        ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </form>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <?php
                echo get_lang('CourseCategories').'</div>';

                $action = 'display_courses';
                // level 1
                foreach ($browse_course_categories[0] as $category) {
                    $category_name = $category['name'];
                    $category_code = $category['code'];
                    $count_courses_lv1 = $category['count_courses'];

                    if ($code == $category_code) {
                        $category_link = '<strong>'.$category_name.' ('.$count_courses_lv1.')</strong>';
                    } else {
                        if (!empty($count_courses_lv1)) {
                            $category_link = '<a href="' .
                                getCourseCategoryUrl(
                                    1,
                                    $pageLength,
                                    $category_code,
                                    $hidden_links,
                                    $action
                                ) .'">'.$category_name.' ('.$count_courses_lv1.') </a>';
                        } else {
                            $category_link = ''.$category_name.' ('.$count_courses_lv1.')';
                        }
                    }
                    echo '<div class="panel-body">';
                    echo '<ul class="nav nav-pills nav-stacked">';
                    echo '<li>'.$category_link.'</li>';
                    echo '</ul></div>';
                    // level 2
                    if (!empty($browse_course_categories[$category_code])) {
                        foreach ($browse_course_categories[$category_code] as $subcategory1) {
                            $subcategory1_name = $subcategory1['name'];
                            $subcategory1_code = $subcategory1['code'];
                            $count_courses_lv2 = $subcategory1['count_courses'];
                            if ($code == $subcategory1_code) {
                                $subcategory1_link = '<strong>'.$subcategory1_name.' ('.$count_courses_lv2.')</strong>';
                            } else {
                                $subcategory1_link = '<a href="' .
                                    getCourseCategoryUrl(
                                        1,
                                        $pageLength,
                                        $subcategory1_code,
                                        $hidden_links,
                                        $action
                                    ) . '">'.$subcategory1_name.' ('.$count_courses_lv2.') </a> ';
                            }
                            echo '<li style="margin-left:20px;">'.$subcategory1_link.'</li>';

                            // level 3
                            if (!empty($browse_course_categories[$subcategory1_code])) {
                                foreach ($browse_course_categories[$subcategory1_code] as $subcategory2) {
                                    $subcategory2_name = $subcategory2['name'];
                                    $subcategory2_code = $subcategory2['code'];
                                    $count_courses_lv3 = $subcategory2['count_courses'];
                                    if ($code == $subcategory2_code) {
                                        $subcategory2_link = '<strong>'.$subcategory2_name.' ('.$count_courses_lv3.')</strong>';
                                    } else {
                                        $subcategory2_link = '<a href="' .
                                            getCourseCategoryUrl(
                                                1,
                                                $pageLength,
                                                $subcategory2_code,
                                                $hidden_links,
                                                $action
                                            ) . '">'.$subcategory2_name.'</a> ('.$count_courses_lv3.')';
                                    }
                                    echo '<li style="margin-left:40px;">'.$subcategory2_link.'</li>';

                                    // level 4
                                    if (!empty($browse_course_categories[$subcategory2_code])) {
                                        foreach ($browse_course_categories[$subcategory2_code] as $subcategory3) {
                                            $subcategory3_name = $subcategory3['name'];
                                            $subcategory3_code = $subcategory3['code'];
                                            $count_courses_lv4 = $subcategory3['count_courses'];
                                            if ($code == $subcategory3_code) {
                                                $subcategory3_link = '<strong>'.$subcategory3_name.' ('.$count_courses_lv4.')</strong>';
                                            } else {
                                                $subcategory3_link = '<a href="' .
                                                    getCourseCategoryUrl(
                                                        1,
                                                        $pageLength,
                                                        $subcategory3_code,
                                                        $hidden_links,
                                                        $action
                                                    ) . '">'.$subcategory3_name.' ('.$count_courses_lv4.') </a>';
                                            }
                                            echo '<li style="margin-left:60px;">'.$subcategory3_link.'</li>';
                                        }
                                    }
                                }
                            }
                        }
                    }
                } ?>
            </ul>
        </div>
        <?php
        }
        }
        if ($showSessions) { ?>
            <div class="panel panel-default">
                <div class="panel-heading"><?php echo get_lang('Sessions'); ?></div>
                <div class="panel-body">
                <?php if ($action == 'display_sessions' && $_SERVER['REQUEST_METHOD'] != 'POST') { ?>
                    <strong><?php echo get_lang('Sessions'); ?></strong>
                <?php } else { ?>
                    <a href="<?php echo getCourseCategoryUrl(1, $pageLength, null, 0, 'display_sessions'); ?>"><?php echo get_lang('SessionList'); ?></a>
                <?php } ?>

                <p><?php echo get_lang('SearchActiveSessions') ?></p>
                <form class="form-search" method="post" action="<?php echo getCourseCategoryUrl(1, $pageLength, null, 0, 'display_sessions'); ?>">
                    <div class="input-append">
                        <?php echo Display::input('date', 'date', $date, array(
                            'class' => 'span2',
                            'id' => 'date',
                            'readonly' => ''
                        )); ?>

                        <button class="btn btn-default" type="submit">
                            <?php echo get_lang('Search'); ?>
                        </button>
                    </div>
                </form>
                </div>
            </div>
        <?php } ?>
    </div>

    <div class="col-md-9">
        <h2><?php echo get_lang('CourseCatalog')?></h2>

        <?php if ($showCourses && $action != 'display_sessions') { ?>
            <?php

            if (!empty($message)) {
                Display::display_confirmation_message($message, false);
            }
            if (!empty($error)) {
                Display::display_error_message($error, false);
            }

            if (!empty($content)) {
                echo $content;
            }

            if (!empty($search_term)) {
                echo "<p><strong>".get_lang('SearchResultsFor')." ".Security::remove_XSS($_POST['search_term'])."</strong><br />";
            }

            $ajax_url = api_get_path(WEB_AJAX_PATH).'course.ajax.php?a=add_course_vote';
            $user_id = api_get_user_id();

            if (!empty($browse_courses_in_category)) {
                foreach ($browse_courses_in_category as $course) {
                    $course_hidden = ($course['visibility'] == COURSE_VISIBILITY_HIDDEN);

                    if ($course_hidden) {
                        continue;
                    }

                    $user_registerd_in_course = CourseManager::is_user_subscribed_in_course($user_id, $course['code']);
                    $user_registerd_in_course_as_teacher = CourseManager::is_course_teacher($user_id, $course['code']);
                    $user_registerd_in_course_as_student = ($user_registerd_in_course && !$user_registerd_in_course_as_teacher);
                    $course_public = ($course['visibility'] == COURSE_VISIBILITY_OPEN_WORLD);
                    $course_open = ($course['visibility'] == COURSE_VISIBILITY_OPEN_PLATFORM);
                    $course_private = ($course['visibility'] == COURSE_VISIBILITY_REGISTERED);
                    $course_closed = ($course['visibility'] == COURSE_VISIBILITY_CLOSED);
                    $course_subscribe_allowed = ($course['subscribe'] == 1);
                    $course_unsubscribe_allowed = ($course['unsubscribe'] == 1);
                    $count_connections = $course['count_connections'];
                    $creation_date = substr($course['creation_date'],0,10);

                    $icon_title = null;

                    // display the course bloc
                    echo '<div class="well_border"><div class="row">';

                    // display thumbnail
                    display_thumbnail($course, $icon_title);

                    // display course title and button bloc
                    echo '<div class="col-md-8">';
                    display_title($course);
                    // display button line
                    echo '<div class="btn-toolbar">';
                    // if user registered as student
                    if ($user_registerd_in_course_as_student) {
                        if (!$course_closed) {
                            display_goto_button($course);
                            display_description_button($course, $icon_title);
                            if ($course_unsubscribe_allowed) {
                                display_unregister_button($course, $stok, $search_term, $code);
                            }
                            display_already_registered_label('student');
                        }
                    } elseif ($user_registerd_in_course_as_teacher) {
                        // if user registered as teacher
                        display_goto_button($course);
                        display_description_button($course, $icon_title);
                        if ($course_unsubscribe_allowed) {
                            display_unregister_button($course, $stok, $search_term, $code);
                        }
                        display_already_registered_label('teacher');
                    } else {
                        // if user not registered in the course
                        if (!$course_closed) {
                            if (!$course_private) {
                                display_goto_button($course);
                                if ($course_subscribe_allowed) {
                                    display_register_button($course, $stok, $code, $search_term);
                                }
                            }
                            display_description_button($course, $icon_title);
                        }
                    }
                    echo '</div>'; // btn-toolbar
                    echo '</div>'; // span4

                    // display counter
                    echo '<div class="col-md-2">';
                    echo '<div class="course-block-popularity"><span>'.get_lang('ConnectionsLastMonth').'</span><div class="course-block-popularity-score">'.$count_connections.'</div></div>';
                    echo '</div>';

                    // end of course bloc
                    echo '</div></div>'; // well_border row
                }
            } else {
                if (!isset($_REQUEST['subscribe_user_with_password']) &&
                    !isset($_REQUEST['subscribe_course'])
                ) {
                    Display::display_warning_message(get_lang('ThereAreNoCoursesInThisCategory'));
                }
            } ?>
        <?php } ?>
        <?php
        echo $cataloguePagination;
        ?>
    </div>
</div>
<?php

/**
 * Display the course catalog image of a course
 * @param $course
 * @param $icon_title
 */
function display_thumbnail($course, $icon_title)
{
    $title = cut($course['title'], 70);
    // course path
    $course_path = api_get_path(SYS_COURSE_PATH).$course['directory'];

    if (file_exists($course_path.'/course-pic85x85.png')) {
        $course_medium_image = api_get_path(WEB_COURSE_PATH).$course['directory'].'/course-pic85x85.png'; // redimensioned image 85x85
    } else {
        $course_medium_image = Display::return_icon('course.png', null, null, ICON_SIZE_BIG, null, true); // without picture
    }

    // course image
    echo '<div class="col-md-2">';
    echo '<div class="thumbnail">';
    if (api_get_setting('show_courses_descriptions_in_catalog') == 'true') {
        echo '<a class="ajax" href="'.api_get_path(WEB_CODE_PATH).'inc/ajax/course_home.ajax.php?a=show_course_information&amp;code='.$course['code'].'" title="'.$icon_title.'" rel="gb_page_center[778]">';
        echo '<img src="'.$course_medium_image.'" alt="'.api_htmlentities($title).'" />';
        echo '</a>';
    } else {
        echo '<img src="'.$course_medium_image.'" alt="'.api_htmlentities($title).'"/>';
    }
    echo '</div>';  // thumbail
    echo '</div>';  // span2
}

/**
 * Display the title of a course in course catalog
 * @param $course
 */
function display_title($course)
{
    $title = cut($course['title'], 70);
    $ajax_url = api_get_path(WEB_AJAX_PATH).'course.ajax.php?a=add_course_vote';
    $teachers = CourseManager::get_teacher_list_from_course_code_to_string($course['code']);
    $rating = Display::return_rating_system('star_'.$course['real_id'], $ajax_url.'&amp;course_id='.$course['real_id'], $course['point_info']);

    $teachers = '<h5>'.$teachers.'</h5>';
    echo '<div class="categories-course-description">';
    echo '<h3>'.cut($title, 60).'</h3>';
    echo $teachers;
    echo $rating;
    echo '</div>';  // categories-course-description
}

/**
 * Display the description button of a course in the course catalog
 * @param $course
 * @param $icon_title
 */
function display_description_button($course, $icon_title)
{
    if (api_get_setting('show_courses_descriptions_in_catalog') == 'true') {
        echo '<a class="ajax btn btn-default" href="'.api_get_path(WEB_CODE_PATH).'inc/ajax/course_home.ajax.php?a=show_course_information&amp;code='.$course['code'].'" title="'.$icon_title.'">'.get_lang('Description').'</a>';
    }
}

/**
 * Display the goto course button of a course in the course catalog
 * @param $course
 */
function display_goto_button($course)
{
    echo ' <a class="btn btn-primary" href="'.api_get_course_url($course['code']).'">'.get_lang('GoToCourse').'</a>';
}

/**
 * Display the already registerd text in a course in the course catalog
 * @param $in_status
 */
function display_already_registered_label($in_status)
{
    $icon = Display::return_icon('teachers.gif', get_lang('Teacher'));
    if ($in_status == 'student') {
        $icon = Display::return_icon('students.gif', get_lang('Student'));
    }
    echo Display::label($icon.' '.get_lang("AlreadyRegisteredToCourse"), "info");
}

/**
 * Display the register button of a course in the course catalog
 * @param $course
 * @param $stok
 * @param $code
 * @param $search_term
 */
function display_register_button($course, $stok, $code, $search_term)
{
    echo ' <a class="btn btn-primary" href="'.api_get_self().'?action=subscribe_course&amp;sec_token='.$stok.'&amp;subscribe_course='.$course['code'].'&amp;search_term='.$search_term.'&amp;category_code='.$code.'">'.get_lang('Subscribe').'</a>';
}

/**
 * Display the unregister button of a course in the course catalog
 * @param $course
 * @param $stok
 * @param $search_term
 * @param $code
 */
function display_unregister_button($course, $stok, $search_term, $code)
{
    echo ' <a class="btn btn-primary" href="'. api_get_self().'?action=unsubscribe&amp;sec_token='.$stok.'&amp;unsubscribe='.$course['code'].'&amp;search_term='.$search_term.'&amp;category_code='.$code.'">'.get_lang('Unsubscribe').'</a>';
}


