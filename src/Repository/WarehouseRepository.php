<?php

namespace App\Repository;

use App\Model\User;
use App\Model\Warehouse;

class WarehouseRepository extends AbstractRepository
{
    /**
     * @param int $id
     * @param User $user
     *
     * @return Warehouse|null
     */
    public function findById($id, $user)
    {
        $row = $this->dbConnection->fetchAssoc(
            'SELECT * FROM Warehouses WHERE id = ? AND userId = ?',
            [$id, $user->getId()]
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
     * @param User $user
     *
     * @return Warehouse|null
     */
    public function findByAddress($address, $user)
    {
        $row = $this->dbConnection->fetchAssoc(
            'SELECT * FROM Warehouses WHERE address = ? AND userId = ?',
            [
                $address,
                $user->getId()
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
     *
     * @return int
     */
    public function getWarehouseCountById(Warehouse $warehouse)
    {
        return (int)$this->dbConnection->fetchColumn(
            'SELECT count(id) FROM ' . $this->tableName . ' WHERE id = ?',
            [$warehouse->getId()]
        );
    }
    /**
     * @param Warehouse $warehouse
     * @param User $user
     */
    public function insert($warehouse, $user)
    {
        $values = [
            'address' => $warehouse->getAddress(),
            'capacity' => $warehouse->getCapacity(),
            'userId' => $user->getId()
        ];

        $this->dbConnection->insert(
            'Warehouses',
            $values
        );

        $warehouse->setId((int)$this->dbConnection->lastInsertId());
    }
    /**
     * @param int $warehouseId
     *
     * @throws \Doctrine\DBAL\Exception\InvalidArgumentException
     */
    public function delete($warehouseId)
    {
        $this->dbConnection->delete(
            'Warehouses',
            ['id' => $warehouseId]
        );
    }
    /**
     * @param Warehouse $warehouse
     * @param User $user
     *
     * @return Warehouse|null
     */
    public function update($warehouse, $user)
    {
        $values = [];

        if ($warehouse->getAddress() !== null) {
            $values['address'] =  $warehouse->getAddress();
        }

        if ($warehouse->getCapacity() !== null) {
            $values['capacity'] = $warehouse->getCapacity();
        }

        $this->dbConnection->update(
            'Warehouses',
            $values,
            [
                'id' => $warehouse->getId(),
                'userId' => $user->getId()
            ]
        );

        return $this->findById($warehouse->getId(), $user);
    }
    /**
     * @param User $user
     *
     * @return Warehouse[]
     */
    public function getAll($user)
    {
        $rows = $this->dbConnection->fetchAll(
            'SELECT * FROM Warehouses WHERE userId = ?',
            [$user->getId()]
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