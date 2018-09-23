<?php

namespace App\Service;

use \Tuupola\Middleware\HttpBasicAuthentication;
use \Doctrine\DBAL\Driver\PDOConnection;

class AuthenticationFactory
{
    /**
     * @return HttpBasicAuthentication
     */
    public static function getAuthentication()
    {
        $configParams = require __DIR__ . '/../../config/config.php';
        $dbh = new PDOConnection(
            $configParams['dsn'] . 'dbname=' . $configParams['dbname'],
            $configParams['username'],
            $configParams['password']
        );

        return new HttpBasicAuthentication([
            'authenticator' => new HttpBasicAuthentication\PdoAuthenticator([
                'pdo' => $dbh,
                'table' => 'Users',
                'user'=> 'login'
            ]),
            'path' => ['/api/v1'],
            'secure' => false,
            'ignore' => ['/api/v1/users', '/api/v1/new'],
            'before' => function ($request, $arg) {
                return $request->withAttribute('user', $arg['user']);
            }
        ]);
    }
}