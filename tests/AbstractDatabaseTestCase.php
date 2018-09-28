<?php

namespace Tests;

use App\Service\ConnectionFactory;
use Doctrine\DBAL\Driver\PDOConnection;
use PHPUnit\DbUnit\TestCaseTrait;
use PHPUnit\Framework\TestCase;

abstract class AbstractDatabaseTestCase extends TestCase
{
    use TestCaseTrait;

    static private $pdo = null;

    protected $dbal = null;

    private $conn = null;

    /**
     * @return null|\PHPUnit\DbUnit\Database\DefaultConnection
     * @expectedException \Doctrine\DBAL\DBALException
     */
    public function getConnection()
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
            $query = file_get_contents(__DIR__ . '/../resources/mvc.sql');
            self::$pdo->query($query);
            $this->conn = $this->createDefaultDBConnection(
                self::$pdo,
                $configParams['dbname']
            );
        }
        if ($this->dbal === null) {
            $this->dbal = ConnectionFactory::getConnection();
        }
        return $this->conn;
    }
}
