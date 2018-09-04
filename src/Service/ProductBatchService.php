<?php

namespace App\Service;

use App\Model\ProductBatch;
use App\Model\Warehouse;
use App\Repository\ProductBatchRepository;

class ProductBatchService
{
    /**
     * @var ProductBatchRepository
     */
    private $productBatchRepository;

    /**
     * ProductBatchService constructor.
     *
     * @param ProductBatchRepository $productBatchRepository
     */
    public function __construct(ProductBatchRepository $productBatchRepository) {

        $this->productBatchRepository = $productBatchRepository;
    }

    /**
     * @param ProductBatch $batch
     * @param Warehouse $warehouse
     */
    public function add(ProductBatch $batch, Warehouse $warehouse)
    {
        return $this->productBatchRepository->insert($batch, $warehouse);
    }

    /**
     * @param ProductBatch $batch
     * @param Warehouse $warehouseId
     */
    public function remove(ProductBatch $batch, Warehouse $warehouseId)
    {
        return $this->productBatchRepository->delete($batch, $warehouseId);
    }

    /**
     * @param ProductBatch $batch
     * @param Warehouse $warehouse
     * @param Warehouse $newWarehouse
     */
    public function movement(ProductBatch $batch, Warehouse $warehouse, Warehouse $newWarehouse)
    {
        return $this->productBatchRepository->movement($batch, $warehouse, $newWarehouse);
    }

}