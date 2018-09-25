<?php

namespace App\Repository;

use Doctrine\DBAL\Connection;

class AbstractRepository
{
    /**
     * @var Connection
     */
    protected $dbConnection;
    /**
     * AbstractRepository constructor.
     *
     * @param Connection $dbConnection
     */
    public function __construct(Connection $dbConnection)
    {
        $this->dbConnection = $dbConnection;
    }
}
