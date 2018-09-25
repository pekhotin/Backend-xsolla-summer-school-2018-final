<?php

namespace Tests\Repository;

use App\Repository\StateRepository;
use Tests\XmlTestCase;

class StateRepositoryTest extends XmlTestCase
{
    /**
     * @var StateRepository
     */
    private static $stateRepository = null;

    /**
     * @dataProvider dataGetTodayQuantity
     */
    public function testGetTodayQuantity($warehouseId, $productId, $expectedValue)
    {
        if (self::$stateRepository === null) {
            self::$stateRepository = new StateRepository($this->dbal);
        }

        $value = self::$stateRepository->getTodayQuantity($warehouseId, $productId);
        $this->assertEquals($value, $expectedValue);
    }

    public function dataGetTodayQuantity()
    {
        return [
            [1, 8, 250],
            [2, 1, -1],
            [2, 11, -1],
            [5, 2, -1],
            [4, 1, -1]
        ];
    }

    /**
     * @dataProvider dataLastTodayQuantity
     */
    public function testGetLastQuantity($warehouseId, $productId, $expectedValue)
    {
        if (self::$stateRepository === null) {
            self::$stateRepository = new StateRepository($this->dbal);
        }

        $value = self::$stateRepository->getLastQuantity($warehouseId, $productId);
        $this->assertEquals($value, $expectedValue);
    }

    public function dataLastTodayQuantity()
    {
        return [
            [1, 8, 250],
            [2, 1, 100],
            [2, 11, -1],
            [5, 2, 700],
            [4, 1, -1],
            [3, 3, 500],
            [6, 2, 200],
            [7, 1, -1]
        ];
    }

}