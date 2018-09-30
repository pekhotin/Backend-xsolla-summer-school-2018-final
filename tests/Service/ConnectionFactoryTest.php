<?php

namespace Tests\Service;

use App\Service\ConnectionFactory;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use PHPUnit\Framework\TestCase;

class ConnectionFactoryTest extends TestCase
{
    public function testGetConnection()
    {
        $configParams = require __DIR__ . '/../../config/config.php';
        $config = new Configuration();
        $expectedValues = DriverManager::getConnection(
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

        $values = ConnectionFactory::getConnection();

        $this->assertEquals($values, $expectedValues);
    }
}
