<?php

namespace Tests\Service;

use App\Model\Product;
use App\Model\Transaction;
use App\Repository\StateRepository;
use App\Service\StateService;
use Tests\XmlTestCase;

class StateServiceTest extends XmlTestCase
{
    /**
     * @var StateService
     */
    static private $stateService = null;
    /**
     * @dataProvider dataQuantityProductInWarehouse
     */
    public function testQuantityProductInWarehouse($warehouseId, $productId, $expectedValue)
    {
        if (self::$stateService === null) {
            self::$stateService = new StateService(new StateRepository($this->dbal));
        }

        $value = self::$stateService->quantityProductInWarehouse($warehouseId, $productId);
        $this->assertEquals($value, $expectedValue);
    }

    public function dataQuantityProductInWarehouse()
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
    /**
     * @dataProvider dataGetFilling
     */
    public function testGetFilling($warehouseId, $expectedValue)
    {
        if (self::$stateService === null) {
            self::$stateService = new StateService(new StateRepository($this->dbal));
        }

        $value = self::$stateService->getFilling($warehouseId);
        $this->assertEquals($value, $expectedValue);
    }

    public function dataGetFilling()
    {
        return [
            [1, 1250],
            [3, 2950],
            [4, 0],
            [5, 2100],
            [7, 0],
            [8, 0],
            [16, 0]
        ];
    }
    /**
     * @dataProvider dataGetResiduesByWarehouse
     */
    public function testGetResiduesByWarehouse($warehouseId, $expectedValue)
    {
        if (self::$stateService === null) {
            self::$stateService = new StateService(new StateRepository($this->dbal));
        }

        $value = self::$stateService->getResiduesByWarehouse($warehouseId);
        $this->assertEquals($value, $expectedValue);
    }

    public function dataGetResiduesByWarehouse()
    {
        $values = [
            [
                'sku' => 114,
                'quantity' => 100,
                'cost' => 20000
            ],
            [
                'sku' => 785,
                'quantity' => 250,
                'cost' => 12500
            ]
        ];

        $result [] = [
            1,
            $values
        ];

        $result [] = [
            4,
            []
        ];

        $values = [
            [
                'sku' => 5555,
                'quantity' => 700,
                'cost' => 161000
            ]
        ];

        $result [] = [
            5,
            $values

        ];

        $result [] = [
            15,
            []

        ];

        return $result;
    }
    /**
     * @dataProvider dataGetResiduesByWarehouseForDate
     */
    public function testGetResiduesByWarehouseForDate($warehouseId, $date, $expectedValue)
    {
        if (self::$stateService === null) {
            self::$stateService = new StateService(new StateRepository($this->dbal));
        }

        $value = self::$stateService->getResiduesByWarehouseForDate($warehouseId, $date);
        $this->assertEquals($value, $expectedValue);
    }

    public function dataGetResiduesByWarehouseForDate()
    {
        $values = [
            [
                'sku' => 114,
                'quantity' => 80,
                'cost' => 16000
            ],
            [
                'sku' => 785,
                'quantity' => 50,
                'cost' => 2500
            ]
        ];
        $result [] = [
            1,
            '2018-09-01',
            $values

        ];

        $values = [
            [
                'sku' => 114,
                'quantity' => 100,
                'cost' => 20000
            ],
            [
                'sku' => 785,
                'quantity' => 150,
                'cost' => 7500
            ]
        ];
        $result [] = [
            1,
            '2018-09-02',
            $values

        ];

        $values = [
            [
                'sku' => 114,
                'quantity' => 100,
                'cost' => 20000
            ],
            [
                'sku' => 785,
                'quantity' => 250,
                'cost' => 12500
            ]
        ];

        $result [] = [
            1,
            '2018-09-28',
            $values

        ];

        $values = [
            [
                'sku' => 785,
                'quantity' => 50,
                'cost' => 2500
            ]
        ];
        $result [] = [
            4,
            '2018-09-01',
            $values

        ];

        $values = [
            [
                'sku' => 5555,
                'quantity' => 500,
                'cost' => 115000
            ]
        ];
        $result [] = [
            5,
            '2018-09-01',
            $values
        ];

        $values = [];
        $result [] = [
            15,
            '2018-09-01',
            $values

        ];

        return $result;
    }
    /**
     * @dataProvider dataGetResiduesByProduct
     */
    public function testGetResiduesByProduct($productId, $expectedValue)
    {
        if (self::$stateService === null) {
            self::$stateService = new StateService(new StateRepository($this->dbal));
        }

        $value = self::$stateService->getResiduesByProduct($productId);
        $this->assertEquals($value, $expectedValue);
    }

