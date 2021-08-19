<?php

declare(strict_types=1);

/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Component\Editor\CkEditor\Toolbar;

use Chamilo\CoreBundle\Component\Editor\Toolbar;

class Basic extends Toolbar
{
    /**
     * Default plugins that will be use in all toolbars
     * In order to add a new plugin you have to load it in default/layout/head.tpl.
     */
    public array $defaultPlugins = [
        //'adobeair',
        //'ajax',
        'audio',
        'image2_chamilo',
        'bidi',
        'colorbutton',
        'colordialog',
        'dialogui',
        'dialogadvtab',
        'div',
        //if you activate this plugin the html, head tags will not be saved
        //'divarea',
        //'docprops',
        'find',
        'flash',
        'font',
        'iframe',
        //'iframedialog',
        'indentblock',
        'justify',
        'language',
        'lineutils',
        'liststyle',
        'newpage',
        'oembed',
        'pagebreak',
        'preview',
        'print',
        'save',
        'selectall',
        //'sharedspace',
        'showblocks',
        'smiley',
        //'sourcedialog',
        //'stylesheetparser',
        //'tableresize',
        'templates',
        //'uicolor',
        'video',
        'widget',
        'wikilink',
        'wordcount',
        'inserthtml',
        //'xml',
        'qmarkersrolls',
    ];

    /**
     * Plugins this toolbar.
     */
    public array $plugins = [];
    private string $toolbarSet;

    public function __construct(
        $router,
        $toolbar = null,
        $config = [],
        $prefix = null
    ) {
        $isAllowedToEdit = api_is_allowed_to_edit();
        $isPlatformAdmin = api_is_platform_admin();
        // Adding plugins depending of platform conditions
        $plugins = [];

        if ('ismanual' === api_get_setting('show_glossary_in_documents')) {
            $plugins[] = 'glossary';
        }

        if ('true' === api_get_setting('youtube_for_students')) {
            $plugins[] = 'youtube';
        } else {
            if (api_is_allowed_to_edit() || api_is_platform_admin()) {
                $plugins[] = 'youtube';
            }
        }

        if ('true' === api_get_setting('enabled_googlemaps')) {
            $plugins[] = 'leaflet';
        }

        if ('true' === api_get_setting('math_asciimathML')) {
            $plugins[] = 'asciimath';
        }

        if ('true' === api_get_setting('enabled_mathjax')) {
            $plugins[] = 'mathjax';
            $config['mathJaxLib'] = api_get_path(WEB_PUBLIC_PATH).'assets/MathJax/MathJax.js?config=TeX-MML-AM_HTMLorMML';
        }

        if ('true' === api_get_setting('enabled_asciisvg')) {
            $plugins[] = 'asciisvg';
        }

        if ('true' === api_get_setting('enabled_wiris')) {
            // Commercial plugin
            $plugins[] = 'ckeditor_wiris';
        }

        if ('true' === api_get_setting('enabled_imgmap')) {
            $plugins[] = 'mapping';
        }

        /*if (api_get_setting('block_copy_paste_for_students') == 'true') {
            // Missing
        }*/

        if ('true' === api_get_setting('more_buttons_maximized_mode')) {
            $plugins[] = 'toolbarswitch';
        }

        if ('true' === api_get_setting('allow_spellcheck')) {
            $plugins[] = 'scayt';
        }

        if (api_get_configuration_sub_value('ckeditor_vimeo_embed/config') && ($isAllowedToEdit || $isPlatformAdmin)) {
            $plugins[] = 'ckeditor_vimeo_embed';
        }

        if (api_get_configuration_value('ck_editor_block_image_copy_paste')) {
            $plugins[] = 'blockimagepaste';
        }
        $this->defaultPlugins = array_unique(array_merge($this->defaultPlugins, $plugins));
        $this->toolbarSet = $toolbar;
        parent::__construct($router, $toolbar, $config, $prefix);
    }

