<?php
/* For licensing terms, see /license.txt */

/**
 *  Class Matching
 *  Matching questions type class
 *
 *	This class allows to instantiate an object of type MULTIPLE_ANSWER (MULTIPLE CHOICE, MULTIPLE ANSWER),
 *	extending the class question
 *
 *	@author Eric Marguin
 *	@package chamilo.exercise
 */
class Matching extends Question
{
    static $typePicture = 'matching.png';
    static $explanationLangVar = 'Matching';

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->type = MATCHING;
        $this->isContent = $this-> getIsContent();
    }

    /**
     * function which redefines Question::createAnswersForm
     * @param FormValidator $form
     */
    public function createAnswersForm($form)
    {
        $defaults = array();
        $navigator_info = api_get_navigator();

        $nb_matches = $nb_options = 2;
        if ($form->isSubmitted()) {
            $nb_matches = $form->getSubmitValue('nb_matches');
            $nb_options = $form->getSubmitValue('nb_options');
            if (isset($_POST['lessMatches']))
                $nb_matches--;
            if (isset($_POST['moreMatches']))
                $nb_matches++;
            if (isset($_POST['lessOptions']))
                $nb_options--;
            if (isset($_POST['moreOptions']))
                $nb_options++;
        } else if (!empty($this->id)) {
            $answer = new Answer($this->id);
            $answer->read();
            if (count($answer->nbrAnswers) > 0) {
                $a_matches = $a_options = array();
                $nb_matches = $nb_options = 0;
                for ($i = 1; $i <= $answer->nbrAnswers; $i++) {
                    if ($answer->isCorrect($i)) {
                        $nb_matches++;
                        $defaults['answer[' . $nb_matches . ']'] = $answer->selectAnswer($i);
                        $defaults['weighting[' . $nb_matches . ']'] = float_format($answer->selectWeighting($i), 1);
                        $defaults['matches[' . $nb_matches . ']'] = $answer->correct[$i];
                    } else {
                        $nb_options++;
                        $defaults['option[' . $nb_options . ']'] = $answer->selectAnswer($i);
                    }
                }
            }
        } else {
            $defaults['answer[1]'] = get_lang('DefaultMakeCorrespond1');
            $defaults['answer[2]'] = get_lang('DefaultMakeCorrespond2');
            $defaults['matches[2]'] = '2';
            $defaults['option[1]'] = get_lang('DefaultMatchingOptA');
            $defaults['option[2]'] = get_lang('DefaultMatchingOptB');
        }
        $a_matches = array();
        for ($i = 1; $i <= $nb_options; ++$i) {
            $a_matches[$i] = chr(64 + $i);  // fill the array with A, B, C.....
        }

        $form->addElement('hidden', 'nb_matches', $nb_matches);
        $form->addElement('hidden', 'nb_options', $nb_options);

        // DISPLAY MATCHES
        $html = '<table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th width="10">' . get_lang('Number') . '</th>
                    <th width="85%">' . get_lang('Answer') . '</th>
                    <th width="15%">' . get_lang('MatchesTo') . '</th>
                    <th width="10">' . get_lang('Weighting') . '</th>
                </tr>
            </thead>
            <tbody>';

        $form->addHeader(get_lang('MakeCorrespond'));
        $form->addHtml($html);

        if ($nb_matches < 1) {
            $nb_matches = 1;
            Display::display_normal_message(get_lang('YouHaveToCreateAtLeastOneAnswer'));
        }

        for ($i = 1; $i <= $nb_matches; ++$i) {
            $renderer = &$form->defaultRenderer();

            $renderer->setElementTemplate(
                '<td><!-- BEGIN error --><span class="form_error">{error}</span><!-- END error -->{element}</td>',
                "answer[$i]"
            );
            $renderer->setElementTemplate(
                '<td><!-- BEGIN error --><span class="form_error">{error}</span><!-- END error -->{element}</td>',
                "matches[$i]"
            );
            $renderer->setElementTemplate(
                '<td><!-- BEGIN error --><span class="form_error">{error}</span><!-- END error -->{element}</td>',
                "weighting[$i]"
            );

            $form->addHtml('<tr>');

            $form->addHtml("<td>$i</td>");
            $form->addText("answer[$i]", null);
            $form->addSelect("matches[$i]", null, $a_matches);
            $form->addText("weighting[$i]", null, true, ['value' => 10]);

            $form->addHtml('</tr>');
        }

        $form->addHtml('</tbody></table>');
        $group = array();

        if ($navigator_info['name'] == 'Internet Explorer' && $navigator_info['version'] == '6') {
            $group[] = $form->createElement('submit', 'lessMatches', get_lang('DelElem'), 'class="btn btn-default"');
            $group[] = $form->createElement('submit', 'moreMatches', get_lang('AddElem'), 'class="btn btn-default"');
        } else {
            $renderer->setElementTemplate('<div class="form-group"><div class="col-sm-offset-2">{element}', 'lessMatches');
            $renderer->setElementTemplate('{element}</div></div>', 'moreMatches');

            $group[] = $form->addButtonDelete(get_lang('DelElem'), 'lessMatches', true);
            $group[] = $form->addButtonCreate(get_lang('AddElem'), 'moreMatches', true);
        }

        $form->addGroup($group);

        // DISPLAY OPTIONS
        $html = '<table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th width="15%">' . get_lang('Number') . '</th>
                    <th width="85%">' . get_lang('Answer') . '</th>
                </tr>
            </thead>
            <tbody>';

        $form->addHtml($html);

        if ($nb_options < 1) {
            $nb_options = 1;
            Display::display_normal_message(get_lang('YouHaveToCreateAtLeastOneAnswer'));
        }

        for ($i = 1; $i <= $nb_options; ++$i) {
            $renderer = &$form->defaultRenderer();

            $renderer->setElementTemplate(
                '<td><!-- BEGIN error --><span class="form_error">{error}</span><!-- END error -->{element}</td>',
                "option[$i]"
            );

            $form->addHtml('<tr>');

            $form->addHtml('<td>' . chr(64 + $i) . '</td>');
            $form->addText("option[$i]", null);

            $form->addHtml('</tr>');
        }

        $form->addHtml('</table>');
        $group = array();
        global $text, $class;

        if ($navigator_info['name'] == 'Internet Explorer' && $navigator_info['version'] == '6') {
            // setting the save button here and not in the question class.php
            $group[] = $form->createElement('submit', 'submitQuestion', $text, 'class="' . $class . '"');
            $group[] = $form->createElement('submit', 'lessOptions', get_lang('DelElem'), 'class="minus"');
            $group[] = $form->createElement('submit', 'moreOptions', get_lang('AddElem'), 'class="plus"');
        } else {
            // setting the save button here and not in the question class.php
            $group[] = $form->addButtonDelete(get_lang('DelElem'), 'lessOptions', true);
            $group[] = $form->addButtonCreate(get_lang('AddElem'), 'moreOptions', true);
            $group[] = $form->addButtonSave($text, 'submitQuestion', true);
        }

        $form->addGroup($group);

        if (!empty($this->id)) {
            $form->setDefaults($defaults);
        } else {
            if ($this->isContent == 1) {
                $form->setDefaults($defaults);
            }
        }

        $form->setConstants(array('nb_matches' => $nb_matches, 'nb_options' => $nb_options));
    }


    /**
     * abstract function which creates the form to create / edit the answers of the question
     * @param FormValidator $form
     */
    public function processAnswersCreation($form)
    {
        $nb_matches = $form -> getSubmitValue('nb_matches');
        $nb_options = $form -> getSubmitValue('nb_options');
        $this -> weighting = 0;
        $objAnswer = new Answer($this->id);

        $position = 0;

        // insert the options
        for($i=1 ; $i<=$nb_options; ++$i) {
            $position++;
            $option = $form -> getSubmitValue('option['.$i.']');
            $objAnswer->createAnswer($option, 0, '', 0, $position);
        }

        // insert the answers
        for($i=1 ; $i<=$nb_matches ; ++$i) {
            $position++;
            $answer = $form -> getSubmitValue('answer['.$i.']');
            $matches = $form -> getSubmitValue('matches['.$i.']');
            $weighting = $form -> getSubmitValue('weighting['.$i.']');
            $this -> weighting += $weighting;
            $objAnswer->createAnswer($answer,$matches,'',$weighting,$position);
        }
        $objAnswer->save();
        $this->save();
    }

    /**
     * @param null $feedback_type
     * @param null $counter
     * @param null $score
     * @return string
     */
    public function return_header($feedback_type = null, $counter = null, $score = null)
    {
        $header = parent::return_header($feedback_type, $counter, $score);
        $header .= '<table class="'.$this->question_table_class .'">';
        $header .= '<tr>
                <th>'.get_lang('ElementList').'</th>
                <th>'.get_lang('CorrespondsTo').'</th>
              </tr>';
        return $header;
    }
}
