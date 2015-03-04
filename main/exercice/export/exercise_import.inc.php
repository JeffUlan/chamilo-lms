<?php
/**
 * @copyright (c) 2001-2006 Universite catholique de Louvain (UCL)
 *
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 *
 * @package chamilo.exercise
 *
 * @author claro team <cvs@claroline.net>
 * @author Guillaume Lederer <guillaume@claroline.net>
 */

/**
 * function to create a temporary directory (SAME AS IN MODULE ADMIN)
 */
function tempdir($dir, $prefix = 'tmp', $mode = 0777)
{
    if (substr($dir, -1) != '/') {
        $dir .= '/';
    }

    do {
        $path = $dir . $prefix . mt_rand(0, 9999999);
    } while (!mkdir($path, $mode));

    return $path;
}

/**
 * Unzip the exercise in the temp folder
 * @param string The path of the temporary directory where the exercise was uploaded and unzipped
 * @param string
 * @return bool
 */
function get_and_unzip_uploaded_exercise($baseWorkDir, $uploadPath)
{
    $_course = api_get_course_info();
    $_user = api_get_user_info();

    //Check if the file is valid (not to big and exists)
    if (!isset($_FILES['userFile']) || !is_uploaded_file($_FILES['userFile']['tmp_name'])) {
        // upload failed
        return false;
    }

    if (preg_match('/.zip$/i', $_FILES['userFile']['name']) &&
        handle_uploaded_document(
            $_course,
            $_FILES['userFile'],
            $baseWorkDir,
            $uploadPath,
            $_user['user_id'],
            0,
            null,
            1
        )
    ) {
        return true;
    }
    return false;
}

/**
 * Imports an exercise in QTI format if the XML structure can be found in it
 * @param array $file
 * @return an array as a backlog of what was really imported, and error or debug messages to display
 */
function import_exercise($file)
{
    global $exercise_info;
    global $element_pile;
    global $non_HTML_tag_to_avoid;
    global $record_item_body;
    // used to specify the question directory where files could be found in relation in any question
    global $questionTempDir;

    $archive_path = api_get_path(SYS_ARCHIVE_PATH) . 'qti2';
    $baseWorkDir = $archive_path;

    if (!is_dir($baseWorkDir)) {
        mkdir($baseWorkDir, api_get_permissions_for_new_directories(), true);
    }

    $uploadPath = '/';

    // set some default values for the new exercise
    $exercise_info = array();
    $exercise_info['name'] = preg_replace('/.zip$/i', '', $file);
    $exercise_info['question'] = array();
    $element_pile = array();

    // create parser and array to retrieve info from manifest
    $element_pile = array(); //pile to known the depth in which we are
    //$module_info = array (); //array to store the info we need

    // if file is not a .zip, then we cancel all

    if (!preg_match('/.zip$/i', $file)) {

        return 'UplZipCorrupt';
    }

    // unzip the uploaded file in a tmp directory
    if (!get_and_unzip_uploaded_exercise($baseWorkDir, $uploadPath)) {

        return 'UplZipCorrupt';
    }

    // find the different manifests for each question and parse them.

    $exerciseHandle = opendir($baseWorkDir);
    //$question_number = 0;
    $file_found = false;
    $operation = false;
    $result = false;
    $filePath = null;

    // parse every subdirectory to search xml question files
    while (false !== ($file = readdir($exerciseHandle))) {

        if (is_dir($baseWorkDir . '/' . $file) && $file != "." && $file != "..") {

            // Find each manifest for each question repository found
            $questionHandle = opendir($baseWorkDir . '/' . $file);
            while (false !== ($questionFile = readdir($questionHandle))) {
                if (preg_match('/.xml$/i', $questionFile)) {
                    $result = parse_file($baseWorkDir, $file, $questionFile);
                    $filePath = $baseWorkDir . $file;
                    $file_found = true;
                }
            }
        } elseif (preg_match('/.xml$/i', $file)) {

            // Else ignore file
            $result = parse_file($baseWorkDir, '', $file);
            $filePath = $baseWorkDir . '/' . $file;
            $file_found = true;
        }
    }

    if (!$file_found) {

        return 'No XML file found in the zip';
    }

    if ($result == false) {
        return false;
    }

    $doc = new DOMDocument();
    $doc->load($filePath);
    $encoding = $doc->encoding;

    // 1. Create exercise.
    $exercise = new Exercise();
    $exercise->exercise = $exercise_info['name'];

    $exercise->save();
    $last_exercise_id = $exercise->selectId();
    if (!empty($last_exercise_id)) {
        // For each question found...
        foreach ($exercise_info['question'] as $question_array) {
            //2. Create question
            $question = new Ims2Question();
            $question->type = $question_array['type'];
            $question->setAnswer();
            $question->updateTitle(formatText($question_array['title']));
            //$question->updateDescription($question_array['title']);
            $type = $question->selectType();
            $question->type = constant($type);
            $question->save($last_exercise_id);
            $last_question_id = $question->selectId();
            //3. Create answer
            $answer = new Answer($last_question_id);
            $answer->new_nbrAnswers = count($question_array['answer']);
            $totalCorrectWeight = 0;
            foreach ($question_array['answer'] as $key => $answers) {
                $split = explode('_', $key);
                $i = $split[1];
                // Answer
                $answer->new_answer[$i] = formatText($answers['value']);
                // Comment
                $answer->new_comment[$i] = isset($answers['feedback']) ? formatText($answers['feedback']) : null;
                // Position
                $answer->new_position[$i] = $i;
                // Correct answers
                if (in_array($key, $question_array['correct_answers'])) {
                    $answer->new_correct[$i] = 1;
                } else {
                    $answer->new_correct[$i] = 0;
                }
                $answer->new_weighting[$i] = $question_array['weighting'][$key];
                if ($answer->new_correct[$i]) {
                    $totalCorrectWeight = $answer->new_weighting[$i];
                }
            }
            $question->updateWeighting($totalCorrectWeight);
            $question->save($last_exercise_id);
            $answer->save();
        }

        // delete the temp dir where the exercise was unzipped
        my_delete($baseWorkDir . $uploadPath);
        return $last_exercise_id;
    }

    return false;
}

