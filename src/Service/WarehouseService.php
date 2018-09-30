<?php

namespace App\Service;

use App\Model\Warehouse;
use App\Model\User;
use App\Repository\WarehouseRepository;

class WarehouseService
{
    /**
     * @var WarehouseRepository
     */
    private $warehouseRepository;
    /**
     * WarehouseService constructor.
     *
     * @param WarehouseRepository $warehouseRepository
     */
    public function __construct(WarehouseRepository $warehouseRepository) {
        $this->warehouseRepository = $warehouseRepository;
    }
    /**
     * @param int $userId
     *
     * @return Warehouse[]
     */
    public function getAll(int $userId)
    {
        return $this->warehouseRepository->getAll($userId);
    }
    /**
     * @param int $id
     * @param int $userId
     *
     * @return Warehouse|null
     */
    public function getOne(int $id, int $userId)
    {
        return $this->warehouseRepository->findById($id, $userId);
    }
    /**
     * @param string $address
     * @param int $userId
     *
     * @return Warehouse|null
     */
    public function getOneByAddress(string $address, $userId)
    {
        return $this->warehouseRepository->findByAddress($address, $userId);
    }
    /**
     * @param Warehouse $warehouse
     * @param int $userId
     *
     * @return Warehouse
     */
    public function add(Warehouse $warehouse, int $userId)
    {
        return $this->warehouseRepository->insert($warehouse, $userId);
    }
    /**
     * @param int $warehouseId
     *
     * @throws \Doctrine\DBAL\Exception\InvalidArgumentException
     *
     * @return null
     */
    public function remove($warehouseId)
    {
        return $this->warehouseRepository->delete($warehouseId);
    }
    /**
     * @param Warehouse $warehouse
     * @param int $userId
     *
     * @return Warehouse
     */
    public function update(Warehouse $warehouse, int $userId)
    {
        return $this->warehouseRepository->update($warehouse, $userId);
    }
}
