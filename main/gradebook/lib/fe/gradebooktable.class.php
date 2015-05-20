<?php
/* For licensing terms, see license.txt */

use CpChart\Classes\pCache as pCache;
use CpChart\Classes\pData as pData;
use CpChart\Classes\pImage as pImage;

/**
 * GradebookTable Class
 * Table to display categories, evaluations and links
 * @author Stijn Konings
 * @author Bert Steppé (refactored, optimised)
 * @package chamilo.gradebook
 */
class GradebookTable extends SortableTable
{
    private $currentcat;
    private $datagen;
    private $evals_links;
    public $cats;
    private $dataForGraph;

    /**
     * Constructor
     * @param Category $currentcat
     * @param array $cats
     * @param array $evals
     * @param array $links
     * @param null $addparams
     */
    public function __construct($currentcat, $cats = array(), $evals = array(), $links = array(), $addparams = null)
    {
        parent::__construct('gradebooklist', null, null, api_is_allowed_to_edit() ? 1 : 0);
        $this->evals_links = array_merge($evals, $links);
        $this->currentcat = $currentcat;
        $this->cats = $cats;
        $this->datagen = new GradebookDataGenerator($cats, $evals, $links);

        if (isset($addparams)) {
            $this->set_additional_parameters($addparams);
        }

        $column= 0;
        if (api_is_allowed_to_edit(null, true)) {
            $this->set_header($column++, '', '', 'width="25px"');
        }

        $this->set_header($column++, get_lang('Type'), '', 'width="35px"');
        $this->set_header($column++, get_lang('Name'), false);
        $this->set_header($column++, get_lang('Description'), false);

        if (api_is_allowed_to_edit(null, true)) {
            $this->set_header(
                $column++,
                get_lang('Weight'),
                '',
                'width="100px"'
            );
        } else {
            $this->set_header($column++, get_lang('Weight'), false);
            $this->set_header($column++, get_lang('Result'), false);
            $this->set_header($column++, get_lang('Ranking'), false);
            $this->set_header($column++, get_lang('Best'), false);
            $this->set_header($column++, get_lang('Average'), false);

            if (!empty($cats)) {
                $this->set_header($column++, get_lang('Actions'), false);
            }
        }

        // Deactivates the odd/even alt rows in order that the +/- buttons work see #4047
        $this->odd_even_rows_enabled = false;

        // Admins get an edit column.
        if (api_is_allowed_to_edit(null, true)) {
            $this->set_header($column++, get_lang('Modify'), false, 'width="195px"');
            // Actions on multiple selected documents.
            $this->set_form_actions(array(
                    'setvisible' => get_lang('SetVisible'),
                    'setinvisible' => get_lang('SetInvisible'),
                    'deleted' => get_lang('DeleteSelected')
                )
            );
        } else {
            if (empty($_GET['selectcat']) && !api_is_allowed_to_edit()) {
                $this->set_header($column++, get_lang('Certificates'), false);
            }
        }
    }

    /**
     * @return GradebookDataGenerator
     */
    public function get_data()
    {
        return $this->datagen;
    }

    /**
     * Function used by SortableTable to get total number of items in the table
     * @return int
     */
    public function get_total_number_of_items()
    {
        return $this->datagen->get_total_items_count();
    }

