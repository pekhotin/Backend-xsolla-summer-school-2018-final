<?php

namespace App\Repository;

use App\Model\Warehouse;
use Doctrine\DBAL\Connection;

class WarehouseRepository extends AbstractRepository
{
    /**
     * @var string
     */
    private $tableName;

    /**
     * WarehouseRepository constructor.
     *
     * @param Connection $dbConnection
     */
    public function __construct(Connection $dbConnection)
    {
        parent::__construct($dbConnection);
        $this->tableName = 'Warehouses';
    }

    /**
     * @param int $id
     *
     * @return Warehouse
     *
     * @throws \LogicException
     */
    public function findById($id)
    {
        $row = $this->dbConnection->fetchAssoc(
            'SELECT * FROM ' . $this->tableName . ' WHERE id = ?',
            [$id]
        );

        if ($row == null) {
            throw new \LogicException(__CLASS__ . " findById() warehouse with id {$id} not found!", 404);
        }

        $productBatchRepository = new ProductBatchRepository($this->dbConnection);

        $warehouse = new Warehouse(
            $row['id'],
            $row['address'],
            $row['capacity']
        );

        $productBatches = $productBatchRepository->getAllInWarehouse($warehouse);
        $warehouse->setProductBatches($productBatches);

        return $warehouse;
    }

    /**
     * @param Warehouse $warehouse
     *
     * @return int
     */
    public function getWarehouseCountByAddress(Warehouse $warehouse)
    {
        return (int)$this->dbConnection->fetchColumn(
            'SELECT count(address) FROM ' . $this->tableName . ' WHERE address = ?',
            [$warehouse->getAddress()]
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
     *
     * @return int
     */
    public function getFilling(Warehouse $warehouse)
    {
        $productBatchRepository = new ProductBatchRepository($this->dbConnection);
        $batches = $productBatchRepository->getAllInWarehouse($warehouse);

        $filling = 0;

        foreach ($batches as $batch)
        {
            $filling += $batch->getSize();
        }

        return $filling;
    }

    /**
     * @param Warehouse $warehouse
     *
     * @return Warehouse
     *
     * @throws \LogicException
     */
    public function insert(Warehouse $warehouse)
    {
        if ($this->getWarehouseCountByAddress($warehouse) > 0) {
            throw new \LogicException(__CLASS__ . " insert() warehouse with address {$warehouse ->getAddress()} already exists!");
        }

        $values = [
            'address' => $warehouse->getAddress(),
            'capacity' => $warehouse->getCapacity()
        ];

        $this->dbConnection->insert(
            $this->tableName,
            $values
        );

        $warehouse->setId($this->dbConnection->lastInsertId());

        return $warehouse;
    }

    /**
     * @param Warehouse $warehouse
     *
     * @return Warehouse
     *
     * @throws \Doctrine\DBAL\DBALException
     * @throws \LogicException
     */
    public function delete(Warehouse $warehouse)
    {
        if ($this->getWarehouseCountById($warehouse) == 0){
            throw new \LogicException(__CLASS__ . ' delete() id does not exists!', 404);
        }

        //добавить проверку на связные перемещения
        $this->dbConnection->delete(
            $this->tableName,
            ['id' => $warehouse->getId()]
        );

        return $warehouse;
    }

    /**
     * @param Warehouse $warehouse
     *
     * @return Warehouse
     *
     * @throws \LogicException
     */
    public function update(Warehouse $warehouse)
    {
        if ($this->getWarehouseCountById($warehouse) == 0){
            throw new \LogicException(__CLASS__ . ' update() id does not exists!', 404);
        }

        $values = [];

        if ($warehouse->getAddress() !== null) {

            if ($this->getWarehouseCountByAddress($warehouse) > 0){
                throw new \LogicException(__CLASS__ . " update() warehouse with address {$warehouse ->getAddress()} already exists!");
            }

            $values['address'] =  $warehouse->getAddress();
        }

        if ($warehouse->getCapacity() !== null) {

            if ($this->getFilling($warehouse) > $warehouse->getCapacity())
                throw new \LogicException(__CLASS__ .  ' update() warehouse filling more than its capacity!');

            $values['capacity'] = $warehouse->getCapacity();
        }

        if (count($values) == 0) {
            throw new \LogicException(__CLASS__ .  ' update() updates parameters are not found!');
        }

        $this->dbConnection->update(
            $this->tableName,
            $values,
            ['id' => $warehouse->getId()]
        );

        return $this->findById($warehouse->getId());
    }

    /**
     * @return Warehouse[]
     */
    public function getAll()
    {
        $rows = $this->dbConnection->fetchAll('SELECT * FROM ' . $this->tableName);

        $warehouses = [];
        $productBatchRepository = new ProductBatchRepository($this->dbConnection);

        foreach ($rows as $row) {
            $warehouses[] = new Warehouse(
                $row['id'],
                $row['address'],
                $row['capacity']
            );

            end($warehouses)->setProductBatches($productBatchRepository->getAllInWarehouse(end($warehouses)));
        }

        return $warehouses;
    }
}