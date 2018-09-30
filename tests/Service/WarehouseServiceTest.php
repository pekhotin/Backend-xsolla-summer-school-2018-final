<?php

namespace Tests\Service;

use App\Model\Warehouse;
use App\Repository\WarehouseRepository;
use App\Service\WarehouseService;
use Tests\XmlTestCase;

class WarehouseServiceTest extends XmlTestCase
{
    /**
     * @var WarehouseService
     */
    static private $warehouseService = null;

    /**
     * @dataProvider dataGetOne
     */
    public function testGetOne($id, $userId, $expectedValue)
    {
        if (self::$warehouseService === null) {
            self::$warehouseService = new WarehouseService(new WarehouseRepository($this->dbal));
        }

        $value = self::$warehouseService->getOne($id, $userId);
        $this->assertEquals($value, $expectedValue);
    }

    public function dataGetOne()
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
     * @dataProvider dataGetOneByAddress
     */
    public function testGetOneByAddress($address, $userId, $expectedValue)
    {
        if (self::$warehouseService === null) {
            self::$warehouseService = new WarehouseService(new WarehouseRepository($this->dbal));
        }

        $value = self::$warehouseService->getOneByAddress($address, $userId);
        $this->assertEquals($value, $expectedValue);
    }

    public function dataGetOneByAddress()
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

    public function testRemove()
    {
        if (self::$warehouseService === null) {
            self::$warehouseService = new WarehouseService(new WarehouseRepository($this->dbal));
        }
        $value = self::$warehouseService->remove(7);
        $expectedValue = self::$warehouseService->getOne(7, 1);
        $this->assertEquals($value, $expectedValue);
    }

    public function testUpdate()
    {
        if (self::$warehouseService === null) {
            self::$warehouseService = new WarehouseService(new WarehouseRepository($this->dbal));
        }

        $warehouse = new Warehouse(
            1,
            'Perm Lenina 1',
            250000
        );
        $value = self::$warehouseService->update($warehouse, 1);
        $this->assertEquals($value, $warehouse);
    }

    public function testGetAll()
    {
        if (self::$warehouseService === null) {
            self::$warehouseService = new WarehouseService(new WarehouseRepository($this->dbal));
        }

        $values = self::$warehouseService->getAll(2);
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

        $values = self::$warehouseService->getAll(5);
        $expectedValues = [];

        $this->assertEquals($values, $expectedValues);
    }

    public function testAdd()
    {
        if (self::$warehouseService === null) {
            self::$warehouseService = new WarehouseService(new WarehouseRepository($this->dbal));
        }
        $warehouse = new Warehouse(
            9,
            'Perm Zvezdnaya 23',
            255000
        );
        $value = self::$warehouseService->add($warehouse, 1);
        $this->assertEquals($value, $warehouse);
    }
}