/**
 * We assume the file charset is UTF8
 **/
function formatText($text)
{
    return api_html_entity_decode($text);
}

/**
 * Parses a given XML file and fills global arrays with the elements
 * @param $exercisePath
 * @param $file
 * @param $questionFile
 * @return bool
 */
function parse_file($exercisePath, $file, $questionFile)
{
    global $non_HTML_tag_to_avoid;
    global $record_item_body;
    global $questionTempDir;

    $questionTempDir = $exercisePath . '/' . $file . '/';
    $questionFilePath = $questionTempDir . $questionFile;

    if (!($fp = fopen($questionFilePath, 'r'))) {
        Display:: display_error_message(get_lang('Error opening question\'s XML file'));
        return false;
    } else {
        $data = fread($fp, filesize($questionFilePath));
    }

    //parse XML question file
    $data = str_replace(array('<p>', '</p>', '<front>', '</front>'), '', $data);

    //used global variable start values declaration :

    $record_item_body = false;
    $non_HTML_tag_to_avoid = array(
        "SIMPLECHOICE",
        "CHOICEINTERACTION",
        "INLINECHOICEINTERACTION",
        "INLINECHOICE",
        "SIMPLEMATCHSET",
        "SIMPLEASSOCIABLECHOICE",
        "TEXTENTRYINTERACTION",
        "FEEDBACKINLINE",
        "MATCHINTERACTION",
        "ITEMBODY",
        "BR",
        "IMG"
    );

    $question_format_supported = true;

    $xml_parser = xml_parser_create();
    xml_parser_set_option($xml_parser, XML_OPTION_SKIP_WHITE, false);
    xml_set_element_handler($xml_parser, 'startElement', 'endElement');
    xml_set_character_data_handler($xml_parser, 'elementData');
    if (!xml_parse($xml_parser, $data, feof($fp))) {
        // if reading of the xml file in not successful :
        // set errorFound, set error msg, break while statement
        Display:: display_error_message(get_lang('Error reading XML file'));
        return false;
    }

    //close file
    fclose($fp);
    if (!$question_format_supported) {
        Display:: display_error_message(
            get_lang(
                'Unknown question format in file %file',
                array(
                '%file' => $questionFile
                )
            )
        );
        return false;
    }
    return true;
}

/**
 * Function used by the SAX xml parser when the parser meets a opening tag
 *
 * @param object $parser xml parser created with "xml_parser_create()"
 * @param string $name name of the element
 * @param array $attributes
 */
