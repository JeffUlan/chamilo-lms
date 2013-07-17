<?php

namespace ChamiloLMS\Component\Editor;

class Editor
{
    /**
     * Name of the instance.
     *
     * @access protected
     * @var string
     */
    public $InstanceName;

    /**
     * Width of the editor.
     * Examples: 100%, 600
     *
     * @var mixed
     */
    public $Width;
    /**
     * Height of the editor.
     * Examples: 400, 50%
     *
     * @var mixed
     */
    public $Height;
    /**
     * Name of the toolbar to load.
     *
     * @var string
     */
    public $ToolbarSet;
    /**
     * Initial value.
     *
     * @var string
     */
    public $Value;
    /**
     * This is where additional configuration can be passed.
     * Example:
     * $oeditor->Config['EnterMode'] = 'br';
     *
     * @var array
     */
    public $Config;

    /** @var \Symfony\Component\Translation\Translator */
    public $translator;

    /**
     * Main Constructor.
     * Refer to the _samples/php directory for examples.
     *
     * @param string $instanceName
     */
    public function __construct($instanceName, \Symfony\Component\Translation\Translator $translator)
    {
        $this->InstanceName = $instanceName;
        $this->Width        = '100%';
        $this->Height       = '200';
        $this->ToolbarSet   = 'Basic';
        $this->Value        = '';
        $this->Config       = array();
        $this->translator = $translator;
    }

    /**
     * Display editor.
     *
     */
    public function Create()
    {
        echo $this->CreateHtml();
    }

    /**
     * Return the HTML code required to run editor.
     *
     * @return string
     */
    public function CreateHtml()
    {
        $Html = '<textarea id="'.$this->InstanceName.'" name="'.$this->InstanceName.'" class="ckeditor" >'.$this->Value.'</textarea>';
        $Html .= $this->ckeditorReplace();

        return $Html;
    }

    public function ckeditorReplace()
    {
        $toolbar  = new Toolbar\Basic($this->ToolbarSet);
        $toolbar->setLanguage($this->translator->getLocale());
        $config   = $toolbar->getConfig();
        $js       = $this->to_js($config);
        $settings = null;

        $Html = "<script>
           CKEDITOR.replace('".$this->InstanceName."',
               $js
           );
           </script>";

        return $Html;
    }

    /**
     * Returns true if browser is compatible with FCKeditor.
     *
     * @return boolean
     */
    public static function IsCompatible()
    {
        return FCKeditor_IsCompatibleBrowser();
    }

    /**
     * Get settings from Config array as a single string.
     *
     * @access protected
     * @return string
     */
    public function GetConfigFieldString()
    {
        $sParams = '';
        $bFirst  = true;

        foreach ($this->Config as $sKey => $sValue) {
            if (!$bFirst) {
                $sParams .= '&amp;';
            } else {
                $bFirst = false;
            }
            if (is_string($sValue)) {
                $sParams .= $this->EncodeConfig($sKey).'='.$this->EncodeConfig($sValue);
            } else {
                $sParams .= $this->EncodeConfig($sKey).'='.$this->EncodeConfig($this->to_js($sValue));
            }
        }

        return $sParams;
    }

    /**
     * Encode characters that may break the configuration string
     * generated by GetConfigFieldString().
     *
     * @access protected
     * @param string $valueToEncode
     * @return string
     */
    public function EncodeConfig($valueToEncode)
    {
        $chars = array(
            '&' => '%26',
            '=' => '%3D',
            '"' => '%22',
            '%' => '%25'
        );

        return strtr($valueToEncode, $chars);
    }

    /**
     * Converts a PHP variable into its Javascript equivalent.
     * The code of this method has been "borrowed" from the funcion drupal_to_js() within the Drupal CMS.
     * @param mixed $var    The variable to be converted into Javascript syntax
     * @return string        Returns a string
     * Note: This function is similar to json_encode(), in addition it produces HTML-safe strings, i.e. with <, > and & escaped.
     * @link http://drupal.org/
     */
    private function to_js($var)
    {
        switch (gettype($var)) {
            case 'boolean':
                return $var ? 'true' : 'false'; // Lowercase necessary!
            case 'integer':
            case 'double':
                return (string)$var;
            case 'resource':
            case 'string':
                return '"'.str_replace(
                    array("\r", "\n", "<", ">", "&"),
                    array('\r', '\n', '\x3c', '\x3e', '\x26'),
                    addslashes($var)
                ).'"';
            case 'array':
                // Arrays in JSON can't be associative. If the array is empty or if it
                // has sequential whole number keys starting with 0, it's not associative
                // so we can go ahead and convert it as an array.
                if (empty($var) || array_keys($var) === range(0, sizeof($var) - 1)) {
                    $output = array();
                    foreach ($var as $v) {
                        $output[] = $this->to_js($v);
                    }

                    return '[ '.implode(', ', $output).' ]';
                }
            // Otherwise, fall through to convert the array as an object.
            case 'object':
                $output = array();
                foreach ($var as $k => $v) {
                    $output[] = $this->to_js(strval($k)).': '.$this->to_js($v);
                }
                return '{ '.implode(', ', $output).' }';
            default:
                return 'null';
        }
    }


    /**
     * This method determines editor's interface language and returns it as compatible with the editor langiage code.
     * @return array
     */
    private function getEditorLanguage()
    {
        static $config;
        if (!is_array($config)) {
            $code_translation_table         = array('' => 'en', 'sr' => 'sr-latn', 'zh' => 'zh-cn', 'zh-tw' => 'zh');
            $editor_lang                    = strtolower(str_replace('_', '-', api_get_language_isocode()));
            $editor_lang                    = isset($code_translation_table[$editor_lang]) ? $code_translation_table[$editor_lang] : $editor_lang;
            $editor_lang                    = file_exists(
                api_get_path(SYS_PATH).'main/inc/lib/fckeditor/editor/lang/'.$editor_lang.'.js'
            ) ? $editor_lang : 'en';
            $config['DefaultLanguage']      = $editor_lang;
            $config['ContentLangDirection'] = api_get_text_direction($editor_lang);
        }

        return $config;
    }
}