    public function dataGetResiduesByProduct()
    {
        $result [] = [
            8,
            [
                [
                    'warehouseId' => 1,
                    'quantity' => 250,
                    'cost' => 12500.0
                ],
                [
                    'warehouseId' => 2,
                    'quantity' => 100,
                    'cost' => 5000.0
                ],
                [
                    'warehouseId' => 3,
                    'quantity' => 150,
                    'cost' => 7500.0
                ]
            ]

        ];

        $result [] = [
            1,
            [
                [
                    'warehouseId' => 2,
                    'quantity' => 100,
                    'cost' => 25000.0
                ]
            ]

        ];

        $result [] = [
            9,
            []

        ];

        $result [] = [
            15,
            []
        ];

        return $result;
    }
    /**
     * @dataProvider dataGetResiduesByProductForDate
     */
    public function testGetResiduesByProductForDate($productId, $date, $expectedValue)
    {
        if (self::$stateService === null) {
            self::$stateService = new StateService(new StateRepository($this->dbal));
        }

        $value = self::$stateService->getResiduesByProduct($productId, $date);
        $this->assertEquals($value, $expectedValue);
    }

    public function dataGetResiduesByProductForDate()
    {
        $values = [
            [
                'warehouseId' => 1,
                'quantity' => 250,
                'cost' => 12500.0
            ],
            [
                'warehouseId' => 2,
                'quantity' => 100,
                'cost' => 5000.0
            ],
            [
                'warehouseId' => 3,
                'quantity' => 150,
                'cost' => 7500.0
            ]
        ];
        $result [] = [
            8,
            '2018-09-25',
            $values

        ];

        $values = [
            [
                'warehouseId' => 1,
                'quantity' => 250,
                'cost' => 12500.0
            ],
            [
                'warehouseId' => 2,
                'quantity' => 100,
                'cost' => 5000.0
            ],
            [
                'warehouseId' => 3,
                'quantity' => 150,
                'cost' => 7500.0
            ]
        ];

        $result [] = [
            8,
            '2018-09-1',
            $values

        ];

        $result [] = [
            9,
            '2018-08-25',
            []

        ];
        $result [] = [
            15,
            '2018-08-25',
            []
        ];

        return $result;
    }

