<?php
/* For licensing terms, see /license.txt */
/**
 * Glossay form class definition
 * @package chamilo.glossary
 */
/**
 * Init
 */
namespace Glossary;

use Chamilo;

/**
 * Form to edit/Create glossary entries.
 *
 * @license /licence.txt
 * @author Laurent Opprecht <laurent@opprecht.info>
 */
class GlossaryForm extends \FormValidator
{

    /**
     *
     * @param string $action
     * @param \Glossary\Glossary $item
     * @return \Glossary\GlossaryForm
     */
    static function create($action, $item = null)
    {
        $result = new self('glossary', 'post', $action);
        if ($item) {
            $result->init($item);
        }
        return $result;
    }

    protected $glossary;

    function __construct($form_name = 'glossary', $method = 'post', $action = '', $target = '', $attributes = null, $track_submit = true)
    {
        parent::__construct($form_name, $method, $action, $target, $attributes, $track_submit);
    }

    /**
     *
     * @return \Glossary\Glossary
     */
    public function get_glossary()
    {
        return $this->glossary;
    }

    public function set_glossary($value)
    {
        $this->glossary = $value;
    }

    /**
     *
     * @param \Glossary\Glossary $glossary
     */
    function init($glossary = null)
    {
        $this->set_glossary($glossary);

        $defaults = array();
        $defaults['name'] = $glossary->name;
        $defaults['description'] = $glossary->description;

        $this->addHidden('c_id', $glossary->c_id);
        $this->addHidden('id', $glossary->id);
        $this->addHidden('session_id', $glossary->session_id);
        $this->addHidden(Request::PARAM_SEC_TOKEN, Access::instance()->get_token());

        $form_name = $glossary->id ? get_lang('TermEdit') : get_lang('TermAddNew');
        $this->addHeader($form_name);

        $this->addText('name', get_lang('TermName'), $required = true, array('class' => 'span3'));
        $this->addHtmlEditor('description', get_lang('TermDefinition'), true, array('ToolbarSet' => 'Glossary', 'Height' => '300'));
        $this->addButton('save', get_lang('Save'));

        $this->setDefaults($defaults);
    }

    function update_model()
    {
        $values = $this->exportValues();
        $glossary = $this->get_glossary();
        $glossary->name = $values['name'];
        $glossary->description = $values['description'];
    }

    function validate()
    {
        $result = parent::validate();
        if ($result) {
            $this->update_model();
        }
        return $result;
    }

}