    /**
     * Function used by SortableTable to generate the data to display
     * @param int $from
     * @param int $per_page
     * @param int $column
     * @param string $direction
     * @param int $sort
     * @return array|mixed
     */
    public function get_table_data($from = 1, $per_page = null, $column = null, $direction = null, $sort = null)
    {
        //variables load in index.php
        global $certificate_min_score;
        // determine sorting type
        $col_adjust = api_is_allowed_to_edit() ? 1 : 0;
        // By id
        $this->column = 5;

        switch ($this->column) {
            // Type
            case (0 + $col_adjust) :
                $sorting = GradebookDataGenerator :: GDG_SORT_TYPE;
                break;
            case (1 + $col_adjust) :
                $sorting = GradebookDataGenerator :: GDG_SORT_NAME;
                break;
            case (2 + $col_adjust) :
                $sorting = GradebookDataGenerator :: GDG_SORT_DESCRIPTION;
                break;
            case (3 + $col_adjust) :
                $sorting = GradebookDataGenerator :: GDG_SORT_WEIGHT;
                break;
            case (4 + $col_adjust) :
                $sorting = GradebookDataGenerator :: GDG_SORT_DATE;
            case (5 + $col_adjust) :
                $sorting = GradebookDataGenerator :: GDG_SORT_ID;
                break;
        }

        if ($this->direction == 'DESC') {
            $sorting |= GradebookDataGenerator :: GDG_SORT_DESC;
        } else {
            $sorting |= GradebookDataGenerator :: GDG_SORT_ASC;
        }

        // Status of user in course.
        $user_id = api_get_user_id();
        $course_code = api_get_course_id();
        $session_id = api_get_session_id();
        $status_user = api_get_status_of_user_in_course($user_id, api_get_course_int_id());

        if (empty($session_id)) {
            $statusToFilter = STUDENT;
        } else {
            $statusToFilter = 0;
        }

        $studentList = CourseManager::get_user_list_from_course_code(
            $course_code,
            $session_id,
            null,
            null,
            $statusToFilter
        );

        $data_array = $this->datagen->get_data(
            $sorting,
            $from,
            $this->per_page,
            false,
            $studentList
        );

        // generate the data to display
        $sortable_data = array();
        $weight_total_links = 0;
        $main_categories = array();
        $main_cat =  Category::load(null, null, $course_code, null, null, $session_id, false);
        $total_categories_weight = 0;
        $scoredisplay = ScoreDisplay :: instance();

        $totalResult = [0, 0];
        $totalBest = [0, 0];
        $totalAverage = [0, 0];

        // Categories.
        foreach ($data_array as $data) {

            // list of items inside the gradebook (exercises, lps, forums, etc)
            $row  = array();
            /** @var AbstractLink $item */
            $item = $mainCategory = $data[0];

            //if the item is invisible, wrap it in a span with class invisible
            $invisibility_span_open  = api_is_allowed_to_edit() && $item->is_visible() == '0' ? '<span class="invisible">' : '';
            $invisibility_span_close = api_is_allowed_to_edit() && $item->is_visible() == '0' ? '</span>' : '';

            // Id
            if (api_is_allowed_to_edit(null, true)) {
                $row[] = $this->build_id_column($item);
            }

            // Type.
            $row[] = $this->build_type_column($item);

            $this->dataForGraph['categories'][] = $item->get_name();

            // Name.
            if (get_class($item) == 'Category') {
                $row[] = $invisibility_span_open.'<h3>'.$item->get_name().'</h3>'.$invisibility_span_close;
                $main_categories[$item->get_id()]['name'] = $item->get_name();
            } else {
                $row[] = $invisibility_span_open.$this->build_name_link($item) . $invisibility_span_close;
                $main_categories[$item->get_id()]['name'] = $this->build_name_link($item);
            }

            $main_categories[$item->get_id()]['weight']= $item->get_weight();
            $total_categories_weight += $item->get_weight();

            // Description.
            $row[] = $invisibility_span_open.$data[2].$invisibility_span_close;

            // Weight.
            $weight = $scoredisplay->display_score(
                array(
                    $data['3'],
                    $this->currentcat->get_weight()
                ),
                SCORE_SIMPLE,
                SCORE_BOTH,
                true
            );

            if (api_is_allowed_to_edit(null, true)) {
                $row[] = $invisibility_span_open .Display::tag('h4', $weight).$invisibility_span_close;
            } else {
                $row[] = $invisibility_span_open .$weight.$invisibility_span_close;
            }

            $category_weight = $item->get_weight();
            $mainCategoryWeight = $main_cat[0]->get_weight();

            if (api_is_allowed_to_edit(null, true)) {
                $weight_total_links += $data[3];
            } else {
                $cattotal = Category::load($_GET['selectcat']);
                $scoretotal = $cattotal[0]->calc_score(api_get_user_id());
                $item_value = $scoredisplay->display_score($scoretotal, SCORE_SIMPLE);
            }

            // Edit (for admins).
            if (api_is_allowed_to_edit(null, true)) {
                $cat = new Category();
                $show_message = $cat->show_message_resource_delete($item->get_course_code());
                if ($show_message === false) {
                    $row[] = $this->build_edit_column($item);
                }
            } else {
                $score = $item->calc_score(api_get_user_id());

                if (!empty($score[1])) {
                    $completeScore = $scoredisplay->display_score($score, SCORE_DIV_PERCENT);
                    $score = $score[0]/$score[1]*$item->get_weight();
                    $score = $scoredisplay->display_score(array($score, null), SCORE_SIMPLE);
                    $scoreToDisplay = Display::tip($score, $completeScore);
                } else {
                    $scoreToDisplay = '-';
                    $categoryScore = null;
                }

                // Students get the results and certificates columns
                //if (count($this->evals_links) > 0 && $status_user != 1) {
                if ($status_user != 1) {
                    $value_data = isset($data[4]) ? $data[4] : null;
                    $best = isset($data['best']) ? $data['best'] : null;
                    $average = isset($data['average']) ? $data['average'] : null;
                    $ranking = isset($data['ranking']) ? $data['ranking'] : null;

                    $totalResult = [
                        $totalResult[0] + $data['result_score_weight'][0],
                        $totalResult[1] + $data['result_score_weight'][1],
                    ];

                    $totalBest = [
                        $totalBest[0] + $data['best_score'][0],
                        $totalBest[1] + $data['best_score'][1],
                    ];

                    $totalAverage = [
                        $totalAverage[0] + $data['average_score'][0],
                        $totalAverage[1] + $data['average_score'][1],
                    ];

                    $this->dataForGraph['my_result'][] = (float) $scoredisplay->display_score($totalResult, SCORE_AVERAGE);
                    $this->dataForGraph['average'][] = (float) $scoredisplay->display_score($totalAverage, SCORE_AVERAGE);

                    // Student result
                    $row[] = $value_data;
                    // Ranking
                    $row[] = $ranking;
                    // Best
                    $row[] = $best;
                    // Average
                    $row[] = $average;

                    if (get_class($item) == 'Category') {
                        $row[] = $this->build_edit_column($item);
                    }
                } else {
                    $row[] = $scoreToDisplay;

                    if (!empty($this->cats)) {
                        $row[] = $this->build_edit_column($item);
                    }
                }
            }

            // Category added.
            $sortable_data[] = $row;

            // Loading children
            if (get_class($item) == 'Category') {
                $stud_id = api_get_user_id();
                $course_code = api_get_course_id();
                $session_id = api_get_session_id();
                $parent_id = $item->get_id();
                $cats = Category::load($parent_id, null, null, null, null, null);

                if (isset($cats[0])) {
                    $allcat  = $cats[0]->get_subcategories($stud_id, $course_code, $session_id);
                    $alleval = $cats[0]->get_evaluations($stud_id);
                    $alllink = $cats[0]->get_links($stud_id);

                    $sub_cat_info = new GradebookDataGenerator($allcat, $alleval, $alllink);
                    $data_array = $sub_cat_info->get_data($sorting, $from, $this->per_page);
                    $total_weight = 0;

                    // Links.
                    foreach ($data_array as $data) {

                        $row = array();
                        $item = $data[0];

                        //if the item is invisible, wrap it in a span with class invisible
                        $invisibility_span_open = api_is_allowed_to_edit() && $item->is_visible() == '0' ? '<span class="invisible">' : '';
                        $invisibility_span_close = api_is_allowed_to_edit() && $item->is_visible() == '0' ? '</span>' : '';

                        if (isset($item)) {
                            $main_categories[$parent_id]['children'][$item->get_id()]['name'] = $item->get_name();
                            $main_categories[$parent_id]['children'][$item->get_id()]['weight'] = $item->get_weight();
                        }

                        if (api_is_allowed_to_edit(null, true)) {
                            $row[] = $this->build_id_column($item);
                        }

                        $row[] = $this->build_type_column($item, array('style' => 'padding-left:5px'));

                        // Name.
                        $row[] = $invisibility_span_open."&nbsp;&nbsp;&nbsp;  ".$this->build_name_link($item) . $invisibility_span_close;

                        // Description.
                        $row[] = $invisibility_span_open.$data[2].$invisibility_span_close;

                        $weight = $data[3];
                        $total_weight += $weight;

                        $row[] = $invisibility_span_open.$weight.$invisibility_span_close;

                        if (api_is_allowed_to_edit(null, true)) {
                            //$weight_total_links += intval($data[3]);
                        } else {
                            $cattotal = Category::load($_GET['selectcat']);
                            $scoretotal = $cattotal[0]->calc_score(api_get_user_id());
                            $item_value = $scoretotal[0];
                        }

                        // Admins get an edit column.
                        if (api_is_allowed_to_edit(null, true)) {
                            $cat = new Category();
                            $show_message = $cat->show_message_resource_delete($item->get_course_code());
                            if ($show_message === false) {
                                $row[] = $this->build_edit_column($item);
                            }
                        } else {
                            // Students get the results and certificates columns
                            $eval_n_links = array_merge($alleval, $alllink);

                            if (count($eval_n_links)> 0 && $status_user != 1) {
                                $value_data = isset($data[4]) ? $data[4] : null;

                                if (!is_null($value_data)) {
                                    //$score = $item->calc_score(api_get_user_id());
                                    //$new_score = $data[3] * $score[0] / $score[1];
                                    //$new_score = floatval(number_format($new_score, api_get_setting('gradebook_number_decimals')));

                                    // Result
                                    $row[] = $value_data;

                                    $best = isset($data['best']) ? $data['best'] : null;
                                    $average = isset($data['average']) ? $data['average'] : null;
                                    $ranking = isset($data['ranking']) ? $data['ranking'] : null;

                                    // Ranking
                                    $row[] = $ranking;
                                    // Best
                                    $row[] = $best;
                                    // Average
                                    $row[] = $average;
                                }
                            }

                            if (!empty($cats)) {
                                $row[] = null;
                            }
                        }
                        $row['child_of'] = $parent_id;
                        $sortable_data[] = $row;
                    }

                    // "Warning row"
                    if (!empty($data_array)) {
                        if (api_is_allowed_to_edit()) {
                            // Compare the category weight to the sum of all weights inside the category
                            if (intval($total_weight) == $category_weight) {
                                $label = null;
                                $total = GradebookUtils::score_badges(
                                    array(
                                        $total_weight.' / '.$category_weight,
                                        '100'
                                    )
                                );
                            } else {
                                $label = Display::return_icon(
                                    'warning.png',
                                    sprintf(get_lang('TotalWeightMustBeX'), $category_weight)
                                );
                                $total = Display::badge($total_weight.' / '.$category_weight, 'warning');
                            }
                            $row = array(
                                null,
                                null,
                                "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<h5>".get_lang('SubTotal').'</h5>',
                                null,
                                $total.' '.$label,
                                'child_of' => $parent_id
                            );
                            $sortable_data[] = $row;
                        }
                    }
                }
            }
        } //end looping categories

        if (api_is_allowed_to_edit()) {
            // Total for teacher.
            if (count($main_cat) > 1) {
                $main_weight = intval($main_cat[0]->get_weight());

                if (intval($total_categories_weight) == $main_weight) {
                    $total = GradebookUtils::score_badges(
                        array($total_categories_weight.' / '.$main_weight, '100')
                    );
                } else {
                    $total = Display::badge($total_categories_weight.' / '.$main_weight, 'warning');
                }
                $row = array(
                    null,
                    null,
                    '<h3>' . get_lang('Total') . '</h3>',
                    null,
                    $total
                );
                $sortable_data[] = $row;
            }
        } else {
            // Total for student.
            if (count($main_cat) > 1) {
                $main_weight = intval($main_cat[0]->get_weight());

                $global = null;
                $average = null;
                // Overwrite main weight
                $totalResult[1] = $main_weight;

                $totalResult = $scoredisplay->display_score(
                    $totalResult,
                    SCORE_DIV
                );

                $totalRanking = array();
                foreach ($studentList as $student) {
                    $score = $main_cat[0]->calc_score($student['user_id']);
                    $totalRanking[$student['user_id']] = $score[0];
                }

                $totalRanking = AbstractLink::getCurrentUserRanking($totalRanking);

                $totalRanking = $scoredisplay->display_score(
                    $totalRanking,
                    SCORE_DIV
                );

                $totalBest = $scoredisplay->display_score(
                    $totalBest,
                    SCORE_DIV
                );

                $totalAverage = $scoredisplay->display_score(
                    $totalAverage,
                    SCORE_DIV
                );

                $row = array(
                    null,
                    '<h3>' . get_lang('Total') . '</h3>',
                    null,
                    $main_weight,
                    $totalResult,
                    $totalRanking,
                    $totalBest,
                    $totalAverage,
                );

                $sortable_data[] = $row;
            }
        }

        // Warning messages
        $view = isset($_GET['view']) ? $_GET['view']: null;

        if (api_is_allowed_to_edit()) {
            if (isset($_GET['selectcat']) &&
                $_GET['selectcat'] > 0 &&
                $view <> 'presence'
            ) {
                $id_cat = intval($_GET['selectcat']);
                $category = Category::load($id_cat);

                $weight_category = intval($this->build_weight($category[0]));

                $course_code = $this->build_course_code($category[0]);
                $weight_total_links  = round($weight_total_links);

                if ($weight_total_links > $weight_category ||
                    $weight_total_links < $weight_category ||
                    $weight_total_links > $weight_category
                ) {
                    $warning_message = sprintf(get_lang('TotalWeightMustBeX'), $weight_category);
                    $modify_icons  = '<a class="right_link" href="gradebook_edit_cat.php?editcat='.$id_cat.'&cidReq='.$course_code.'&id_session='.api_get_session_id().'">'.
                            Display::return_icon('edit.png', $warning_message, array(), ICON_SIZE_SMALL).'</a>';
                    $warning_message .= $modify_icons;
                    Display::display_warning_message($warning_message, false);
                }

                $content_html = DocumentManager::replace_user_info_into_html(
                    api_get_user_id(),
                    $course_code
                );

                if (!empty($content_html)) {
                    $new_content = explode('</head>',$content_html['content']);
                }

                if (empty($new_content[0])) {
                    // Set default certificate
                    $courseData = api_get_course_info($course_code);
                    DocumentManager::generateDefaultCertificate($courseData);
                }
            }

            if (empty($_GET['selectcat'])) {
                $categories = Category :: load();
                $weight_categories = $certificate_min_scores = $course_codes = array();
                foreach ($categories as $category) {
                    $course_code_category = $this->build_course_code($category);
                    if (!empty($course_code)) {
                        if ($course_code_category == $course_code) {
                            $weight_categories[] = intval($this->build_weight($category));
                            $certificate_min_scores[] = intval($this->build_certificate_min_score($category));
                            $course_codes[] = $course_code;
                            break;
                        }
                    } else {
                        $weight_categories[] = intval($this->build_weight($category));
                        $certificate_min_scores[] = intval($this->build_certificate_min_score($category));
                        $course_codes[] = $course_code_category;
                    }
                }

                if (is_array($weight_categories) &&
                    is_array($certificate_min_scores) &&
                    is_array($course_codes)
                ) {
                    $warning_message = '';
                    for ($x = 0; $x<count($weight_categories);$x++) {
                        $weight_category = intval($weight_categories[$x]);
                        $certificate_min_score = intval($certificate_min_scores[$x]);
                        $course_code = $course_codes[$x];

                        if (empty($certificate_min_score) ||
                            ($certificate_min_score > $weight_category)
                        ) {
                            $warning_message .= $course_code .'&nbsp;-&nbsp;'.get_lang('CertificateMinimunScoreIsRequiredAndMustNotBeMoreThan').'&nbsp;'.$weight_category.'<br />';
                        }
                    }

                    if (!empty($warning_message)) {
                        Display::display_warning_message($warning_message,false);
                    }
                }
            }
        }

        return $sortable_data;
    }


