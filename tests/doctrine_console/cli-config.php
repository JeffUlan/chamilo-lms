<?php
require_once dirname(__FILE__).'/../../main/inc/global.inc.php';

error_reporting(-1);

$config = new \Doctrine\ORM\Configuration();
$config->setMetadataCacheImpl(new \Doctrine\Common\Cache\ArrayCache);

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;

AnnotationRegistry::registerFile(api_get_path(SYS_PATH)."vendor/doctrine/orm/lib/Doctrine/ORM/Mapping/Driver/DoctrineAnnotations.php");
$reader = new AnnotationReader();

$driverImpl = new \Doctrine\ORM\Mapping\Driver\AnnotationDriver($reader, array(api_get_path(SYS_PATH)."tests/doctrine_console/mapping"));

$config->setMetadataDriverImpl($driverImpl);
$config->setProxyDir(__DIR__ . '/Proxies');
$config->setProxyNamespace('Proxies');

$courseList = CourseManager::get_real_course_list();

$connectionOptions = array();

if (!empty($courseList)) {
    foreach ($courseList as $course) {
        $connectionOptions['_chamilo_course_.'.$course['db_name']] = array(
            'driver'    => 'pdo_mysql',
            'dbname'    => $course['db_name'],
            'user'      => $_configuration['db_user'],
            'password'  => $_configuration['db_password'],
            'host'      => $_configuration['db_host'],
        );
    }
}

$connectionOptions['main_database'] = array(
    'driver'    => 'pdo_mysql',
    'dbname'    => $_configuration['main_database'],
    'user'      => $_configuration['db_user'],
    'password'  => $_configuration['db_password'],
    'host'      => $_configuration['db_host'],
);

$connectionOptions['statistics_database'] = array(
    'driver'    => 'pdo_mysql',
    'dbname'    => $_configuration['statistics_database'],
    'user'      => $_configuration['db_user'],
    'password'  => $_configuration['db_password'],
    'host'      => $_configuration['db_host'],
);

/*
$connectionOptions['scorm_database'] = array(
    'driver'    => 'pdo_mysql',
    'dbname'    => $_configuration['scorm_database'],
    'user'      => $_configuration['db_user'],
    'password'  => $_configuration['db_password'],
    'host'      => $_configuration['db_host'],
);*/

$connectionOptions['user_personal_database'] = array(
    'driver'    => 'pdo_mysql',
    'dbname'    => $_configuration['user_personal_database'],
    'user'      => $_configuration['db_user'],
    'password'  => $_configuration['db_password'],
    'host'      => $_configuration['db_host'],
);


$defaultConnection = array(
    'driver'    => 'pdo_mysql',
    'dbname'    => $_configuration['main_database'],
    'user'      => $_configuration['db_user'],
    'password'  => $_configuration['db_password'],
    'host'      => $_configuration['db_host'],
);

$em = \Doctrine\ORM\EntityManager::create($defaultConnection, $config);

//Fixes some errors
$platform = $em->getConnection()->getDatabasePlatform();
$platform->registerDoctrineTypeMapping('enum', 'string');
$platform->registerDoctrineTypeMapping('set', 'string');

$helpers = array(
    'db' => new \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper($em->getConnection()),
    'em' => new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper($em),
);

use Doctrine\DBAL\DriverManager;
$multipleEM = array();
foreach ($connectionOptions as $name => $connection) {
    $em = \Doctrine\ORM\EntityManager::create($defaultConnection, $config);
    //$helpers[$name] = new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper($em);
    $helpers[$name] = new \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper($em->getConnection());
}

/*
To generate doctrine2 entities you must:

cd /var/www/chamilo11/tests/doctrine_console

Delete old mappings/entities

sudo rm -R mapping generated repository

Creating the mapping from the DB

sudo mkdir mapping generated repository

You can add a Namespace if you want to with: --namespace "Entity"
sudo php5 doctrine.php orm:convert-mapping --force --from-database --namespace "Entity" annotation mapping


1. Generate entities

sudo php5 doctrine.php orm:generate-entities   --generate-annotations="true"   generated

Validate schema
sudo php5 doctrine.php orm:validate-schema -v

Move generated files in a chamilo folder:

sudo rm -R main/inc/Entity/*
mkdir main/inc/Entity

cp -R tests/doctrine_console/generated/* main/inc/Entity

fixes \ORM bug see http://redgreenrefactor.blogsite.org/php/code-first-approaching-php-with-doctrine-2-2-1-and-composer/
cd main/inc/Entity

sed -i 's/@ORM\\/@/g' *.php

For tests
php5 tests/doctrine_console/doctrine.php orm:generate-entities   --generate-annotations="true"   main/inc/Entity

Then autoload classes with composer
sudo php5 composer.phar update or sudo composer.phar update

2. Migrations

a. Generate empty migration file
cd /var/www/chamilo11/tests/doctrine_console

php5 doctrine.php migrations:generate

b. Check status

php5 doctrine.php migrations:status

c. Check sql
php5 doctrine.php migrations:migrate --dry-run

d. execute migration
php5 doctrine.php migrations:migrate

e. Revert migrations
php5 doctrine.php  migrations:migrate 0


http://docs.doctrine-project.org/projects/doctrine-migrations/en/latest/reference/managing_migrations.html

*/