function startElement($parser, $name, $attributes)
{
    global $element_pile;
    global $exercise_info;
    global $current_question_ident;
    global $current_answer_id;
    global $current_match_set;
    global $currentAssociableChoice;
    global $current_question_item_body;
    global $record_item_body;
    global $non_HTML_tag_to_avoid;
    global $current_inlinechoice_id;
    global $cardinality;
    global $questionTempDir;

    array_push($element_pile, $name);
    $current_element = end($element_pile);
    if (sizeof($element_pile) >= 2) {
        $parent_element = $element_pile[sizeof($element_pile) - 2];
    } else {
        $parent_element = "";
    }
    if (sizeof($element_pile) >= 3) {
        $grant_parent_element = $element_pile[sizeof($element_pile) - 3];
    } else {
        $grant_parent_element = "";
    }

    if ($record_item_body) {

        if ((!in_array($current_element, $non_HTML_tag_to_avoid))) {
            $current_question_item_body .= "<" . $name;

            foreach ($attributes as $attribute_name => $attribute_value) {
                $current_question_item_body .= " " . $attribute_name . "=\"" . $attribute_value . "\"";
            }
            $current_question_item_body .= ">";
        } else {
            //in case of FIB question, we replace the IMS-QTI tag b y the correct answer between "[" "]",
            //we first save with claroline tags ,then when the answer will be parsed, the claroline tags will be replaced

            if ($current_element == 'INLINECHOICEINTERACTION') {

                $current_question_item_body .= "**claroline_start**" . $attributes['RESPONSEIDENTIFIER'] . "**claroline_end**";
            }
            if ($current_element == 'TEXTENTRYINTERACTION') {
                $correct_answer_value = $exercise_info['question'][$current_question_ident]['correct_answers'][$current_answer_id];
                $current_question_item_body .= "[" . $correct_answer_value . "]";

            }
            if ($current_element == 'BR') {
                $current_question_item_body .= "<br />";
            }
        }
    }
    switch ($current_element) {
        case 'ASSESSMENTITEM':
            //retrieve current question
            $current_question_ident = $attributes['IDENTIFIER'];
            $exercise_info['question'][$current_question_ident] = array();
            $exercise_info['question'][$current_question_ident]['answer'] = array();
            $exercise_info['question'][$current_question_ident]['correct_answers'] = array();
            $exercise_info['question'][$current_question_ident]['title'] = $attributes['TITLE'];
            $exercise_info['question'][$current_question_ident]['tempdir'] = $questionTempDir;
            break;
        case 'SECTION':
            //retrieve exercise name
            $exercise_info['name'] = $attributes['TITLE'];
            break;
        case 'RESPONSEDECLARATION':
            // Retrieve question type
            if ("multiple" == $attributes['CARDINALITY']) {
                $exercise_info['question'][$current_question_ident]['type'] = 'MCMA';
                $cardinality = 'multiple';
            }
            if ("single" == $attributes['CARDINALITY']) {
                $exercise_info['question'][$current_question_ident]['type'] = 'MCUA';
                $cardinality = 'single';
            }
            //needed for FIB
            $current_answer_id = $attributes['IDENTIFIER'];
            break;
        case 'INLINECHOICEINTERACTION':
            $exercise_info['question'][$current_question_ident]['type'] = 'FIB';
            $exercise_info['question'][$current_question_ident]['subtype'] = 'LISTBOX_FILL';
            $current_answer_id = $attributes['RESPONSEIDENTIFIER'];
            break;
        case 'INLINECHOICE':
            $current_inlinechoice_id = $attributes['IDENTIFIER'];
            break;
        case 'TEXTENTRYINTERACTION':
            $exercise_info['question'][$current_question_ident]['type'] = 'FIB';
            $exercise_info['question'][$current_question_ident]['subtype'] = 'TEXTFIELD_FILL';
            $exercise_info['question'][$current_question_ident]['response_text'] = $current_question_item_body;
            //replace claroline tags
            break;
        case 'MATCHINTERACTION':
            $exercise_info['question'][$current_question_ident]['type'] = 'MATCHING';
            break;
        case 'SIMPLEMATCHSET':
            if (!isset ($current_match_set)) {
                $current_match_set = 1;
            } else {
                $current_match_set++;
            }
            $exercise_info['question'][$current_question_ident]['answer'][$current_match_set] = array();
            break;
        case 'SIMPLEASSOCIABLECHOICE':
            $currentAssociableChoice = $attributes['IDENTIFIER'];
            break;
        //retrieve answers id for MCUA and MCMA questions
        case 'SIMPLECHOICE':
            $current_answer_id = $attributes['IDENTIFIER'];
            if (!isset($exercise_info['question'][$current_question_ident]['answer'][$current_answer_id])) {
                $exercise_info['question'][$current_question_ident]['answer'][$current_answer_id] = array();
            }
            break;
        case 'MAPENTRY':
            if ($parent_element == "MAPPING") {
                $answer_id = $attributes['MAPKEY'];

                if (!isset ($exercise_info['question'][$current_question_ident]['weighting'])) {
                    $exercise_info['question'][$current_question_ident]['weighting'] = array();
                }
                $exercise_info['question'][$current_question_ident]['weighting'][$answer_id] = $attributes['MAPPEDVALUE'];
            }
            break;
        case 'MAPPING':
            if (isset ($attributes['DEFAULTVALUE'])) {
                $exercise_info['question'][$current_question_ident]['default_weighting'] = $attributes['DEFAULTVALUE'];
            }
        case 'ITEMBODY':
            $record_item_body = true;
            $current_question_item_body = '';
            break;
        case 'IMG':
            $exercise_info['question'][$current_question_ident]['attached_file_url'] = $attributes['SRC'];
            break;
    }
}