    public function testAddProducts()
    {
        if (self::$stateService === null) {
            self::$stateService = new StateService(new StateRepository($this->dbal));
        }

        $product = new Product(
            1,
            5555,
            'Куриное филе охлажденное',
            250,
            3,
            'food'
        );
        $transactions = [
            new Transaction(
                22,
                $product,
                500,
                'receipt',
                date('Y-m-d'),
                'Пазолини Корней Свястоплясович',
                '2'
            )];

        self::$stateService->addProducts($transactions);
        $value = self::$stateService->quantityProductInWarehouse(2, 1);
        $this->assertEquals($value, 600);

        $product = new Product(
            8,
            785,
            'Молоко 3,2% 1 литр',
            50,
            3,
            'food'
        );
        $transactions = [
            new Transaction(
                23,
                $product,
                600,
                'receipt',
                date('Y-m-d'),
                'Пазолини Корней Свястоплясович',
                '1'
            )];

        self::$stateService->addProducts($transactions);
        $value = self::$stateService->quantityProductInWarehouse(1, 8);
        $this->assertEquals($value, 850);

        $product1 = new Product(
            8,
            785,
            'Молоко 3,2% 1 литр',
            50,
            3,
            'food'
        );
        $product2 = new Product(
            3,
            125,
            'Морковь свежая',
            50,
            5,
            'food'
        );

        $transactions = [
            new Transaction(
                24,
                $product2,
                100,
                'receipt',
                date('Y-m-d'),
                'Пазолини Корней Свястоплясович',
                '3'
            ),
            new Transaction(
                25,
                $product1,
                600,
                'receipt',
                date('Y-m-d'),
                'Пазолини Корней Свястоплясович',
                '1'
            ),
            new Transaction(
                26,
                $product2,
                800,
                'receipt',
                date('Y-m-d'),
                'Пазолини Корней Свястоплясович',
                '1'
            )
        ];

        self::$stateService->addProducts($transactions);

        $value = self::$stateService->quantityProductInWarehouse(1, 8);
        $this->assertEquals($value, 1450);

        $value = self::$stateService->quantityProductInWarehouse(1, 3);
        $this->assertEquals($value, 800);

        $value = self::$stateService->quantityProductInWarehouse(3, 3);
        $this->assertEquals($value, 600);
    }
    /**
     * @dataProvider dataAddProducts2
     */
    public function testAddProducts2($transactions, $expectedValues)
    {
        if (self::$stateService === null) {
            self::$stateService = new StateService(new StateRepository($this->dbal));
        }

        $exception = $expectedValues['exception'];
        $this->expectException($exception['class']);
        $this->expectExceptionCode($exception['code']);
        $this->expectExceptionMessage($exception['message']);

        self::$stateService->addProducts($transactions);
    }

    public function dataAddProducts2()
    {
        $product1 = new Product(
            8,
            785,
            'Молоко 3,2% 1 литр',
            50,
            3,
            'food'
        );
        $product2 = new Product(
            3,
            125,
            'Морковь свежая',
            50,
            5,
            'food'
        );

        $transactions = [
            new Transaction(
                27,
                $product2,
                10001,
                'receipt',
                date('Y-m-d'),
                'Пазолини Корней Свястоплясович',
                '3'
            ),
            new Transaction(
                28,
                $product1,
                600,
                'receipt',
                date('Y-m-d'),
                'Пазолини Корней Свястоплясович',
                '1'
            ),
            new Transaction(
                29,
                $product2,
                800,
                'receipt',
                date('Y-m-d'),
                'Пазолини Корней Свястоплясович',
                '1'
            )
        ];

        $result[] = [
            $transactions,
            [
                'exception' => [
                    'class' => \LogicException::class,
                    'code' => 400,
                    'message' => 'Not enough space on warehouse with id 3.'
                ]
            ]
        ];

        $transactions = [
            new Transaction(
                30,
                $product2,
                100,
                'receipt',
                date('Y-m-d'),
                'Пазолини Корней Свястоплясович',
                '3'
            ),
            new Transaction(
                31,
                $product1,
                60000,
                'receipt',
                date('Y-m-d'),
                'Пазолини Корней Свястоплясович',
                '1'
            ),
            new Transaction(
                32,
                $product2,
                800,
                'receipt',
                date('Y-m-d'),
                'Пазолини Корней Свястоплясович',
                '1'
            )
        ];
        $result[] = [
            $transactions,
            [
                'exception' => [
                    'class' => \LogicException::class,
                    'code' => 400,
                    'message' => 'Not enough space on warehouse with id 1.'
                ]
            ]
        ];

        return $result;
    }

