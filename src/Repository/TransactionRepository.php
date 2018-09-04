<?php

namespace App\Repository;

use App\Model\Transaction;
use Doctrine\DBAL\Connection;

class TransactionRepository extends AbstractRepository
{
    /**
     * @var string
     */
    private $tableName;

    /**
     * TransactionRepository constructor.
     *
     * @param Connection $dbConnection
     */
    public function __construct(Connection $dbConnection)
    {
        parent::__construct($dbConnection);
        $this->tableName = 'Transactions';
    }

    /**
     * @param Transaction $transaction
     */
    public function insert(Transaction $transaction)
    {
        $this->dbConnection->insert(
            $this->tableName,
            $transaction->getTransactionArray()
        );

        $transaction->setId($this->dbConnection->lastInsertId());
    }
}