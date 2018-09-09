<?php

namespace App\Repository;

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
        return (int)$this->dbConnection->fetchColumn(
            'SELECT quantity FROM State WHERE warehouseId = ? AND productId = ? AND date = ?',
            [
                $warehouseId,
                $productId,
                date('Y-m-d')
            ]
        );

    }

    public function getLastQuantity($warehouseId, $productId)
    {
        return (int)$this->dbConnection->fetchColumn(
            'SELECT quantity FROM State WHERE warehouseId = ? AND productId = ? ORDER BY date DESC',
            [
                $warehouseId,
                $productId
            ]
        );

    }

    /**
     * @param int $warehouseId
     * @param int $productId
     * @param int $quantity
     */
    public function update($warehouseId, $productId, $quantity)
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
     */
    public function insert($warehouseId, $productId, $quantity)
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
     * @param int $warehouseId
     * @param int $productId
     *
     * @throws \Doctrine\DBAL\Exception\InvalidArgumentException
     */
    public function delete($warehouseId, $productId)
    {
        $this->dbConnection->delete(
            'State',
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
     */
    public function addProducts($warehouseId, $productId, $quantity)
    {
        $todayQuantity = $this->getTodayQuantity($warehouseId, $productId);

        if ($todayQuantity > 0) {
            $this->update($warehouseId, $productId,$quantity + $todayQuantity);
        } else {
            $lastQuantity = $this->getLastQuantity($warehouseId, $productId);
            if ($lastQuantity > 0){
                $this->insert($warehouseId, $productId, $lastQuantity + $quantity);
            } else {
                $this->insert($warehouseId, $productId, $quantity);
            }
        }
    }

    /**
     * @param $warehouseId
     * @param $productId
     * @param $quantity
     */
    public function removeProducts($warehouseId, $productId, $quantity)
    {
        $todayQuantity = $this->getTodayQuantity($warehouseId, $productId);

        if ($todayQuantity > 0) {
            $this->update($warehouseId,$productId, $todayQuantity - $quantity);
        } else {
            $lastQuantity = $this->getLastQuantity($warehouseId, $productId);
            if ($lastQuantity > 0) {
                $this->insert($warehouseId, $productId, $lastQuantity - $quantity);
            }
        }
    }

    /**
     * @param int $warehouseId
     * @param int $productId
     * @param int $quantity
     * @param int $newWarehouseId
     *
     * @throws \Doctrine\DBAL\Exception\InvalidArgumentException
     */
    public function movementProducts($warehouseId, $productId, $quantity, $newWarehouseId)
    {
        $this->removeProducts($warehouseId, $productId, $quantity);
        $this->addProducts($newWarehouseId, $productId, $quantity);
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
}