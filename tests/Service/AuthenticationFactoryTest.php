<?php

namespace Tests\Service;

use App\Service\AuthenticationFactory;
use Doctrine\DBAL\Driver\PDOConnection;
use PHPUnit\Framework\TestCase;
use Tuupola\Middleware\HttpBasicAuthentication;

class AuthenticationFactoryTest extends TestCase
{
    public function testGetAuthentication()
    {
        $configParams = require __DIR__ . '/../../config/config.php';
        $dbh = new PDOConnection(
            $configParams['dsn'] . ';dbname=' . $configParams['dbname'],
            $configParams['username'],
            $configParams['password']
        );

        $expectedValues = new HttpBasicAuthentication([
            'authenticator' => new HttpBasicAuthentication\PdoAuthenticator([
                'pdo' => $dbh,
                'table' => 'Users',
                'user'=> 'login'
            ]),
            'path' => ['/api/v1'],
            'secure' => false,
            'ignore' => ['/api/v1/users'],
            'before' => function ($request, $arg) {
                return $request->withAttribute('user', $arg['user']);
            }
        ]);

        $values = AuthenticationFactory::getAuthentication();

        $this->assertEquals($values, $expectedValues);
    }
}
