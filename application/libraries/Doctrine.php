<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;

class Doctrine
{
    /**
     * @var EntityManager
     */
    public $em;

    /**
     * Doctrine constructor
     *
     * @throws Exception
     * @noinspection PhpIncludeInspection
     */
    public function __construct()
    {
        if (! file_exists($database_config_path = APPPATH . 'config/' . ENVIRONMENT . '/database.php')
            && ! file_exists($database_config_path = APPPATH . 'config/database.php')) {
            throw new Exception('The configuration file database.php does not exist.');
        }
        /**
         * @var $db
         */
        require_once $database_config_path;
        $config = Setup::createAnnotationMetadataConfiguration(
            array(APPPATH . 'models'),
            IS_DEV_MODE,
            APPPATH . 'proxies',
            null, //use default ArrayCache
            false);
        //$config->setSQLLogger(new \Doctrine\DBAL\Logging\EchoSQLLogger());

        $this->em = EntityManager::create(array(
            'driver' => 'pdo_mysql',
            'user' =>     $db['default']['username'],
            'password' => $db['default']['password'],
            'host' =>     $db['default']['hostname'],
            'dbname' =>   $db['default']['database']
        ), $config);
    }
}
