<?php

namespace App\Service;

use App\Model\Transaction;
use App\Repository\StateRepository;

class StateService
{
    /**
     * @var StateRepository
     */
    private $stateRepository;

    /**
     * StateService constructor.
     *
     * @param StateRepository $stateRepository
     */
    public function __construct(StateRepository $stateRepository) {

        $this->stateRepository = $stateRepository;
    }

    /**
     * @param Transaction[] $transactions
     * @param int $warehouseId
     */
    public function addProducts($transactions, $warehouseId)
    {
        $this->stateRepository->addProducts($transactions, $warehouseId);
    }

    /**
     * @param Transaction[] $transactions
     * @param int $warehouseId
     */
    public function removeProducts($transactions, $warehouseId)
    {
        $this->stateRepository->removeProducts($transactions, $warehouseId);
    }

    /**
     * @param $warehouseId
     * @param $productId
     * @param $quantity
     * @param $newWarehouseId
     */
    public function movementProducts($warehouseId, $productId, $quantity, $newWarehouseId)
    {
        $this->stateRepository->movementProducts($warehouseId, $productId, $quantity, $newWarehouseId);
    }

    /**
     * @param int $warehouseId
     *
     * @return int
     */
    public function getFilling($warehouseId)
    {
        return $this->stateRepository->getFilling($warehouseId);
    }

    /**
     * @param int $warehouseId
     * @param int $productId
     *
     * @return int
     */
    public function quantityProductInWarehouse($warehouseId, $productId)
    {
        return $this->stateRepository->getLastQuantity($warehouseId, $productId);
    }

    /**
     * @param $warehouseId
     *
     * @return array
     */
    public function getResiduesByWarehouse($warehouseId)
    {
        return $this->stateRepository->getResiduesByWarehouse($warehouseId);
    }

    public function getResiduesByWarehouseForDate($warehouseId, $date)
    {
        return $this->stateRepository->getResiduesByWarehouseForDate($warehouseId, $date);
    }

    public function getResiduesByProduct($productId)
    {
        return $this->stateRepository->getResiduesByProduct($productId);
    }

    public function getResiduesByProductForDate($productId, $date)
    {
        return $this->stateRepository->getResiduesByProductForDate($productId, $date);
    }
}