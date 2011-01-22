<?php // $Id: document.php 16494 2008-10-10 22:07:36Z yannoo $
/* For licensing terms, see /chamilo_license.txt */

/**
*	File containing the MultipleAnswer class.
*	@package dokeos.exercise
* 	@author Eric Marguin
* 	@version $Id: admin.php 10680 2007-01-11 21:26:23Z pcool $
*/

if(!class_exists('MultipleAnswerCombinationTrueFalse')):

/**
	CLASS MultipleAnswer
 *
 *	This class allows to instantiate an object of type MULTIPLE_ANSWER (MULTIPLE CHOICE, MULTIPLE ANSWER),
 *	extending the class question
 *
 *	@author Eric Marguin
 *	@package dokeos.exercise
 **/
require 'multiple_answer_combination.class.php';

class MultipleAnswerCombinationTrueFalse extends MultipleAnswerCombination {

	static $typePicture = 'mcmaco.gif';
	static $explanationLangVar = 'MultipleAnswerCombinationTrueFalse';
    var    $options; 

	/**
	 * Constructor
	 */
	function MultipleAnswerCombinationTrueFalse(){
		parent::question();
		$this -> type = MULTIPLE_ANSWER_COMBINATION_TRUE_FALSE;
		$this -> isContent = $this-> getIsContent();
        $this->options = array('1'=>get_lang('True'),'0' =>get_lang('False'), '2' =>get_lang('DontKnow'));
	}
}
endif;