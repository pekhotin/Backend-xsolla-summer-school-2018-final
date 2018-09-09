<?php

namespace App\Service;

use \Tuupola\Middleware\HttpBasicAuthentication;
use \Doctrine\DBAL\Driver\PDOConnection;

class AuthenticationFactory
{
    public static function getAuthentication()
    {
        $dbh = new PDOConnection('mysql:host=localhost;dbname=mvc', 'root', 'root');

        return new HttpBasicAuthentication([
            'authenticator' => new HttpBasicAuthentication\PdoAuthenticator([
                'pdo' => $dbh,
                'table' => 'Users',
                'user'=> 'login'
            ]),
            'path' => '/api/v1',
            'secure' => false,
            'ignore' => '/api/v1/users',
            'before' => function ($request, $arg) {
                return $request->withAttribute('user', $arg['user']);
            }
        ]);
    }
}