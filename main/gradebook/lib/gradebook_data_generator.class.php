<?php
/* For licensing terms, see /license.txt */

/**
 * Class GradebookDataGenerator
 * Class to select, sort and transform object data into array data,
 * used for the general gradebook view
 * @author Bert Steppé
 * @package chamilo.gradebook
 */
class GradebookDataGenerator
{
    // Sorting types constants
    const GDG_SORT_TYPE            = 1;
    const GDG_SORT_NAME            = 2;
    const GDG_SORT_DESCRIPTION     = 4;
    const GDG_SORT_WEIGHT          = 8;
    const GDG_SORT_DATE            = 16;

    const GDG_SORT_ASC             = 32;
    const GDG_SORT_DESC            = 64;

    const GDG_SORT_ID              = 128;


    private $items;
    private $evals_links;

    /**
     * @param array $cats
     * @param array $evals
     * @param array $links
     */
    public function __construct($cats = array(), $evals = array(), $links = array())
    {
        $allcats = (isset($cats) ? $cats : array());
        $allevals = (isset($evals) ? $evals : array());
        $alllinks = (isset($links) ? $links : array());

        // if we are in the root category and if there are sub categories
        // display only links depending of the root category and not link that belongs
        // to a sub category https://support.chamilo.org/issues/6602
        $tabLinkToDisplay = $alllinks;
        if (count($allcats) > 0) {
            // get sub categories id
            $tabCategories = array();
            for ($i=0; $i < count($allcats); $i++) {
                $tabCategories[] = $allcats[$i]->get_id();
            }
            // dont display links that belongs to a sub category
            $tabLinkToDisplay = array();
            for ($i=0; $i < count($alllinks); $i++) {
                if (!in_array($alllinks[$i]->get_category_id(), $tabCategories)) {
                    $tabLinkToDisplay[] = $alllinks[$i];
                }
            }
        }

        // merge categories, evaluations and links
        $this->items = array_merge($allcats, $allevals, $tabLinkToDisplay);
        $this->evals_links = array_merge($allevals, $tabLinkToDisplay);
    }

    /**
     * Get total number of items (rows)
     * @return int
     */
    public function get_total_items_count()
    {
        return count($this->items);
    }

    /**
     * Get actual array data
     * @return array 2-dimensional array - each array contains the elements:
     * 0: cat/eval/link object
     * 1: item name
     * 2: description
     * 3: weight
     * 4: date
     * 5: student's score (if student logged in)
     */
    public function get_data($sorting = 0, $start = 0, $count = null, $ignore_score_color = false)
    {
        //$status = CourseManager::get_user_in_course_status(api_get_user_id(), api_get_course_id());
        // do some checks on count, redefine if invalid value
        if (!isset($count)) {
            $count = count ($this->items) - $start;
        }
        if ($count < 0) {
            $count = 0;
        }

        $allitems = $this->items;
        // sort array
        if ($sorting & self :: GDG_SORT_TYPE) {
            usort($allitems, array('GradebookDataGenerator', 'sort_by_type'));
        } elseif ($sorting & self :: GDG_SORT_ID) {
            usort($allitems, array('GradebookDataGenerator', 'sort_by_id'));
        } elseif ($sorting & self :: GDG_SORT_NAME) {
            usort($allitems, array('GradebookDataGenerator', 'sort_by_name'));
        } elseif ($sorting & self :: GDG_SORT_DESCRIPTION) {
            usort($allitems, array('GradebookDataGenerator', 'sort_by_description'));
        } elseif ($sorting & self :: GDG_SORT_WEIGHT) {
            usort($allitems, array('GradebookDataGenerator', 'sort_by_weight'));
        } elseif ($sorting & self :: GDG_SORT_DATE) {
            //usort($allitems, array('GradebookDataGenerator', 'sort_by_date'));
        }
        if ($sorting & self :: GDG_SORT_DESC) {
            $allitems = array_reverse($allitems);
        }
        // get selected items
        $visibleitems = array_slice($allitems, $start, $count);
        //status de user in course
        $user_id = api_get_user_id();
        $course_code = api_get_course_id();
        $status_user = api_get_status_of_user_in_course($user_id, $course_code);

        // Generate the data to display
        $data = array();
        /** @var GradebookItem $item */
        foreach ($visibleitems as $item) {
            $row = array ();
            $row[] = $item;
            $row[] = $item->get_name();
            // display the 2 first line of description, and all description on mouseover (https://support.chamilo.org/issues/6588)
            $row[] = '<span title="'.api_remove_tags_with_space($item->get_description()).'">'.
                api_get_short_text_from_html($item->get_description(), 160).'</span>';
            $row[] = $item->get_weight();
            if (count($this->evals_links) > 0) {
                if (!api_is_allowed_to_edit() || $status_user != 1 ) {
                    $row[] = $this->build_result_column($item, $ignore_score_color);
                    $row['best'] = $this->buildBestResultColumn($item);
                    $row['average'] = $this->buildAverageResultColumn($item);
                    $row[] = $item;
                }
            } else {
                $row[] = $this->build_result_column($item, $ignore_score_color, true);
                $row['best'] = $this->buildBestResultColumn($item);
                $row['average'] = $this->buildAverageResultColumn($item);
            }
            $data[] = $row;
        }
        return $data;
    }


