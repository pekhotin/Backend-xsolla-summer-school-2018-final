<?php

namespace App\Service;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;

class ConnectionFactory
{
    /**
     * @return \Doctrine\DBAL\Connection
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public static function getConnection()
    {
        $configParams = require __DIR__ . '/../../config/config.php';
	    $config = new Configuration();

        return DriverManager::getConnection(
        	[
                'driver' => 'pdo_mysql',
                'host' => $configParams['host'],
                'dbname' => $configParams['dbname'],
                'user' => $configParams['username'],
                'password' => $configParams['password'],
                'charset' => 'utf8'
            ],
        	$config
    	);
    }
}
