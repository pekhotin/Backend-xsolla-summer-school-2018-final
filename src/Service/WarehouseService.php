<?php
/**
 * Created by PhpStorm.
 * User: Tamara
 * Date: 31.08.2018
 * Time: 19:45
 */

namespace App\Service;

use App\Model\Warehouse;
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
     * @return Warehouse[]
     */
    public function getAll()
    {
        return $this->warehouseRepository->getAll();
    }

    /**
     * @param int $id
     *
     * @return Warehouse|null
     */
    public function getOne($id)
    {
        return $this->warehouseRepository->findById($id);
    }

    /**
     * @param Warehouse $warehouse
     *
     * @return Warehouse
     */
    public function add(Warehouse $warehouse)
    {
        return $this->warehouseRepository->insert($warehouse);
    }

    /**
     * @param Warehouse $warehouse
     * @return Warehouse
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function remove(Warehouse $warehouse)
    {
        return $this->warehouseRepository->delete($warehouse);
    }

    /**
     * @param Warehouse $warehouse
     *
     * @return Warehouse
     */
    public function update(Warehouse $warehouse)
    {
        return $this->warehouseRepository->update($warehouse);
    }
}