    public function testRemoveProducts()
    {
        if (self::$stateService === null) {
            self::$stateService = new StateService(new StateRepository($this->dbal));
        }

        $product = new Product(
            1,
            5555,
            'Куриное филе охлажденное',
            250,
            3,
            'food'
        );
        $transactions = [
            new Transaction(
                33,
                $product,
                100,
                'dispatch',
                date('Y-m-d'),
                '2',
                'Пазолини Корней Свястоплясович'
            )];

        self::$stateService->removeProducts($transactions);
        $value = self::$stateService->quantityProductInWarehouse(2, 1);
        $this->assertEquals($value, 0);

        $product = new Product(
            3,
            125,
            'Морковь свежая',
            50,
            5,
            'food'
        );
        $transactions = [
            new Transaction(
                34,
                $product,
                400,
                'dispatch',
                date('Y-m-d'),
                3,
                'Пазолини Корней Свястоплясович'
            )];

        self::$stateService->removeProducts($transactions);
        $value = self::$stateService->quantityProductInWarehouse(3, 3);
        $this->assertEquals($value, 100);

        $product1 = new Product(
            8,
            785,
            'Молоко 3,2% 1 литр',
            50,
            3,
            'food'
        );
        $product2 = new Product(
            5,
            114,
            'Тапки резиновые с дырками',
            200,
            5,
            'household'
        );
        $transactions = [
            new Transaction(
                35,
                $product2,
                80,
                'dispatch',
                date('Y-m-d'),
                1,
                'Пазолини Корней Свястоплясович'
            ),
            new Transaction(
                36,
                $product1,
                200,
                'dispatch',
                date('Y-m-d'),
                1,
                'Пазолини Корней Свястоплясович'
            ),
            new Transaction(
                37,
                $product1,
                100,
                'dispatch',
                date('Y-m-d'),
                2,
                'Пазолини Корней Свястоплясович'
            )
        ];

        self::$stateService->removeProducts($transactions);

        $value = self::$stateService->quantityProductInWarehouse(1, 5);
        $this->assertEquals($value, 20);

        $value = self::$stateService->quantityProductInWarehouse(1, 8);
        $this->assertEquals($value, 50);

        $value = self::$stateService->quantityProductInWarehouse(2, 8);
        $this->assertEquals($value, 0);
    }
    /**
     * @dataProvider dataRemoveProducts2
     */
    public function testRemoveProducts2($transactions, $expectedValues)
    {
        if (self::$stateService === null) {
            self::$stateService = new StateService(new StateRepository($this->dbal));
        }

        $exception = $expectedValues['exception'];
        $this->expectException($exception['class']);
        $this->expectExceptionCode($exception['code']);
        $this->expectExceptionMessage($exception['message']);

        self::$stateService->removeProducts($transactions);
    }

    public function dataRemoveProducts2()
    {
        $product1 = new Product(
            8,
            785,
            'Молоко 3,2% 1 литр',
            50,
            3,
            'food'
        );
        $product2 = new Product(
            5,
            114,
            'Тапки резиновые с дырками',
            200,
            5,
            'household'
        );
        $transactions = [
            new Transaction(
                38,
                $product2,
                10001,
                'dispatch',
                date('Y-m-d'),
                1,
                'Грозный Вахтанг Петрович'
            ),
            new Transaction(
                39,
                $product1,
                600,
                'dispatch',
                date('Y-m-d'),
                2,
                'Грозный Вахтанг Петрович'
            ),
            new Transaction(
                40,
                $product2,
                800,
                'dispatch',
                date('Y-m-d'),
                2,
                'Грозный Вахтанг Петрович'
            )
        ];

        $result[] = [
            $transactions,
            [
                'exception' => [
                    'class' => \LogicException::class,
                    'code' => 400,
                    'message' => 'Not enough product with sku 114 in warehouse with id 1.'
                ]
            ]
        ];

        $transactions = [
            new Transaction(
                41,
                $product1,
                10,
                'dispatch',
                date('Y-m-d'),
                1,
                'Грозный Вахтанг Петрович'
            ),
            new Transaction(
                42,
                $product1,
                60000,
                'dispatch',
                date('Y-m-d'),
                3,
                'Грозный Вахтанг Петрович'
            ),
            new Transaction(
                43,
                $product2,
                800,
                'dispatch',
                date('Y-m-d'),
                1,
                'Грозный Вахтанг Петрович'
            )
        ];

        $result[] = [
            $transactions,
            [
                'exception' => [
                    'class' => \LogicException::class,
                    'code' => 400,
                    'message' => 'Not enough product with sku 785 in warehouse with id 3.'
                ]
            ]
        ];

        $transactions = [
            new Transaction(
                41,
                $product1,
                10,
                'dispatch',
                date('Y-m-d'),
                1,
                'Грозный Вахтанг Петрович'
            ),
            new Transaction(
                42,
                $product1,
                100,
                'dispatch',
                date('Y-m-d'),
                3,
                'Грозный Вахтанг Петрович'
            ),
            new Transaction(
                43,
                $product2,
                800,
                'dispatch',
                date('Y-m-d'),
                2,
                'Грозный Вахтанг Петрович'
            )
        ];

        $result[] = [
            $transactions,
            [
                'exception' => [
                    'class' => \LogicException::class,
                    'code' => 400,
                    'message' => 'Not enough product with sku 114 in warehouse with id 2.'
                ]
            ]
        ];

        return $result;
    }

