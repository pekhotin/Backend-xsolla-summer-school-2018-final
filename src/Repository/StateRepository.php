<?php

namespace App\Repository;

use App\Model\Transaction;

class StateRepository extends AbstractRepository
{
    /**
     * @param int $warehouseId
     * @param int $productId
     *
     * @return int
     */
    public function getTodayQuantity($warehouseId, $productId)
    {
        $quantity = $this->dbConnection->fetchColumn(
            'SELECT quantity FROM State WHERE warehouseId = ? AND productId = ? AND date = ?',
            [
                $warehouseId,
                $productId,
                date('Y-m-d')
            ]
        );

        if ($quantity === false) {
            return -1;
        }

        return (int)$quantity;
    }
    /**
     * @param int $warehouseId
     * @param int $productId
     *
     * @return int
     */
    public function getLastQuantity($warehouseId, $productId)
    {
        $quantity = $this->dbConnection->fetchColumn(
            'SELECT quantity FROM State WHERE warehouseId = ? AND productId = ? ORDER BY date DESC',
            [
                $warehouseId,
                $productId
            ]
        );

        if ($quantity === false) {
            return -1;
        }
        return (int)$quantity;

    }
    /**
     * @param int $warehouseId
     * @param int $productId
     * @param int $quantity
     *
     * @return array
     */
    private function update($warehouseId, $productId, $quantity)
    {
        $this->dbConnection->update(
            'State',
            ['quantity' => $quantity],
            [
                'warehouseId' => $warehouseId,
                'productId' => $productId,
                'date' => date('Y-m-d')
            ]
        );
    }
    /**
     * @param int $warehouseId
     * @param int $productId
     * @param int $quantity
     *
     * @return array
     */
    private function insert($warehouseId, $productId, $quantity)
    {
        $this->dbConnection->insert(
            'State',
            [
                'warehouseId' => $warehouseId,
                'productId' => $productId,
                'quantity' => $quantity,
                'date' => date('Y-m-d')
            ]
        );
    }
    /**
     * @param Transaction[] $transactions
     *
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function addProducts($transactions)
    {
        $this->dbConnection->beginTransaction();
        try {
            foreach ($transactions as $transaction) {
                $warehouseId = (int)$transaction->getRecipient();
                $productId = $transaction->getProduct()->getId();
                $quantity = $transaction->getQuantity();
                $todayQuantity = $this->getTodayQuantity($warehouseId, $productId);

                if ($todayQuantity >= 0) {
                    $this->update($warehouseId, $productId, $quantity + $todayQuantity);
                } else {
                    $lastQuantity = $this->getLastQuantity($warehouseId, $productId);
                    if ($lastQuantity > 0) {
                        $this->insert($warehouseId, $productId, $lastQuantity + $quantity);
                    } else {
                        $this->insert($warehouseId, $productId, $quantity);
                    }
                }
                if ($this->getFreePlace($warehouseId) < 0) {
                    throw new \LogicException(
                        "Not enough space on warehouse with id {$warehouseId}.",
                        400
                    );
                }
            }
            $this->dbConnection->commit();
        } catch (\LogicException $e) {
            $this->dbConnection->rollBack();
            throw $e;
        }
    }
    /**
     * @param Transaction[] $transactions
     *
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function removeProducts($transactions)
    {
        $this->dbConnection->beginTransaction();
        try {
            foreach ($transactions as $transaction) {
                $warehouseId = (int)$transaction->getSender();
                $productId = $transaction->getProduct()->getId();
                $quantity = $transaction->getQuantity();
                $todayQuantity = $this->getTodayQuantity($warehouseId, $productId);

                if ($todayQuantity >= 0) {
                    $this->update($warehouseId, $productId, $todayQuantity - $quantity);

                } else {
                    $lastQuantity = $this->getLastQuantity($warehouseId, $productId);
                    if ($lastQuantity >= 0) {
                        $this->insert($warehouseId, $productId, $lastQuantity - $quantity);
                    }
                }

                if ($this->getLastQuantity($warehouseId, $productId) < 0) {
                    throw new \LogicException(
                        "Not enough product with sku {$transaction->getProduct()->getSku()} in warehouse with id {$warehouseId}.",
                        400
                    );
                }
            }
            $this->dbConnection->commit();
        } catch (\LogicException $e) {
            $this->dbConnection->rollBack();
            throw $e;
        }

    }
    /**
     * @param Transaction[] $transactions
     *
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function movementProducts($transactions)
    {
        $this->dbConnection->beginTransaction();
        try {
            $this->removeProducts($transactions);
            $this->addProducts($transactions);
            $this->dbConnection->commit();
        } catch (\LogicException $e) {
            $this->dbConnection->rollBack();
            throw $e;
        }
    }
    /**
     * @param int $warehouseId
     *
     * @return int
     */
    public function getFilling($warehouseId)
    {
        $rows = $this->dbConnection->fetchAll(
            'SELECT s1.productId, s1.quantity, p.size
            FROM State AS s1
            JOIN Products AS p ON p.id = s1.productId
            WHERE warehouseId = ? AND date = (
              SELECT MAX(s2.date)
              FROM State AS s2
              WHERE s1.productId = s2.productId AND warehouseId = ? 
            )',
            [
                $warehouseId,
                $warehouseId
            ]
        );

        $filling = 0;

        foreach ($rows as $row) {
            $filling += (int)$row['size'] * (int)$row['quantity'];
        }

        return $filling;
    }
    /**
     * @param int $warehouseId
     *
     * @return int|null
     */
    public function getFreePlace($warehouseId)
    {
        $row = $this->dbConnection->fetchAssoc(
            'SELECT capacity
            FROM Warehouses
            WHERE id = ?',
            [$warehouseId]
        );

        if ($row === false) {
            return null;
        }
        $freePlace = (int)$row['capacity'] - $this->getFilling($warehouseId);

        return $freePlace;
    }
    /**
     * @param int $warehouseId
     * @param string $date
     *
     * @return array
     */
    public function getResiduesByWarehouse($warehouseId, $date = null)
    {
        if ($date === null) {
            $date = date('Y-m-d');
        }
        $rows = $this->dbConnection->fetchAll(
            'SELECT p.name, p.sku, s1.quantity, p.price
            FROM State AS s1
            JOIN Products AS p ON p.id = s1.productId
            WHERE warehouseId = ? AND s1.quantity > 0 AND date = (
              SELECT MAX(s2.date)
              FROM State AS s2
              WHERE s1.productId = s2.productId AND s2.warehouseId = ? AND s2.date <= ?
            )
            ORDER BY s1.warehouseId',
            [
                $warehouseId,
                $warehouseId,
                $date
            ]
        );

        $residues = [];

        foreach ($rows as $row) {
            $residues[] = [
                'sku' => (int)$row['sku'],
                'quantity' => (int)$row['quantity'],
                'cost' => (float)$row['price'] * (float)$row['quantity']
            ];
        }
        return $residues;
    }
    /**
     * @param int $productId
     * @param string $date
     *
     * @return array
     */
    public function getResiduesByProduct($productId, $date = null)
    {
        if ($date === null) {
            $date = date('Y-m-d');
        }
        $rows = $this->dbConnection->fetchAll(
            'SELECT s1.warehouseId, s1.quantity, p.price
            FROM State AS s1
            JOIN Products AS p ON p.id = s1.productId
            WHERE s1.productId = ? AND s1.quantity > 0 AND date = (
              SELECT MAX(s2.date)
              FROM State AS s2
              WHERE s1.productId = s2.productId AND s1.warehouseId = s2.warehouseId AND s2.date <= ?
            )
            ORDER BY s1.warehouseId',
            [
                $productId,
                $date
            ]
        );
        $residues = [];

        foreach ($rows as $row) {
            $residues[] = [
                'warehouseId' => (int)$row['warehouseId'],
                'quantity' => (int)$row['quantity'],
                'cost' => (float)$row['price'] * (float)$row['quantity']
            ];
        }
        return $residues;

    }
}