    /**
     * @return array
     */
    private function getDataForGraph()
    {
        return $this->dataForGraph;
    }

    /**
     * @return string
     */
    public function getGraph()
    {
        $data = $this->getDataForGraph();
        if (!empty($data) &&
            isset($data['categories']) &&
            isset($data['my_result']) &&
            isset($data['average'])
        ) {
            $dataSet = new pData();
            $dataSet->addPoints($data['my_result'], get_lang('Me'));
            // In order to generate random values
            // $data['average'] = array(rand(0,50), rand(0,50));
            $dataSet->addPoints($data['average'], get_lang('Average'));
            $dataSet->addPoints($data['categories'], 'categories');

            $dataSet->setAbscissa("categories");
            $xSize = 600;
            $ySize = 400;
            $pChart = new pImage($xSize, $ySize, $dataSet);
            /* Turn of Antialiasing */
            $pChart->Antialias = FALSE;

            /* Add a border to the picture */
            $pChart->drawRectangle(0,0,$xSize-10,$ySize-10,array("R"=>0,"G"=>0,"B"=>0));

            $pChart->drawText(10,16,get_lang('Results'),array("FontSize"=>11,"Align"=>TEXT_ALIGN_BOTTOMLEFT));

            $pChart->setGraphArea(50, 30, $xSize-50, $ySize-50);

            $pChart->setFontProperties(
                array(
                    'FontName' => api_get_path(SYS_FONTS_PATH) . 'opensans/OpenSans-Regular.ttf',
                    'FontSize' => 10
                )
            );

            /* Draw the scale */
            $scaleSettings = array(
                "XMargin" => 10,
                "YMargin" => 10,
                "Floating" => true,
                "GridR" => 200,
                "GridG" => 200,
                "GridB" => 200,
                "DrawSubTicks" => true,
                "CycleBackground" => true,
            );
            $pChart->drawScale($scaleSettings);

            /* Draw the line chart */
            $pChart->drawLineChart();
            $pChart->drawPlotChart(array("DisplayValues"=>TRUE,"PlotBorder"=>TRUE,"BorderSize"=>2,"Surrounding"=>-60,"BorderAlpha"=>80));

            /* Write the chart legend */
            $pChart->drawLegend(
                $xSize - 180,
                9,
                array(
                    "Style" => LEGEND_NOBORDER,
                    "Mode" => LEGEND_HORIZONTAL,
                    "FontR" => 0,
                    "FontG" => 0,
                    "FontB" => 0,
                )
            );

            $cachePath = api_get_path(SYS_ARCHIVE_PATH);
            $myCache = new pCache(array('CacheFolder' => substr($cachePath, 0, strlen($cachePath) - 1)));
            $chartHash = $myCache->getHash($dataSet);

            $myCache->writeToCache($chartHash, $pChart);
            $imgSysPath = api_get_path(SYS_ARCHIVE_PATH) . $chartHash;
            $myCache->saveFromCache($chartHash, $imgSysPath);
            $imgWebPath = api_get_path(WEB_ARCHIVE_PATH) . $chartHash;

            if (file_exists($imgSysPath)) {
                $result = '<div id="contentArea" style="text-align: center;" >';
                $result .= '<img src="' . $imgWebPath.'" >';
                $result .= '</div>';
                return $result;
            }
        }

        return '';
    }

