<?php


//the plugin title
$plugin_info['title'] = 'Edit htaccess';
//the comments that go with the plugin
$plugin_info['comment'] = 'Edit htaccess';
//the plugin version
$plugin_info['version'] = '1.0';
//the plugin author
$plugin_info['author'] = 'Julio Montoya';

$editFile = false;

$file = api_get_path(SYS_PATH).'.htaccess';
$maintenanceHtml = api_get_path(SYS_PATH).'maintenance.html';

if (!file_exists($file)) {
    Display::addFlash(
        Display::return_message(
            "$file does not exists. ",
            'warning'
        )
    );
} else {
    if (is_readable($file) && is_writable($file)) {
        $editFile = true;
    } else {
        if (!is_readable($file)) {
            Display::addFlash(
                Display::return_message("$file is not readable", 'warning')
            );
        }

        if (!is_writable($file)) {
            Display::addFlash(
                Display::return_message("$file is not writable", 'warning')
            );
        }
    }
}

if ($editFile && api_is_platform_admin()) {
    $originalContent = file_get_contents($file);
    $beginLine = '###@@ This part was generated by the edit_htaccess plugin @@##';
    $endLine = '###@@ End @@##';

    $handler = fopen($file, 'r');
    $deleteLinesList = [];
    $deleteLine = false;
    $contentNoBlock = '';
    $block = '';
    while (!feof($handler)) {
        $line = fgets($handler);
        $lineTrimmed = trim($line);

        if ($lineTrimmed == $beginLine) {
            $deleteLine = true;
        }

        if ($deleteLine) {
            $block .= $line;
        } else {
            $contentNoBlock .= $line;
        }

        if ($lineTrimmed == $endLine) {
            $deleteLine = false;
        }
    }

    fclose($handler);
    $block = str_replace($beginLine, '', $block);
    $block = str_replace($endLine, '', $block);

    $form = new FormValidator('htaccess');
    $form->addHtml('The following text will be added in the /.htaccess');
    $form->addText('ip', 'IP');
    $form->addTextarea('text', 'htaccess', ['rows' => '15']);

    $config = [
        'ToolbarSet' => 'Documents',
        'Width' => '100%',
        'Height' => '400',
        //'fullPage' => true,
        'allowedContent' => true
    ];

    $form->addHtmlEditor(
        'maintenance',
        'Maintenance',
        true,
        true,
        $config
    );

    $form->addButtonSave(get_lang('Save'));
    $content = file_get_contents($maintenanceHtml);
    if (empty($content)) {
        $content = '<html><head><title></title></head><body></body></html>';
    }
    $ip = api_get_plugin_setting('edit_htaccess', 'ip');
    $ip = api_get_real_ip();
    $ipSubList = explode('.', $ip);
    $implode = implode('\.', $ipSubList);
    $append = api_get_configuration_value('url_append');
    $default = '
RewriteCond %{REQUEST_URI} !/'.$append.'/maintenance.html$ 
RewriteCond %{REMOTE_HOST} !^'.$implode.'
RewriteRule $ /'.$append.'/maintenance.html [R=302,L]
';
    if (empty($block)) {
        $block = $default;
    }

    $form->setDefaults(['text' => $block, 'maintenance' => $content, 'ip' => $ip]);

    if ($form->validate()) {
        $values = $form->getSubmitValues();
        $text = $values['text'];
        $content = $values['maintenance'];

        // Restore htaccess with out the block
        $newFileContent = $contentNoBlock;
        $newFileContent .= $beginLine.PHP_EOL;
        $newFileContent .= $text.PHP_EOL;
        $newFileContent .= $endLine;
        file_put_contents($file, $newFileContent);

        $handle = curl_init(api_get_path(WEB_PATH));
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($handle);
        $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        curl_close($handle);

        // Looks htaccess contains errors. Restore as it was.
        if ($httpCode != 200) {
            Display::addFlash(
                Display::return_message(
                    'Check your htaccess instructions. The original file was restored.',
                    'warning'
                )
            );
            file_put_contents($file, $originalContent);
        } else {
            file_put_contents($maintenanceHtml, $content);
            Display::addFlash(Display::return_message('Saved'));
        }
    }
    $plugin_info['settings_form'] = $form;
}

