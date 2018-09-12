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
        $configParams = [
            'driver' => 'pdo_mysql',
            'host' => 'localhost',
            'dbname' => 'mvc',
            'user' => 'non-root',
            'password' => '12345',
            'port' => 3306,
            'charset' => 'utf8',

        ];

	    $config = new Configuration();

        return DriverManager::getConnection(
        	$configParams, 
        	$config
    	);
    }
}