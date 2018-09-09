<?php
/**
 * Created by PhpStorm.
 * User: Tamara
 * Date: 31.08.2018
 * Time: 19:45
 */

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
        return $this->warehouseRepository->getAll($user);
    }

    /**
     * @param int $id
     * @param User $user
     *
     * @return Warehouse|null
     */
    public function getOne($id, User $user)
    {
        return $this->warehouseRepository->findById($id, $user);
    }

    /**
     * @param int $address
     * @param User $user
     *
     * @return Warehouse|null
     */
    public function getOneByAddress($address, User $user)
    {
        return $this->warehouseRepository->findByAddress($address, $user);
    }

    /**
     * @param Warehouse $warehouse
     * @param User $user
     */
    public function add(Warehouse $warehouse, User $user)
    {
        $this->warehouseRepository->insert($warehouse, $user);
    }

    /**
     * @param int $warehouseId
     *
     * @throws \Doctrine\DBAL\Exception\InvalidArgumentException
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
        return $this->warehouseRepository->update($warehouse, $user);
    }
}