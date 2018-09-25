<?php

namespace App\Repository;

use App\Model\Warehouse;

class WarehouseRepository extends AbstractRepository
{
    /**
     * @param int $id
     * @param int $userId
     *
     * @return Warehouse|null
     */
    public function findById($id, $userId)
    {
        $row = $this->dbConnection->fetchAssoc(
            'SELECT * FROM Warehouses WHERE id = ? AND userId = ?',
            [$id, $userId]
        );

        if ($row === false) {
            return null;
        }

        $warehouse = new Warehouse(
            (int)$row['id'],
            (string)$row['address'],
            (int)$row['capacity']
        );

        return $warehouse;
    }
    /**
     * @param string $address
     * @param int $userId
     *
     * @return Warehouse|null
     */
    public function findByAddress($address, $userId)
    {
        $row = $this->dbConnection->fetchAssoc(
            'SELECT * FROM Warehouses WHERE address = ? AND userId = ?',
            [
                $address,
                $userId
            ]
        );

        if ($row === false) {
            return null;
        }

        return new Warehouse(
            (int)$row['id'],
            (string)$row['address'],
            (int)$row['capacity']
        );

    }
    /**
     * @param Warehouse $warehouse
     * @param int $userId
     * @return Warehouse
     */
    public function insert($warehouse, $userId)
    {
        $values = [
            'address' => $warehouse->getAddress(),
            'capacity' => $warehouse->getCapacity(),
            'userId' => $userId
        ];

        $this->dbConnection->insert(
            'Warehouses',
            $values
        );

        $warehouse->setId((int)$this->dbConnection->lastInsertId());
        return $warehouse;
    }
    /**
     * @param int $warehouseId
     *
     * @return null
     *
     * @throws \Doctrine\DBAL\Exception\InvalidArgumentException
     */
    public function delete($warehouseId)
    {
        $this->dbConnection->delete(
            'Warehouses',
            ['id' => $warehouseId]
        );
        return null;
    }
    /**
     * @param Warehouse $warehouse
     * @param int $userId
     *
     * @return Warehouse|null
     */
    public function update($warehouse, $userId)
    {
        $this->dbConnection->update(
            'Warehouses',
            [
                'address' => $warehouse->getAddress(),
                'capacity' => $warehouse->getCapacity()
            ],
            [
                'id' => $warehouse->getId()
            ]
        );

        return $this->findById($warehouse->getId(), $userId);
    }
    /**
     * @param int $userId
     *
     * @return Warehouse[]
     */
    public function getAll($userId)
    {
        $rows = $this->dbConnection->fetchAll(
            'SELECT * FROM Warehouses WHERE userId = ?',
            [$userId]
        );

        $warehouses = [];

        foreach ($rows as $row) {
            $warehouses[] = new Warehouse(
                (int)$row['id'],
                (string)$row['address'],
                (int)$row['capacity']
            );
        }

        return $warehouses;
    }
}
