<?php

namespace Tests\Repository;

use App\Model\Warehouse;
use App\Repository\WarehouseRepository;
use Tests\XmlTestCase;

class WarehouseRepositoryTest extends XmlTestCase
{
    /**
     * @var WarehouseRepository
     */
    static private $warehouseRepository = null;

    /**
     * @dataProvider dataFindById
     */
    public function testFindById($id, $userId, $expectedValue)
    {
        if (self::$warehouseRepository === null) {
            self::$warehouseRepository = new WarehouseRepository($this->dbal);
        }

        $value = self::$warehouseRepository->findById($id, $userId);
        $this->assertEquals($value, $expectedValue);
    }

    public function dataFindById()
    {
        $result = [];
        $result[] = [
            1,
            1,
            new Warehouse(
                1,
                "Perm Lenina 1",
                100000
            )
        ];
        $result[] = [
            4,
            1,
            new Warehouse(
                4,
                'Perm Kompros 2',
                150000
            )
        ];
        $result[] = [
            6,
            2,
            new Warehouse(
                6,
                'Perm Plehanova 1',
                100000
            )
        ];
        $result[] = [
            6,
            1,
            null
        ];
        $result[] = [
            1,
            2,
            null
        ];

        return $result;
    }
    /**
     * @dataProvider dataFindByAddress
     */
    public function testFindByAddress($address, $userId, $expectedValue)
    {
        if (self::$warehouseRepository === null) {
            self::$warehouseRepository = new WarehouseRepository($this->dbal);
        }

        $value = self::$warehouseRepository->findByAddress($address, $userId);
        $this->assertEquals($value, $expectedValue);
    }

    public function dataFindByAddress()
    {
        $result = [];
        $result[] = [
            'Perm Lenina 1',
            1,
            new Warehouse(
                1,
                'Perm Lenina 1',
                100000
            )
        ];
        $result[] = [
            'Perm Kompros 2',
            1,
            new Warehouse(
                4,
                'Perm Kompros 2',
                150000
            )
        ];
        $result[] = [
            'Perm Plehanova 1',
            2,
            new Warehouse(
                6,
                'Perm Plehanova 1',
                100000
            )
        ];
        $result[] = [
            6,
            1,
            null
        ];
        $result[] = [
            1,
            2,
            null
        ];

        return $result;
    }

    public function testDelete()
    {
        if (self::$warehouseRepository === null) {
            self::$warehouseRepository = new WarehouseRepository($this->dbal);
        }
        $value = self::$warehouseRepository->delete(7);
        $expectedValue = self::$warehouseRepository->findById(7, 1);
        $this->assertEquals($value, $expectedValue);
    }

    public function testUpdate()
    {
        if (self::$warehouseRepository === null) {
            self::$warehouseRepository = new WarehouseRepository($this->dbal);
        }

        $warehouse = new Warehouse(
            1,
            'Perm Lenina 1',
            250000
        );
        $value = self::$warehouseRepository->update($warehouse, 1);
        $this->assertEquals($value, $warehouse);
    }

    public function testGetAll()
    {
        if (self::$warehouseRepository === null) {
            self::$warehouseRepository = new WarehouseRepository($this->dbal);
        }

        $values = self::$warehouseRepository->getAll(2);
        $expectedValues[] = new Warehouse(
            5,
            'Perm Lenina 1',
            10000
        );
        $expectedValues[] = new Warehouse(
            6,
            'Perm Plehanova 1',
            100000
        );
        $expectedValues[] = new Warehouse(
            8,
            'Perm Kompros 12',
            100000
        );
        $this->assertEquals($values, $expectedValues);

        $values = self::$warehouseRepository->getAll(5);
        $expectedValues = [];

        $this->assertEquals($values, $expectedValues);
    }

    public function testInsert()
    {
        if (self::$warehouseRepository === null) {
            self::$warehouseRepository = new WarehouseRepository($this->dbal);
        }
        $warehouse = new Warehouse(
            9,
            'Perm Zvezdnaya 23',
            255000
        );
        $value = self::$warehouseRepository->insert($warehouse, 1);
        $this->assertEquals($value, $warehouse);
    }
}