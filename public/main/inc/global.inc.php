<?php

/* For licensing terms, see /license.txt */

use Chamilo\CoreBundle\Framework\Container;
use Patchwork\Utf8\Bootup;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\ErrorHandler\Debug;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;

/**
 * All legacy Chamilo scripts should include this important file.
 */
define('USERNAME_MAX_LENGTH', 100);

require_once __DIR__.'/../../../vendor/autoload.php';

try {
    // Check the PHP version
    api_check_php_version();

    // Get settings from .env.local file created.
    $envFile = __DIR__.'/../../../.env.local';
    if (file_exists($envFile)) {
        (new Dotenv())->load($envFile);
    } else {
        throw new \RuntimeException('APP_ENV environment variable is not defined.
        You need to define environment variables for configuration to load variables from a .env.local file.');
    }

    $env = $_SERVER['APP_ENV'] ?? 'dev';
    //Debug::enable();
    $kernel = new Chamilo\Kernel($env, true);
    // Loading Request from Sonata. In order to use Sonata Pages Bundle.
    $request = \Symfony\Component\HttpFoundation\Request::createFromGlobals();

    // This 'load_legacy' variable is needed to know that symfony is loaded using old style legacy mode,
    // and not called from a symfony controller from public/
    $request->request->set('load_legacy', true);

    $currentBaseUrl = $request->getBaseUrl();
    $kernel->boot();
    $container = $kernel->getContainer();
    $router = $container->get('router');
    /** @var FlashBag $flashBag */
    $flashBag = $container->get('session')->getFlashBag();
    $saveFlashBag = null;
    if (!empty($flashBag->keys())) {
        $saveFlashBag = $flashBag->all();
    }

    $context = $router->getContext();

    $router->setContext($context);

    $response = $kernel->handle($request);
    $context = Container::getRouter()->getContext();

    $pos = strpos($currentBaseUrl, 'main');
    if (false === $pos) {
        echo 'Cannot load current URL';
        exit;
    }
    $newBaseUrl = substr($currentBaseUrl, 0, $pos - 1);
    $context->setBaseUrl($newBaseUrl);

    $container = $kernel->getContainer();

    if ($kernel->isInstalled()) {
        require_once $kernel->getConfigurationFile();
    } else {
        throw new Exception('Chamilo is not installed');
    }

    // Do not over-use this variable. It is only for this script's local use.
    $libraryPath = __DIR__.'/lib/';
    $container = $kernel->getContainer();

    // Symfony uses request_stack now
    $container->get('request_stack')->push($request);

    if (!empty($saveFlashBag)) {
        foreach ($saveFlashBag as $typeMessage => $messageList) {
            foreach ($messageList as $message) {
                Container::getSession()->getFlashBag()->add($typeMessage, $message);
            }
        }
    }

    // Connect Chamilo with the Symfony container
    // Container::setContainer($container);
    // Container::setLegacyServices($container);

    // The code below is not needed. The connections is now made in the file:
    // src/CoreBundle/EventListener/LegacyListener.php
    // This is called when when doing the $kernel->handle
    $charset = 'UTF-8';
    // Enables the portability layer and configures PHP for UTF-8
    Bootup::initAll();
    ini_set('log_errors', '1');
    $this_section = SECTION_GLOBAL;
    //Default quota for the course documents folder
    /*$default_quota = api_get_setting('default_document_quotum');
    //Just in case the setting is not correctly set
    if (empty($default_quota)) {
        $default_quota = 100000000;
    }
    define('DEFAULT_DOCUMENT_QUOTA', $default_quota);*/
    define('DEFAULT_DOCUMENT_QUOTA', 100000000);
} catch (Exception $e) {
    echo $e->getMessage();
    var_dump($e->getMessage());
    var_dump($e->getCode());
    var_dump($e->getLine());
    echo $e->getTraceAsString();
    //exit;*/
}
