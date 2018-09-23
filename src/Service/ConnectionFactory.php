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
        $configParams = require __DIR__ . '/../../config/dbal-config.php';

	    $config = new Configuration();

        return DriverManager::getConnection(
        	$configParams, 
        	$config
    	);
    }
}