    /**
     * @param $item
     * @return mixed
     */
    private function build_certificate_min_score($item)
    {
        return $item->get_certificate_min_score();
    }

    /**
     * @param $item
     * @return mixed
     */
    private function build_weight($item)
    {
        return $item->get_weight();
    }

    /**
     * @param $item
     * @return mixed
     */
    private function build_course_code($item)
    {
        return $item->get_course_code();
    }

    /**
     * @param $item
     * @return string
     */
    private function build_id_column($item)
    {
        switch ($item->get_item_type()) {
            // category
            case 'C' :
                return 'CATE' . $item->get_id();
            // evaluation
            case 'E' :
                return 'EVAL' . $item->get_id();
            // link
            case 'L' :
                return 'LINK' . $item->get_id();
        }
    }

    /**
     * @param $item
     * @param array $attributes
     * @return string
     */
    private function build_type_column($item, $attributes = array())
    {
        return GradebookUtils::build_type_icon_tag($item->get_icon_name(), $attributes);
    }

    /**
     * Generate name column
     * @param unknown_type $item
     * @return string
     */
    private function build_name_link($item)
    {
        $view = isset($_GET['view']) ? Security::remove_XSS($_GET['view']) : null;
        switch ($item->get_item_type()) {
            // category
            case 'C' :
                $prms_uri='?selectcat=' . $item->get_id() . '&amp;view='.$view;

                if (isset($_GET['isStudentView'])) {
                    if ( isset($is_student) || ( isset($_SESSION['studentview']) && $_SESSION['studentview']=='studentview') ) {
                        $prms_uri=$prms_uri.'&amp;isStudentView='.Security::remove_XSS($_GET['isStudentView']);
                    }
                }

                $cat = new Category();
                $show_message=$cat->show_message_resource_delete($item->get_course_code());
                return '&nbsp;<a href="'.Security::remove_XSS($_SESSION['gradebook_dest']).$prms_uri.'">'
                . $item->get_name()
                . '</a>'
                . ($item->is_course() ? ' &nbsp;[' . $item->get_course_code() . ']'.$show_message : '');
            // evaluation
            case 'E' :
                $cat = new Category();
                $course_id = CourseManager::get_course_by_category($_GET['selectcat']);
                $show_message = $cat->show_message_resource_delete($course_id);

                // course/platform admin can go to the view_results page
                if (api_is_allowed_to_edit() && $show_message===false) {
                    if ($item->get_type() == 'presence') {
                        return '&nbsp;'
                        . '<a href="gradebook_view_result.php?cidReq='.$course_id.'&amp;selecteval=' . $item->get_id() . '">'
                        . $item->get_name()
                        . '</a>';
                    } else {
                        return '&nbsp;'
                        . '<a href="gradebook_view_result.php?cidReq='.$course_id.'&amp;selecteval=' . $item->get_id() . '">'
                        . $item->get_name()
                        . '</a>&nbsp;'.Display::label(get_lang('Evaluation'));
                    }
                } elseif (ScoreDisplay :: instance()->is_custom() && $show_message===false) {
                    // students can go to the statistics page (if custom display enabled)
                    return '&nbsp;'
                    . '<a href="gradebook_statistics.php?selecteval=' . $item->get_id() . '">'
                    . $item->get_name()
                    . '</a>';

                } elseif ($show_message === false && !api_is_allowed_to_edit() && !ScoreDisplay :: instance()->is_custom()) {
                    return '&nbsp;'
                    . '<a href="gradebook_statistics.php?selecteval=' . $item->get_id() . '">'
                    . $item->get_name()
                    . '</a>';
                } else {
                    return '['.get_lang('Evaluation').']&nbsp;&nbsp;'.$item->get_name().$show_message;
                }
            case 'L':
                // link
                $cat = new Category();
                $course_id = CourseManager::get_course_by_category($_GET['selectcat']);
                $show_message = $cat->show_message_resource_delete($course_id);

                $url = $item->get_link();

                if (isset($url) && $show_message === false) {
                    $text = '&nbsp;<a href="' . $item->get_link() . '">'
                        . $item->get_name()
                        . '</a>';
                } else {
                    $text = $item->get_name();
                }

                $text .= "&nbsp;".Display::label($item->get_type_name(), 'info').$show_message;
                $cc = $this->currentcat->get_course_code();
                if (empty($cc)) {
                    $text .= '&nbsp;[<a href="'.api_get_path(REL_COURSE_PATH).$item->get_course_code().'/">'.$item->get_course_code().'</a>]';
                }
                return $text;
        }
    }

    /**
     * @param $item
     * @return null|string
     */
    private function build_edit_column($item)
    {
        switch ($item->get_item_type()) {
            // category
            case 'C' :
                return GradebookUtils::build_edit_icons_cat($item, $this->currentcat);
            // evaluation
            case 'E' :
                return GradebookUtils::build_edit_icons_eval($item, $this->currentcat->get_id());
            // link
            case 'L' :
                return GradebookUtils::build_edit_icons_link($item, $this->currentcat->get_id());
        }
    }
}
