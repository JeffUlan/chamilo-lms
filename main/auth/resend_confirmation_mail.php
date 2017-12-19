<?php
/* For license terms, see /license.txt */

require_once __DIR__.'/../inc/global.inc.php';

// Build the form
$form = new FormValidator('resend');
$form->addElement('header', get_lang('ReSendConfirmationMail'));
$form->addText('user', get_lang('UserName'), true);
$form->addButtonSend(get_lang('Send'));

if ($form->validate()) {
    $values = $form->exportValues();
    $user = UserManager::getManager()->findUserByUsername($values['user']);

    UserManager::sendUserConfirmationMail($user);
    header('Location: '.api_get_path(WEB_PATH));
    exit;
}

$tpl = new Template(null);
$tpl->assign('content', $form->toHtml());
$tpl->display_one_col_template();
