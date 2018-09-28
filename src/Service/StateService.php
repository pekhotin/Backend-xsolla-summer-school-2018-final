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
     *
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function addProducts($transactions)
    {
        return $this->stateRepository->addProducts($transactions);
    }
    /**
     * @param Transaction[] $transactions
     *
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function removeProducts($transactions)
    {
        $this->stateRepository->removeProducts($transactions);
    }
    /**
     * @param $transactions
     *
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function movementProducts($transactions)
    {
        return $this->stateRepository->movementProducts($transactions);
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
     * @param int $warehouseId
     *
     * @return array
     */
    public function getResiduesByWarehouse($warehouseId)
    {
        return $this->stateRepository->getResiduesByWarehouse($warehouseId);
    }
    /**
     * @param int $warehouseId
     * @param string $date
     *
     * @return array
     */
    public function getResiduesByWarehouseForDate($warehouseId, $date)
    {
        return $this->stateRepository->getResiduesByWarehouse($warehouseId, $date);
    }
    /**
     * @param int $productId
     *
     * @return array
     */
    public function getResiduesByProduct($productId)
    {
        return $this->stateRepository->getResiduesByProduct($productId);
    }
    /**
     * @param int $productId
     * @param string $date
     *
     * @return array
     */
    public function getResiduesByProductForDate($productId, $date)
    {
        return $this->stateRepository->getResiduesByProduct($productId, $date);
    }
}

