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
     * @param User $user
     *
     * @return Warehouse[]
     */
    public function getAll(User $user)
    {
        return $this->warehouseRepository->getAll($user->getId());
    }
    /**
     * @param int $id
     * @param int $userId
     *
     * @return Warehouse|null
     */
    public function getOne($id, int $userId)
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
     * @param User $user
     */
    public function add(Warehouse $warehouse, User $user)
    {
        $this->warehouseRepository->insert($warehouse, $user->getId());
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
     * @param User $user
     *
     * @return Warehouse
     */
    public function update(Warehouse $warehouse, User $user)
    {
        return $this->warehouseRepository->update($warehouse, $user->getId());
    }
}