    public function testMovementProducts()
    {
        if (self::$stateService === null) {
            self::$stateService = new StateService(new StateRepository($this->dbal));
        }

        $product = new Product(
            2,
            5555,
            'Куриное филе охлажденное',
            230,
            3,
            'food'
        );
        $transactions = [
            new Transaction(
                44,
                $product,
                100,
                'betweenWarehouses',
                date('Y-m-d'),
                5,
                6
            )];

        self::$stateService->movementProducts($transactions);
        $value = self::$stateService->quantityProductInWarehouse(5, 2);
        $this->assertEquals($value, 600);
        $value = self::$stateService->quantityProductInWarehouse(6, 2);
        $this->assertEquals($value, 300);

        $product = new Product(
            3,
            125,
            'Морковь свежая',
            50,
            5,
            'food'
        );
        $transactions = [
            new Transaction(
                45,
                $product,
                500,
                'betweenWarehouses',
                date('Y-m-d'),
                3,
                4
            )];

        self::$stateService->movementProducts($transactions);
        $value = self::$stateService->quantityProductInWarehouse(4, 3);
        $this->assertEquals($value, 500);
        $value = self::$stateService->quantityProductInWarehouse(3, 3);
        $this->assertEquals($value, 0);

        $product1 = new Product(
            8,
            785,
            'Молоко 3,2% 1 литр',
            50,
            3,
            'food'
        );
        $product2 = new Product(
            5,
            114,
            'Тапки резиновые с дырками',
            200,
            5,
            'household'
        );
        $transactions = [
            new Transaction(
                46,
                $product2,
                80,
                'betweenWarehouses',
                date('Y-m-d'),
                1,
                3
            ),
            new Transaction(
                47,
                $product1,
                100,
                'betweenWarehouses',
                date('Y-m-d'),
                2,
                3
            ),
            new Transaction(
                48,
                $product,
                100,
                'betweenWarehouses',
                date('Y-m-d'),
                4,
                1
            ),
            new Transaction(
                49,
                $product1,
                100,
                'betweenWarehouses',
                date('Y-m-d'),
                1,
                3
            )
        ];

        self::$stateService->movementProducts($transactions);

        $value = self::$stateService->quantityProductInWarehouse(1, 5);
        $this->assertEquals($value, 20);
        $value = self::$stateService->quantityProductInWarehouse(3, 5);
        $this->assertEquals($value, 80);
        $value = self::$stateService->quantityProductInWarehouse(2, 8);
        $this->assertEquals($value, 0);
        $value = self::$stateService->quantityProductInWarehouse(3, 8);
        $this->assertEquals($value, 350);
        $value = self::$stateService->quantityProductInWarehouse(1, 8);
        $this->assertEquals($value, 150);
        $value = self::$stateService->quantityProductInWarehouse(1, 3);
        $this->assertEquals($value, 100);
        $value = self::$stateService->quantityProductInWarehouse(4, 3);
        $this->assertEquals($value, 400);
    }
}