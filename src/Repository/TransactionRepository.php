<?php

namespace App\Repository;

use App\Model\Transaction;
use Doctrine\DBAL\Connection;

class TransactionRepository extends AbstractRepository
{
    /**
     * @param Transaction $transaction
     */
    public function insert(Transaction $transaction)
    {
        $this->dbConnection->insert(
            'Transactions',
            $transaction->getTransactionArray()
        );

        $transaction->setId($this->dbConnection->lastInsertId());
    }

    /**
     * @param int $warehouseId
     *
     * @return Transaction[]|null
     */
    public function findAllByWarehouseId($warehouseId)
    {
        $rows = $this->dbConnection->fetchAll(
            'SELECT * FROM Transactions WHERE warehouseId = ?',
            [$warehouseId]
        );

        if ($rows === false) return null;

        $transactionsArray = [];

        foreach ($rows as $row) {
            $transactionsArray[] = new Transaction(
                null,
                $row['warehouseId'],
                $row['productId'],
                $row['quantity'],
                $row['direction'],
                $row['datetime'],
                $row['sender'],
                $row['recipient']
            );
        }
        return $transactionsArray;
    }
}