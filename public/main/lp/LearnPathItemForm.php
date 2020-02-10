<?php

/* For licensing terms, see /license.txt */

use Chamilo\CoreBundle\Framework\Container;
use Chamilo\CourseBundle\Entity\CDocument;
use Chamilo\CourseBundle\Entity\CLpItem;

/**
 * Class LearnPathItemForm
 */
class LearnPathItemForm
{
    public static function setForm(FormValidator $form, $action, learnpath $lp, CLpItem $lpItem)
    {
        $itemId = $lpItem->getIid();
        $itemTitle = $lpItem->getTitle();
        $itemDescription = $lpItem->getDescription();
        $parentItemId = $lpItem->getParentItemId();
        $itemType = $lpItem->getItemType();
        $previousItemId = $lpItem->getPreviousItemId();

        $arrLP = $lp->getItemsForForm();
        $lp->tree_array($arrLP);
        $arrLP = isset($lp->arrMenu) ? $lp->arrMenu : [];

        switch ($action) {
            case 'add':
                $form->addHeader(get_lang('Add'));

                self::setItemTitle($form);

                break;

            case 'edit':
                $form->addHeader(get_lang('Edit'));
                self::setItemTitle($form);

                break;

            case 'move':
                $form->addHeader(get_lang('Move'));

                break;
        }

        $arrHide = [];
        $count = count($arrLP);
        $sections = [];
        for ($i = 0; $i < $count; $i++) {
            if ('add' !== $action) {
                if ('dir' === $arrLP[$i]['item_type'] &&
                    !in_array($arrLP[$i]['id'], $arrHide) &&
                    !in_array($arrLP[$i]['parent_item_id'], $arrHide)
                ) {
                    $arrHide[$arrLP[$i]['id']]['value'] = $arrLP[$i]['title'];
                    $arrHide[$arrLP[$i]['id']]['padding'] = 20 + $arrLP[$i]['depth'] * 20;
                }
            }

            if ('dir' === $arrLP[$i]['item_type']) {
                $sections[$arrLP[$i]['id']]['value'] = $arrLP[$i]['title'];
                $sections[$arrLP[$i]['id']]['padding'] = 20 + $arrLP[$i]['depth'] * 20;
            }
        }

        // Parent
        $parentSelect = $form->addSelect(
            'parent',
            get_lang('Parent'),
            [],
            [
                'id' => 'idParent',
                'onchange' => 'javascript:load_cbo(this.value);',
            ]
        );
        $parentSelect->addOption($lp->name, 0);

        $arrHide = [];
        for ($i = 0; $i < $count; $i++) {
            if ($arrLP[$i]['id'] != $itemId && 'dir' !== $arrLP[$i]['item_type']) {
                $arrHide[$arrLP[$i]['id']]['value'] = $arrLP[$i]['title'];
            }
        }

        $sectionCount = 0;
        foreach ($sections as $key => $value) {
            if (0 != $sectionCount) {
                // The LP name is also the first section and is not in the same charset like the other sections.
                $value['value'] = Security::remove_XSS($value['value']);
                $parentSelect->addOption(
                    $value['value'],
                    $key
                    //,'style="padding-left:'.$value['padding'].'px;"'
                );
            } else {
                $value['value'] = Security::remove_XSS($value['value']);
                $parentSelect->addOption(
                    $value['value'],
                    $key
                    //'style="padding-left:'.$value['padding'].'px;"'
                );
            }
            $sectionCount++;
        }

        $parentSelect->setSelected($parentItemId);

        if (is_array($arrLP)) {
            reset($arrLP);
        }

        $arrHide = [];

        // Position
        for ($i = 0; $i < $count; $i++) {
            if (($arrLP[$i]['parent_item_id'] == $parentItemId && $arrLP[$i]['id'] != $itemId) ||
                TOOL_LP_FINAL_ITEM == $arrLP[$i]['item_type']
            ) {
                $arrHide[$arrLP[$i]['id']]['value'] = get_lang('After').' "'.$arrLP[$i]['title'].'"';
            }
        }

        $position = $form->addSelect(
            'previous',
            get_lang('Position'),
            [],
            ['id' => 'previous']
        );

        $position->addOption(get_lang('First position'), 0);

        foreach ($arrHide as $key => $value) {
            $padding = $value['padding'] ?? 20;
            $position->addOption(
                $value['value'],
                $key,
                'style="padding-left:'.$padding.'px;"'
            );
        }

        $position->setSelected($previousItemId);

        if (is_array($arrLP)) {
            reset($arrLP);
        }

        if (TOOL_LP_FINAL_ITEM == $itemType) {
            $parentSelect->freeze();
            $position->freeze();
        }

        // Content
        if (in_array($itemType, [TOOL_DOCUMENT, TOOL_LP_FINAL_ITEM, TOOL_READOUT_TEXT], true)) {
            $document = null;
            if (!empty($lpItem->getPath())) {
                $repo = Container::getDocumentRepository();
                /** @var CDocument $document */
                $document = $repo->find($lpItem->getPath());
            }

            $editorConfig = [
                'ToolbarSet' => 'LearningPathDocuments',
                'Width' => '100%',
                'Height' => '500',
                'FullPage' => true,
                //   'CreateDocumentDir' => $relative_prefix,
                //'CreateDocumentWebDir' => api_get_path(WEB_COURSE_PATH).api_get_course_path().'/document/',
                //'BaseHref' => api_get_path(WEB_COURSE_PATH).api_get_course_path().'/document/'.$relative_path,
            ];

            $renderer = $form->defaultRenderer();
            $renderer->setElementTemplate('&nbsp;{label}{element}', 'content_lp');
            $form->addElement('html', '<div class="editor-lp">');
            $form->addHtmlEditor('content_lp', null, null, true, $editorConfig, true);
            $form->addElement('html', '</div>');

            if ($document) {
                if ($document->getResourceNode()->hasEditableContent()) {
                    $form->addHidden('document_id', $document->getIid());
                    $content = $lp->display_document(
                        $document,
                        false,
                        false
                    );
                    $form->setDefault('content_lp', $content);
                }
            }
        }

        if ($form->hasElement('title')) {
            $form->setDefault('title', $itemTitle);
        }
        if ($form->hasElement('description')) {
            $form->setDefault('description', $itemDescription);
        }

        $form->addHidden('id', $itemId);
        $form->addHidden('type', $itemType);
        $form->addHidden('post_time', time());
        $form->addHidden('path', $lpItem->getPath());
    }

    public static function setItemTitle(FormValidator $form)
    {
        if (api_get_configuration_value('save_titles_as_html')) {
            $form->addHtmlEditor(
                'title',
                get_lang('Title'),
                true,
                false,
                ['ToolbarSet' => 'TitleAsHtml', 'id' => uniqid('editor')]
            );
        } else {
            $form->addText('title', get_lang('Title'), true, ['id' => 'idTitle', 'class' => 'learnpath_item_form']);
            $form->applyFilter('title', 'trim');
            $form->applyFilter('title', 'html_filter');
        }
    }
}
