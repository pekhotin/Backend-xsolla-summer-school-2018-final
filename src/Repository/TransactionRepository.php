<?php

namespace App\Repository;

use App\Model\Transaction;

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

    public function findAllByWarehouse($warehouseId)
    {
        $rows = $this->dbConnection->fetchAll(
            'SELECT t.id, t.productId, t.quantity, t.direction, t.datetime, t.sender, t.recipient, p.price, p.name 
                FROM Transactions AS t
                JOIN Products AS p ON t.productId = p.id
                WHERE t.sender = ? OR t.recipient = ?
                ORDER BY t.datetime',
            [
                $warehouseId,
                $warehouseId
            ]
        );

        if ($rows === false) return null;

        $transactionsArray = [];

        foreach ($rows as $row) {
            $transactionsArray[] = [
                'transactionId' => $row['id'],
                'productId' => $row['productId'],
                'quantity' => $row['quantity'],
                'cost' => $row['quantity'] * $row['price'],
                'direction' => $row['direction'],
                'datetime' => $row['datetime'],
                'sender' => $row['sender'],
                'recipient' => $row['recipient'],
            ];
        }
        return $transactionsArray;
    }

    public function getAllByProduct($productId)
    {
        $rows = $this->dbConnection->fetchAll(
            'SELECT t.id, t.productId, t.quantity, t.direction, t.datetime, t.sender, t.recipient, p.price, p.name 
                FROM Transactions AS t
                JOIN Products AS p ON t.productId = p.id
                WHERE t.productId = ?
                ORDER BY t.datetime',
            [$productId]
        );

        if ($rows === false) return null;

        $transactionsArray = [];

        foreach ($rows as $row) {
            $transactionsArray[] = [
                'transactionId' => $row['id'],
                'productId' => $row['productId'],
                'quantity' => $row['quantity'],
                'cost' => $row['quantity'] * $row['price'],
                'direction' => $row['direction'],
                'datetime' => $row['datetime'],
                'sender' => $row['sender'],
                'recipient' => $row['recipient']
            ];
        }
        return $transactionsArray;
    }
}