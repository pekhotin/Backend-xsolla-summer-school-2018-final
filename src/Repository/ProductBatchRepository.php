<?php

namespace App\Repository;

use App\Model\ProductBatch;
use App\Model\Warehouse;
use Doctrine\DBAL\Connection;

class ProductBatchRepository extends AbstractRepository
{
    /**
     * @var string
     */
    private $tableName;

    /**
     * ProductBatchRepository constructor.
     *
     * @param Connection $dbConnection
     */
    public function __construct(Connection $dbConnection)
    {
        parent::__construct($dbConnection);
        $this->tableName = 'ProductBatches';
    }


    public function findProductBatch($productId, $warehouseId)
    {
        $row = $this->dbConnection->fetchAssoc (
            'SELECT * FROM ' . $this->tableName . ' WHERE idProduct = ? AND idWarehouse = ?',
            [$productId, $warehouseId]
        );

        if ($row == null) {
            return false;
        }

        $productRepository = new ProductRepository($this->dbConnection);
        $product = $productRepository->findById($row['idProduct']);

        return new ProductBatch(
            $row['id'],
            $product,
            $row['quantity']
        );
    }

    /**
     * @param ProductBatch $batch
     *
     * @return int
     */
    public function getProductBatchesCount(ProductBatch $batch)
    {
        return (int)$this->dbConnection->fetchColumn(
            'SELECT count(id) FROM ' . $this->tableName . ' WHERE id = ?',
            [$batch->getId()]
        );
    }

    /**
     * @param ProductBatch $batch
     * @param Warehouse $warehouse
     */
    public function insert(ProductBatch $batch, Warehouse $warehouse)
    {
        //сделать проверку на размер склада

        $productBatch = $this->findProductBatch($batch->getProduct()->getId(), $warehouse->getId());

        if ($productBatch === false) {
            $values = [
                'idProduct' => $batch->getProduct()->getId(),
                'quantity' => $batch->getQuantity(),
                'idWarehouse' => $warehouse->getId()
            ];

            $this->dbConnection->insert($this->tableName, $values);

        } else {
            $newQuantity = $batch->getQuantity() + $productBatch->getQuantity();

            $values['quantity'] = $newQuantity;

            $this->dbConnection->update(
                $this->tableName,
                $values,
                ['id' => $productBatch->getId()]
            );
        }
    }

    public function delete(ProductBatch $batch, Warehouse $warehouse)
    {
        $productBatch = $this->findProductBatch($batch->getProduct()->getId(), $warehouse->getId());

        if ($productBatch == false) {
            throw new \LogicException(__CLASS__ . " delete() product with id {$batch->getProduct()->getId()} not exist warehouse!", 404);
        }

        if ($productBatch->getQuantity() > $batch->getQuantity()) {

            $newQuantity = $productBatch->getQuantity() - $batch->getQuantity();

            $this->dbConnection->update(
                $this->tableName,
                ['quantity' => $newQuantity],
                ['id' => $productBatch->getId()]
            );
        } elseif ($productBatch->getQuantity() == $batch->getQuantity()){

            $this->dbConnection->delete(
                $this->tableName,
                ['id' => $productBatch->getId()]
            );
        } else {
            throw new \LogicException(__CLASS__ . ' delete() deficiency products in warehouse!', 400);
        }
    }

    /**
     * @param ProductBatch $batch
     * @param Warehouse $warehouse
     * @param Warehouse $newWarehouse
     */
    public function movement($batch, $warehouse, $newWarehouse)
    {
        $productBatch = $this->findProductBatch($batch->getProduct()->getId(), $warehouse->getId());

        if ($productBatch == false) {
            throw new \LogicException(__CLASS__ . " delete() product with id {$batch->getProduct()->getId()}not exist warehouse!", 404);
        }

        $this->delete($batch, $warehouse);
        $this->insert($batch, $newWarehouse);
    }

    /**
     * @param Warehouse $warehouse
     *
     * @return ProductBatch[]
     */
    public function getAllInWarehouse(Warehouse $warehouse)
    {
        //делать ли проверку?
        $rows = $this->dbConnection->fetchAll(
            'SELECT * FROM ' . $this->tableName . ' WHERE idWarehouse = ?',
            [$warehouse->getId()]
        );

        $productBatches = [];
        $productRepository = new ProductRepository($this->dbConnection);

        foreach ($rows as $row) {
            $productBatches[] = new ProductBatch(
                $row['id'],
                $productRepository->findById($row['idProduct']),
                $row['quantity']
            );
        }

        return $productBatches;
    }
}