<?php

namespace App\Service;

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
     * @param int $productId
     * @param int $warehouseId
     * @param int $quantity
     */
    public function addProducts($warehouseId, $productId, $quantity)
    {
        $this->stateRepository->addProducts($warehouseId, $productId, $quantity);
    }

    /**
     * @param int $warehouseId
     * @param int $productId
     * @param int $quantity
     * 
     * @throws \Doctrine\DBAL\Exception\InvalidArgumentException
     */
    public function removeProducts($warehouseId, $productId, $quantity)
    {
        $this->stateRepository->removeProducts($warehouseId, $productId, $quantity);
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