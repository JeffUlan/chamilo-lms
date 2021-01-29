<?php

/* For licensing terms, see /license.txt */

use Chamilo\Kernel;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\ErrorHandler\Debug;
use Symfony\Component\HttpFoundation\Request;

require dirname(__DIR__).'/vendor/autoload.php';

(new Dotenv())->bootEnv(dirname(__DIR__).'/.env');

// Redirects to the installation page.
$isInstalled = $_ENV['APP_INSTALLED'] ?? null;
if (1 !== (int) $isInstalled) {
    // Does not support subdirectories for now
    header('Location: /main/install/index.php');
    exit;
}

if ($_SERVER['APP_DEBUG']) {
    umask(0000);

    Debug::enable();
}

$kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
