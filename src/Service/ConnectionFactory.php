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
     //    $config = new Configuration();
     //    return DriverManager::getConnection(
     //    	include('config/doctrine.php'), 
     //    	$config

        $configParams = [
            'driver' => 'pdo_mysql',
            'host' => 'localhost',
            'dbname' => 'mvc',
            'user' => 'root',
            'password' => 'root',
            'port' => 3306,
            'charset' => 'utf8'
        ];

	    $config = new Configuration();

        return DriverManager::getConnection(
        	$configParams, 
        	$config
    	);
    }
}