    /**
     * Get best result of an item
     * @param GradebookItem $item
     * @return string
     */
    private function buildBestResultColumn(GradebookItem $item)
    {
        $score = $item->calc_score(
            null,
            'best',
            api_get_course_id(),
            api_get_session_id()
        );

        $scoreDisplay = ScoreDisplay :: instance();

        return $scoreDisplay->display_score($score, SCORE_DIV);
    }

    /**
     * @param GradebookItem $item
     * @return string
     */
    private function buildAverageResultColumn(GradebookItem $item)
    {
        $score = $item->calc_score(null, 'average');
        $scoreDisplay = ScoreDisplay :: instance();
        return $scoreDisplay->display_score($score, SCORE_DIV);
    }

    /**
     * @param GradebookItem $item
     * @param $ignore_score_color
     * @return null|string
     */
    private function build_result_column($item, $ignore_score_color, $forceSimpleResult = false)
    {
        $scoredisplay = ScoreDisplay :: instance();
        $score = $item->calc_score(api_get_user_id());

        if (!empty($score)) {
            switch ($item->get_item_type()) {
                // category
                case 'C' :
                    if ($score != null) {
                        $displaytype = SCORE_PERCENT;
                        if ($ignore_score_color) {
                            $displaytype |= SCORE_IGNORE_SPLIT;
                        }
                        if ($forceSimpleResult) {
                            return $scoredisplay->display_score($score, SCORE_DIV);
                        }
                        return get_lang('Total') . ' : '. $scoredisplay->display_score($score, $displaytype);
                    } else {
                        return '';
                    }
                    break;
                // evaluation and link
                case 'E' :
                case 'L' :
                    /*$displaytype = SCORE_DIV_PERCENT;
                    if ($ignore_score_color) {
                        $displaytype |= SCORE_IGNORE_SPLIT;
                    }*/
                    return $scoredisplay->display_score($score, SCORE_DIV_PERCENT_WITH_CUSTOM);
            }
        }
        return null;
    }

    /**
     * @param GradebookItem $item
     * @return string
     */
    private function build_date_column($item)
    {
        $date = $item->get_date();
        if (!isset($date) || empty($date)) {
            return '';
        } else {
            if (is_int($date)) {
                return api_convert_and_format_date($date);
            } else {
                return api_format_date($date);
            }
        }
    }

    /**
     * Returns the link to the certificate generation, if the score is enough, otherwise
     * returns an empty string. This only works with categories.
     * @param    object Item
     */
    public function get_certificate_link($item)
    {
        if (is_a($item, 'Category')) {
            if ($item->is_certificate_available(api_get_user_id())) {
                $link = '<a href="'.Security::remove_XSS($_SESSION['gradebook_dest']).'?export_certificate=1&cat='.$item->get_id().'&user='.api_get_user_id().'">'.
                    get_lang('Certificate').'</a>';
                return $link;
            }
        }
        return '';
    }

    /**
     * @param GradebookItem $item1
     * @param GradebookItem $item2
     * @return int
     */
    public function sort_by_name($item1, $item2)
    {
        return api_strnatcmp($item1->get_name(), $item2->get_name());
    }

    /**
     * @param GradebookItem $item1
     * @param GradebookItem $item2
     * @return int
     */
    public function sort_by_id($item1, $item2)
    {
        return api_strnatcmp($item1->get_id(), $item2->get_id());
    }

    /**
     * @param GradebookItem $item1
     * @param GradebookItem $item2
     * @return int
     */
    public function sort_by_type($item1, $item2)
    {
        if ($item1->get_item_type() == $item2->get_item_type()) {
            return $this->sort_by_name($item1,$item2);
        } else {
            return ($item1->get_item_type() < $item2->get_item_type() ? -1 : 1);
        }
    }

    /**
     * @param GradebookItem $item1
     * @param GradebookItem $item2
     * @return int
     */
    public function sort_by_description($item1, $item2)
    {
        $result = api_strcmp($item1->get_description(), $item2->get_description());
        if ($result == 0) {
            return $this->sort_by_name($item1,$item2);
        }
        return $result;
    }

    /**
     * @param GradebookItem $item1
     * @param GradebookItem $item2
     * @return int
     */
    public function sort_by_weight($item1, $item2)
    {
        if ($item1->get_weight() == $item2->get_weight()) {
            return $this->sort_by_name($item1,$item2);
        } else {
            return ($item1->get_weight() < $item2->get_weight() ? -1 : 1);
        }
    }

    /**
     * @param GradebookItem $item1
     * @param GradebookItem $item2
     * @return int
     */
    public function sort_by_date($item1, $item2)
    {
        if (is_int($item1->get_date())) {
            $timestamp1 = $item1->get_date();
        } else {
            $date = $item1->get_date();
            if (!empty($date)) {
                $timestamp1 = api_strtotime($date, 'UTC');
            } else {
                $timestamp1 = null;
            }
        }

        if (is_int($item2->get_date())) {
            $timestamp2 = $item2->get_date();
        } else {
            $timestamp2 = api_strtotime($item2->get_date(), 'UTC');
        }

        if ($timestamp1 == $timestamp2) {
            return $this->sort_by_name($item1,$item2);
        } else {
            return ($timestamp1 < $timestamp2 ? -1 : 1);
        }
    }
}
