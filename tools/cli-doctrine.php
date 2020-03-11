<?php

use Doctrine\ORM\Tools\Console\ConsoleRunner;

define('APPPATH', dirname(__FILE__) . '/../application/');
define('BASEPATH', APPPATH . '/../system/');
define('ENVIRONMENT', 'development');
define('IS_DEV_MODE', true);
require_once APPPATH . 'vendor/autoload.php';
require_once APPPATH . 'libraries/Doctrine.php';
$doctrine = new Doctrine();
ConsoleRunner::run(ConsoleRunner::createHelperSet($doctrine->em));
