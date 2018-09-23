<?php

namespace Tests;

use App\Model\User;
use Doctrine\DBAL\Driver\PDOConnection;
use PHPUnit\DbUnit\Database\Connection;
use PHPUnit\DbUnit\TestCaseTrait;
use PHPUnit\Framework\TestCase;

abstract class AbstractDatabaseTestCase extends TestCase
{
    use TestCaseTrait;
    /**
     * @var mixed
     */
    protected $fixture;
    /**
     * @var PDOConnection
     */
    static private $pdo = null;
    /**
     * @var Connection
     */
    private $conn = null;

    protected function setUp()
    {

    }

    protected function tearDown()
    {
        $this->fixture = null;
    }

    /**
     * @return Connection|\PHPUnit\DbUnit\Database\DefaultConnection
     */
    final public function getConnection()
    {
        if ($this->conn === null) {
            $configParams = require __DIR__ . '/../config/config.php';
            if (self::$pdo === null) {
                self::$pdo = new PDOConnection(
                    $configParams['dsn'],
                    $configParams['username'],
                    $configParams['password']
                );
            }
            $query = file_get_contents(__DIR__ . '/../resources/script.sql');
            self::$pdo->query($query);
            $this->conn = $this->createDefaultDBConnection(
                self::$pdo,
                $configParams['dbname']
            );
        }

        return $this->conn;
    }
}