    /**
     * Get the toolbar config.
     *
     * @return array
     */
    public function getConfig()
    {
        $config = [];
        /*if ('true' === api_get_setting('more_buttons_maximized_mode')) {
            $config['toolbar_minToolbar'] = $this->getMinimizedToolbar();
            $config['toolbar_maxToolbar'] = $this->getMaximizedToolbar();
        }

        $config['customConfig'] = api_get_path(WEB_PUBLIC_PATH).'editor/config?'.api_get_cidreq().'&tool=document&type=files';*/
        //$config['flash_flvPlayer'] = api_get_path(WEB_LIBRARY_JS_PATH).'ckeditor/plugins/flash/swf/player.swf';

        /*filebrowserFlashBrowseUrl
        filebrowserFlashUploadUrl
        filebrowserImageBrowseLinkUrl
        filebrowserImageBrowseUrl
        filebrowserImageUploadUrl
        filebrowserUploadUrl*/

        //$config['extraPlugins'] = $this->getPluginsToString();

        //$config['oembed_maxWidth'] = '560';
        //$config['oembed_maxHeight'] = '315';

        /*$config['wordcount'] = array(
            // Whether or not you want to show the Word Count
            'showWordCount' => true,
            // Whether or not you want to show the Char Count
            'showCharCount' => true,
            // Option to limit the characters in the Editor
            'charLimit' => 'unlimited',
            // Option to limit the words in the Editor
            'wordLimit' => 'unlimited'
        );*/

        /*$config['skin'] = 'moono-lisa';
        $config['image2_chamilo_alignClasses'] = [
            'pull-left',
            'text-center',
            'pull-right',
            'img-va-baseline',
            'img-va-top',
            'img-va-bottom',
            'img-va-middle',
            'img-va-super',
            'img-va-sub',
            'img-va-text-top',
            'img-va-text-bottom',
        ];
        $config['startupOutlineBlocks'] = true === api_get_configuration_value('ckeditor_startup_outline_blocks');*/

        $customPlugins = '';
        $customPluginsPath = [];
        if ('true' === api_get_setting('editor.translate_html')) {
            $customPlugins .= ' translatehtml';
            $customPluginsPath['translatehtml'] = api_get_path(WEB_PUBLIC_PATH).'libs/editor/tinymce_plugins/translatehtml/plugin.js';

            $languageList = api_get_languages();
            $rtlIsocodes = ['ps', 'ar', 'he', 'fa'];
            $list = [];
            foreach ($languageList as $isocode => $name) {
                // Example format language list : ['ar:Arabic:rtl', 'fr:French', 'es:Spanish'];
                $rtl = (in_array($isocode, $rtlIsocodes)?':rtl':'');
                $list[] = $isocode.':'.$name.$rtl;
            }
            $config['translatehtml_lenguage_list'] = $list;
        }

        $plugins = [
            'advlist autolink lists link image charmap print preview anchor',
            'searchreplace visualblocks code fullscreen',
            'insertdatetime media table paste wordcount'.$customPlugins,
        ];

        /*plugins: [
           'fullpage advlist autolink lists link image charmap print preview anchor',
           'searchreplace visualblocks code fullscreen',
           'insertdatetime media table paste wordcount emoticons'
       ],*/

        if ($this->getConfigAttribute('fullPage')) {
            $plugins[] = 'fullpage';
        }

        $config['plugins'] = implode(' ', $plugins);
        $config['toolbar'] = 'undo redo directionality | bold italic underline strikethrough | insertfile image media template link | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | fullscreen  preview save print | code codesample | ltr rtl'.$customPlugins;

        if (!empty($customPluginsPath)) {
            $config['external_plugins'] = $customPluginsPath;
        }

        //$config['skin'] = 'oxide';
        $config['skin_url'] = '/build/libs/tinymce/skins/ui/oxide';
        $config['content_css'] = '/build/libs/tinymce/skins/content/default/content.css';
        $config['branding'] = false;
        $config['relative_urls'] = false;
        $config['toolbar_mode'] = 'sliding';
        $config['autosave_ask_before_unload'] = true;
        $config['toolbar_mode'] = 'sliding';

        //file_picker_callback : browser,

        $iso = api_get_language_isocode();
        $url = api_get_path(WEB_PATH);

        // Language list: https://www.tiny.cloud/get-tiny/language-packages/
        if ('en_US' !== $iso) {
            $config['language'] = $iso;
            $config['language_url'] = "$url/libs/editor/langs/$iso.js";
        }



        /*if (isset($this->config)) {
            $this->config = array_merge($config, $this->config);
        } else {
            $this->config = $config;
        }*/

        $this->config = $config;

        //$config['width'] = '100';
        $config['height'] = '300';

        return $this->config;
    }

    /**
     * @return array
     */
    public function getNewPageBlock()
    {
        return ['NewPage', 'Templates', '-', 'PasteFromWord', 'inserthtml'];
    }

    /**
     * Get the default toolbar configuration when the setting more_buttons_maximized_mode is false.
     *
     * @return array
     */
    protected function getNormalToolbar()
    {
        return null;
    }

    /**
     * Get the toolbar configuration when CKEditor is minimized.
     *
     * @return array
     */
    protected function getMinimizedToolbar()
    {
        return [
            $this->getNewPageBlock(),
            ['Undo', 'Redo'],
            [
                'Link',
                'Image',
                'Video',
                'Oembed',
                'Flash',
                'Youtube',
                'VimeoEmbed',
                'Audio',
                'Table',
                'Asciimath',
                'Asciisvg',
            ],
            ['BulletedList', 'NumberedList', 'HorizontalRule'],
            ['JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'],
            ['Styles', 'Format', 'Font', 'FontSize', 'Bold', 'Italic', 'Underline', 'TextColor', 'BGColor'],
            'true' === api_get_setting('enabled_wiris') ? ['ckeditor_wiris_formulaEditor', 'ckeditor_wiris_CAS'] : [''],
            ['Toolbarswitch', 'Source'],
        ];
    }

    /**
     * Get the toolbar configuration when CKEditor is maximized.
     *
     * @return array
     */
    protected function getMaximizedToolbar()
    {
        return [
            $this->getNewPageBlock(),
            ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', 'inserthtml'],
            ['Undo', 'Redo', '-', 'SelectAll', 'Find', '-', 'RemoveFormat'],
            ['Link', 'Unlink', 'Anchor', 'Glossary'],
            [
                'Image',
                'Mapping',
                'Video',
                'Oembed',
                'Flash',
                'Youtube',
                'VimeoEmbed',
                'Audio',
                'leaflet',
                'Smiley',
                'SpecialChar',
                'Asciimath',
                'Asciisvg',
            ],
            '/',
            ['Table', '-', 'CreateDiv'],
            ['BulletedList', 'NumberedList', 'HorizontalRule', '-', 'Outdent', 'Indent', 'Blockquote'],
            ['JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'],
            ['Bold', 'Italic', 'Underline', 'Strike', '-', 'Subscript', 'Superscript', '-', 'TextColor', 'BGColor'],
            ['true' === api_get_setting('allow_spellcheck') ? 'Scayt' : ''],
            ['Styles', 'Format', 'Font', 'FontSize'],
            ['PageBreak', 'ShowBlocks'],
            'true' === api_get_setting('enabled_wiris') ? ['ckeditor_wiris_formulaEditor', 'ckeditor_wiris_CAS'] : [''],
            ['Toolbarswitch', 'Source'],
        ];
    }
}
