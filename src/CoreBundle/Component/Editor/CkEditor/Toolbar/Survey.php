<?php

/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Component\Editor\CkEditor\Toolbar;

/**
 * Survey toolbar configuration.
 */
class Survey extends Basic
{
    /**
     * Get the toolbar config.
     *
     * @return array
     */
    public function getConfig()
    {
        if ('true' != api_get_setting('more_buttons_maximized_mode')) {
            $config['toolbar'] = $this->getNormalToolbar();
        } else {
            $config['toolbar_minToolbar'] = $this->getMinimizedToolbar();
            $config['toolbar_maxToolbar'] = $this->getMaximizedToolbar();
        }

        return $config;
    }

    /**
     * Get the toolbar configuration when CKEditor is maximized.
     *
     * @return array
     */
    protected function getMaximizedToolbar()
    {
        return [
            ['NewPage', 'Templates', '-', 'Preview', 'Print'],
            ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord'],
            ['Undo', 'Redo', '-', 'SelectAll', 'Find', '-', 'RemoveFormat'],
            ['Link', 'Unlink', 'Anchor', 'Glossary'],
            [
                'Image',
                'Mapping',
                'Video',
                'Oembed',
                'Youtube',
                'Flash',
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
            ['true' == api_get_setting('allow_spellcheck') ? 'Scayt' : ''],
            ['Styles', 'Format', 'Font', 'FontSize'],
            ['PageBreak', 'ShowBlocks', 'Source'],
            ['Toolbarswitch'],
        ];
    }

    /**
     * Get the default toolbar configuration when the setting more_buttons_maximized_mode is false.
     *
     * @return array
     */
    protected function getNormalToolbar()
    {
        return [
            ['Maximize'],
            ['Link', 'Unlink'],
            ['Image'],
            ['Table'],
            ['FontSize'],
            ['Bold', 'Italic'],
            ['NumberedList', 'BulletedList', '-', 'TextColor'],
            ['Source'],
        ];
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
            ['Link', 'Image', 'Video', 'Flash', 'Audio', 'Table', 'Asciimath', 'Asciisvg'],
            ['BulletedList', 'NumberedList', 'HorizontalRule'],
            ['JustifyLeft', 'JustifyCenter', 'JustifyRight'],
            ['Format', 'FontSize', 'Bold', 'Italic', 'Underline', 'TextColor', 'BGColor', 'Source'],
            ['Toolbarswitch'],
        ];
    }
}
