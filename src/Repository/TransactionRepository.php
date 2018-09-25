<?php

namespace App\Repository;

use App\Model\Transaction;

class TransactionRepository extends AbstractRepository
{
    /**
     * @param Transaction[] $transactions
     */
    public function insert($transactions)
    {
        foreach ($transactions as $transaction) {
            $this->dbConnection->insert(
                'Transactions',
                $transaction->getTransactionArray()
            );

            $transaction->setId((int)$this->dbConnection->lastInsertId());
        }
    }
    /**
     * @param int $warehouseId
     *
     * @return array|null
     */
    public function findAllByWarehouse($warehouseId)
    {
        $rows = $this->dbConnection->fetchAll(
            'SELECT t.id, t.quantity, t.direction, t.datetime, t.sender, t.recipient, p.price, p.name, p.sku 
                FROM Transactions AS t
                JOIN Products AS p ON t.productId = p.id
                WHERE t.sender = ? OR t.recipient = ?
                ORDER BY t.datetime',
            [
                $warehouseId,
                $warehouseId
            ]
        );

        if (count($rows) === 0) {
            return null;
        }

        $transactionsArray = [];

        foreach ($rows as $row) {
            $transactionsArray[] = [
                'transactionId' => (int)$row['id'],
                'sku' => (int)$row['sku'],
                'quantity' => (int)$row['quantity'],
                'cost' => (float)$row['quantity'] * (float)$row['price'],
                'direction' => (string)$row['direction'],
                'datetime' => (string)$row['datetime'],
                'sender' => (string)$row['sender'],
                'recipient' => (string)$row['recipient'],
            ];
        }
        return $transactionsArray;
    }
    /**
     * @param int $productId
     *
     * @return array|null
     */
    public function getAllByProduct($productId)
    {
        $rows = $this->dbConnection->fetchAll(
            'SELECT t.id, t.quantity, t.direction, t.datetime, t.sender, t.recipient, p.price, p.name, p.sku
                FROM Transactions AS t
                JOIN Products AS p ON t.productId = p.id
                WHERE t.productId = ?
                ORDER BY t.datetime',
            [$productId]
        );

        if (count($rows) === 0) {
            return null;
        }

        $transactionsArray = [];

        foreach ($rows as $row) {
            $transactionsArray[] = [
                'transactionId' => (int)$row['id'],
                'sku' => (int)$row['sku'],
                'quantity' => (int)$row['quantity'],
                'cost' => (float)$row['quantity'] * (float)$row['price'],
                'direction' => (string)$row['direction'],
                'datetime' => (string)$row['datetime'],
                'sender' => (string)$row['sender'],
                'recipient' => (string)$row['recipient']
            ];
        }
        return $transactionsArray;
    }
}