/**
 * Function used by the SAX xml parser when the parser meets a closing tag
 *
 * @param $parser xml parser created with "xml_parser_create()"
 * @param $name name of the element
 */
function endElement($parser, $name)
{
    global $element_pile;
    global $exercise_info;
    global $current_question_ident;
    global $record_item_body;
    global $current_question_item_body;
    global $non_HTML_tag_to_avoid;
    global $cardinality;

    $current_element = end($element_pile);

    //treat the record of the full content of itembody tag :

    if ($record_item_body && (!in_array($current_element, $non_HTML_tag_to_avoid))) {
        $current_question_item_body .= "</" . $name . ">";
    }

    switch ($name) {
        case 'ITEMBODY':
            $record_item_body = false;
            if ($exercise_info['question'][$current_question_ident]['type'] == 'FIB') {
                $exercise_info['question'][$current_question_ident]['response_text'] = $current_question_item_body;
            } else {
                $exercise_info['question'][$current_question_ident]['statement'] = $current_question_item_body;
            }
            break;
    }
    array_pop($element_pile);

}

/**
 * @param $parser
 * @param $data
 */
function elementData($parser, $data)
{
    global $element_pile;
    global $exercise_info;
    global $current_question_ident;
    global $current_answer_id;
    global $current_match_set;
    global $currentAssociableChoice;
    global $current_question_item_body;
    global $record_item_body;
    global $non_HTML_tag_to_avoid;
    global $current_inlinechoice_id;
    global $cardinality;

    $current_element = end($element_pile);
    if (sizeof($element_pile) >= 2) {
        $parent_element = $element_pile[sizeof($element_pile) - 2];
    } else {
        $parent_element = "";
    }
    if (sizeof($element_pile) >= 3) {
        $grant_parent_element = $element_pile[sizeof($element_pile) - 3];
    } else {
        $grant_parent_element = "";
    }

    //treat the record of the full content of itembody tag (needed for question statment and/or FIB text:

    if ($record_item_body && (!in_array($current_element, $non_HTML_tag_to_avoid))) {
        $current_question_item_body .= $data;
    }

    switch ($current_element) {
        case 'SIMPLECHOICE':
            if (!isset ($exercise_info['question'][$current_question_ident]['answer'][$current_answer_id]['value'])) {
                $exercise_info['question'][$current_question_ident]['answer'][$current_answer_id]['value'] = trim($data);
            } else {
                $exercise_info['question'][$current_question_ident]['answer'][$current_answer_id]['value'] .= '' . trim($data);
            }
            break;
        case 'FEEDBACKINLINE':
            if (!isset ($exercise_info['question'][$current_question_ident]['answer'][$current_answer_id]['feedback'])) {
                $exercise_info['question'][$current_question_ident]['answer'][$current_answer_id]['feedback'] = trim($data);
            } else {
                $exercise_info['question'][$current_question_ident]['answer'][$current_answer_id]['feedback'] .= ' ' . trim($data);
            }
            break;
        case 'SIMPLEASSOCIABLECHOICE':
            $exercise_info['question'][$current_question_ident]['answer'][$current_match_set][$currentAssociableChoice] = trim($data);
            break;
        case 'VALUE':
            if ($parent_element == "CORRECTRESPONSE") {
                if ($cardinality == "single") {
                    $exercise_info['question'][$current_question_ident]['correct_answers'][$current_answer_id] = $data;
                } else {
                    $exercise_info['question'][$current_question_ident]['correct_answers'][] = $data;
                }
            }
            break;

        case 'ITEMBODY':
            $current_question_item_body .= $data;
            break;
        case 'INLINECHOICE':
            // if this is the right answer, then we must replace the claroline tags in the FIB text bye the answer between "[" and "]" :
            $answer_identifier = $exercise_info['question'][$current_question_ident]['correct_answers'][$current_answer_id];
            if ($current_inlinechoice_id == $answer_identifier) {
                $current_question_item_body = str_replace(
                    "**claroline_start**" . $current_answer_id . "**claroline_end**",
                    "[" . $data . "]",
                    $current_question_item_body
                );
            } else {
                if (!isset ($exercise_info['question'][$current_question_ident]['wrong_answers'])) {
                    $exercise_info['question'][$current_question_ident]['wrong_answers'] = array();
                }
                $exercise_info['question'][$current_question_ident]['wrong_answers'][] = $data;
            }
            break;
    }
}
