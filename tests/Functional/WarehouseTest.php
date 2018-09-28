<?php

namespace Tests\Functional;

use JsonSchema\Exception\ValidationException;

class WarehouseTest extends ApiTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $query = file_get_contents(__DIR__ . '/../fixtures/mvc_Products.sql');
        $this->dbConnection->executeQuery($query);
        $query = file_get_contents(__DIR__ . '/../fixtures/mvc_Warehouses.sql');
        $this->dbConnection->executeQuery($query);
        $query = file_get_contents(__DIR__ . '/../fixtures/mvc_State.sql');
        $this->dbConnection->executeQuery($query);
        $query = file_get_contents(__DIR__ . '/../fixtures/mvc_Transactions.sql');
        $this->dbConnection->executeQuery($query);
    }

    public function testGetAllWarehouses()
    {
        $this->request('GET', '/api/v1/warehouses');

        $this->assertThatResponseHasStatus(200);
        $this->assertThatResponseHasContentType('application/json');
        $this->assertCount(6, $this->responseData());
        $this->assertEquals(
            [
                [
                    'id' => 1,
                    'address' => 'Perm Lenina 1',
                    'capacity' => 100000
                ],
                [
                    'id' => 2,
                    'address' => 'Perm Vosstanya 99',
                    'capacity' => 500000
                ],
                [
                    'id' => 3,
                    'address' => 'Perm Kompros 1',
                    'capacity' => 50000
                ],
                [
                    'id' => 4,
                    'address' => 'Perm Kompros 2',
                    'capacity' => 150000
                ],
                [
                    'id' => 7,
                    'address' => 'Perm Lunacharskogo 34',
                    'capacity' => 500000
                ],
                [
                    'id' => 9,
                    'address' => 'Perm Zvezdnaya 23',
                    'capacity' => 255000
                ]
            ],
            $this->responseData()
        );
    }

    public function testGetWarehouse()
    {
        $this->request('GET', '/api/v1/warehouses/1');

        $this->assertThatResponseHasStatus(200);
        $this->assertThatResponseHasContentType('application/json');
        $this->assertCount(3, $this->responseData());
        $this->assertEquals(
            [
                'id' => 1,
                'address' => 'Perm Lenina 1',
                'capacity' => 100000
            ],
            $this->responseData()
        );

        $this->expectException(\LogicException::class);
        $this->expectExceptionCode(404);
        $this->expectExceptionMessage('warehouse with id 11 not found.');

        $this->request('GET', '/api/v1/warehouses/11');
    }

    /**
     * @dataProvider dataAddProduct
     */
    public function testAddProduct($data, $expectedValues)
    {
        if (isset($expectedValues['exception'])) {
            $exception = $expectedValues['exception'];
            $this->expectException($exception['class']);
            $this->expectExceptionCode($exception['code']);
            $this->expectExceptionMessage($exception['message']);
        }

        $this->request(
            'POST',
            '/api/v1/warehouses',
            $data
        );

        $this->assertThatResponseHasStatus(201);
        $this->assertThatResponseHasContentType('application/json');
        $this->assertCount(3, $this->responseData());
        $this->assertEquals(
            $expectedValues,
            $this->responseData()
        );
    }

    public function dataAddProduct()
    {
        $result = [];

        $result[] = [
            [
                'address' => 'Perm Kompros 56',
                'capacity' => '800000ed'
            ],
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => 400,
                    'message' => 'The property capacity is incorrect. String value found, but an integer is required.'
                ]
            ]
        ];

        $result[] = [
            [
                'address' => 'Perm Kompros 1',
                'capacity' => 800000
            ],
            [
                'exception' => [
                    'class' => \LogicException::class,
                    'code' => 400,
                    'message' => 'warehouse with address Perm Kompros 1 already exists.'
                ]
            ]
        ];

        $result[] = [
            [
                'address' => 'Perm Kompros 56',
                'capacity' => 800000
            ],
            [
                'id' => 10,
                'address' => 'Perm Kompros 56',
                'capacity' => 800000
            ]
        ];

        return $result;
    }

    /**
     * @dataProvider dataUpdateWarehouse
     */
    public function testUpdateWarehouse($id, $data, $expectedValues)
    {
        if (isset($expectedValues['exception'])) {
            $exception = $expectedValues['exception'];
            $this->expectException($exception['class']);
            $this->expectExceptionCode($exception['code']);
            $this->expectExceptionMessage($exception['message']);
        }

        $this->request(
            'PUT',
            "/api/v1/warehouses/$id",
            $data
        );

        $this->assertThatResponseHasStatus(200);
        $this->assertThatResponseHasContentType('application/json');
        $this->assertCount(3, $this->responseData());
        $this->assertEquals(
            $expectedValues,
            $this->responseData()
        );
    }

    public function dataUpdateWarehouse()
    {
        $result = [];

        $result[] = [
            19,
            [
                'address' => 'Perm Lenina 123'
            ],
            [
                'exception' => [
                    'class' => \LogicException::class,
                    'code' => 404,
                    'message' => 'warehouse with id 19 not found.'
                ]
            ]
        ];

        $result[] = [
            5,
            [
                'address' => 'Perm Lenina 123'
            ],
            [
                'exception' => [
                    'class' => \LogicException::class,
                    'code' => 404,
                    'message' => 'warehouse with id 5 not found.'
                ]
            ]
        ];

        $result[] = [
            1,
            [
                'capacity' => '85200ed'
            ],
            [
                'exception' => [
                    'class' => \InvalidArgumentException::class,
                    'code' => 400,
                    'message' => 'capacity is not integer.'
                ]
            ]
        ];

        $result[] = [
            1,
            [
                'capacity' => 10
            ],
            [
                'exception' => [
                    'class' => \LogicException::class,
                    'code' => 400,
                    'message' => 'new capacity can not be less than filling.'
                ]
            ]
        ];

        $result[] = [
            2,
            [
                'capacity' => 1000000
            ],
            [
                'id' => 2,
                'address' => 'Perm Vosstanya 99',
                'capacity' => 1000000
            ]
        ];

        return $result;
    }

    /**
     * @dataProvider dataDeleteWarehouse
     */
    public function testDeleteWarehouse($sku, $expectedValues)
    {
        if (isset($expectedValues['exception'])) {
            $exception = $expectedValues['exception'];
            $this->expectException($exception['class']);
            $this->expectExceptionCode($exception['code']);
            $this->expectExceptionMessage($exception['message']);
        }

        $this->request(
            'DELETE',
            "/api/v1/warehouses/$sku"
        );

        $this->assertThatResponseHasStatus(204);
        $this->assertEquals(
            $expectedValues,
            $this->responseData()
        );
    }

    public function dataDeleteWarehouse()
    {
        $result = [];
        //несуществующий склад
        $result[] = [
            19,
            [
                'exception' => [
                    'class' => \LogicException::class,
                    'code' => 404,
                    'message' => 'warehouse with id 19 not found.'
                ]
            ]
        ];
        //чужой
        $result[] = [
            5,
            [
                'exception' => [
                    'class' => \LogicException::class,
                    'code' => 404,
                    'message' => 'warehouse with id 5 not found.'
                ]
            ]
        ];
        //учитывается в перемещениях
        $result[] = [
            1,
            [
                'exception' => [
                    'class' => \LogicException::class,
                    'code' => 400,
                    'message' => 'warehouse with id 1 already participated in the movements'
                ]
            ]
        ];
        //можно удалить
        $result[] = [
            9,
            null
        ];

        return $result;
    }

    public function testGetResidues()
    {
        $this->request('GET', '/api/v1/warehouses/3/residues');

        $this->assertThatResponseHasStatus(200);
        $this->assertThatResponseHasContentType('application/json');
        $this->assertCount(2, $this->responseData());
        $this->assertEquals(
            [
                [
                    "sku" => 125,
                    "quantity" => 500,
                    "cost" => 25000
                ],
                [
                    "sku" => 785,
                    "quantity" => 150,
                    "cost" => 7500
                ]
            ],
            $this->responseData()
        );

        $this->expectException(\LogicException::class);
        $this->expectExceptionCode(404);
        $this->expectExceptionMessage('warehouse with id 18 not found.');

        $this->request('GET', '/api/v1/warehouses/18/residues');
    }

    public function testGetResiduesForDate()
    {
        $this->request('GET', '/api/v1/warehouses/1/residues/2018-09-02');

        $this->assertThatResponseHasStatus(200);
        $this->assertThatResponseHasContentType('application/json');
        $this->assertCount(2, $this->responseData());
        $this->assertEquals(
            [
                [
                    "sku" => 114,
                    'quantity' => 100,
                    'cost' => 20000
                ],
                [
                    "sku" => 785,
                    'quantity' => 150,
                    'cost' => 7500
                ]
            ],
            $this->responseData()
        );

        $this->expectException(\LogicException::class);
        $this->expectExceptionCode(404);
        $this->expectExceptionMessage('warehouse with id 15 not found.');

        $this->request('GET', '/api/v1/warehouses/15/residues/2018-09-02');
    }

    public function testGetMovements()
    {
        $this->request('GET', '/api/v1/warehouses/4/movements');

        $this->assertThatResponseHasStatus(200);
        $this->assertThatResponseHasContentType('application/json');
        $this->assertCount(2, $this->responseData());
        $this->assertEquals(
            [
                [
                    'transactionId' => 16,
                    'sku' => 785,
                    'quantity' => 50,
                    'cost' => 2500,
                    'direction' => 'betweenWarehouses',
                    'datetime' => '2018-09-01 13:34:07',
                    'sender' => '1',
                    'recipient' => '4'
                ],
                [
                    'transactionId' => 27,
                    'sku' => 785,
                    'quantity' => 50,
                    'cost' => 2500,
                    'direction' => 'betweenWarehouses',
                    'datetime' => '2018-09-02 13:55:11',
                    'sender' => '4',
                    'recipient' => '3'
                ]
            ],
            $this->responseData()
        );

        $this->expectException(\LogicException::class);
        $this->expectExceptionCode(404);
        $this->expectExceptionMessage('warehouse with id 12 not found.');

        $this->request('GET', '/api/v1/warehouses/12/residues');
    }

    /**
     * @dataProvider dataReceiptProducts
     */
    public function testReceiptProducts($id, $data, $expectedValues)
    {
        if (isset($expectedValues['exception'])) {
            $exception = $expectedValues['exception'];
            $this->expectException($exception['class']);
            $this->expectExceptionCode($exception['code']);
            $this->expectExceptionMessage($exception['message']);
        }

        $this->request(
            'PUT',
            "/api/v1/warehouses/$id/receipt",
            $data
        );

        foreach ($expectedValues as &$value) {
            $value['datetime'] = date('Y-m-d H:i:s');
        }

        $this->assertThatResponseHasStatus(201);
        $this->assertEquals(
            $expectedValues,
            $this->responseData()
        );
    }

    public function dataReceiptProducts()
    {
        $result = [];

        $data = [
            [
                'sku' => 125,
                'quantity' => 600,
                'sender' => 'Пазолини Корней Свястоплясович'
            ],
            [
                'sku' => 785,
                'quantity' => 15000000,
                'sender' => 'Пазолини Корней Свястоплясович'
            ]
        ];

        $result[] = [
            1,
            $data,
            [
                'exception' => [
                    'class' => \LogicException::class,
                    'code' => 400,
                    'message' => 'Not enough space on warehouse with id 1.'
                ]
            ]
        ];

        $data = [
            [
                'sku' => 'dfsh213',
                'quantity' => 600,
                'sender' => 'Пазолини Корней Свястоплясович'
            ],
            [
                'sku' => 785,
                'quantity' => 15000000,
                'sender' => 'Пазолини Корней Свястоплясович'
            ]
        ];

        $result[] = [
            1,
            $data,
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => 400,
                    'message' => 'The property sku is incorrect. String value found, but an integer is required. '
                ]
            ]
        ];

        $data = [
            [
                'sku' => 115,
                'quantity' => 600,
                'sender' => 'Пазолини Корней Свястоплясович'
            ],
            [
                'sku' => 785,
                'quantity' => 150,
                'sender' => 'Пазолини Корней Свястоплясович'
            ]
        ];

        $result[] = [
            1,
            $data,
            [
                [
                    'transactionId' => 33,
                    'sku' => 115,
                    'quantity' => 600,
                    'direction' => 'receipt',
                    'sender' => 'Пазолини Корней Свястоплясович',
                    'recipient' => 1
                ],
                [
                    'transactionId' => 34,
                    'sku' => 785,
                    'quantity' => 150,
                    'direction' => 'receipt',
                    'sender' => 'Пазолини Корней Свястоплясович',
                    'recipient' => 1
                ]
            ]
        ];

        return $result;
    }

    /**
     * @dataProvider dataDispatchProducts
     */
    public function testDispatchProducts($id, $data, $expectedValues)
    {
        if (isset($expectedValues['exception'])) {
            $exception = $expectedValues['exception'];
            $this->expectException($exception['class']);
            $this->expectExceptionCode($exception['code']);
            $this->expectExceptionMessage($exception['message']);
        }

        $this->request(
            'PUT',
            "/api/v1/warehouses/$id/dispatch",
            $data
        );

        foreach ($expectedValues as &$value) {
            $value['datetime'] = date('Y-m-d H:i:s');
        }

        $this->assertThatResponseHasStatus(201);
        $this->assertEquals(
            $expectedValues,
            $this->responseData()
        );
    }

    public function dataDispatchProducts()
    {
        $result = [];

        $data = [
            [
                'sku' => 125,
                'quantity' => 600,
                'recipient' => 'Пазолини Корней Свястоплясович'
            ],
            [
                'sku' => 785,
                'quantity' => 15000000,
                'recipient' => 'Пазолини Корней Свястоплясович'
            ]
        ];

        $result[] = [
            1,
            $data,
            [
                'exception' => [
                    'class' => \LogicException::class,
                    'code' => 400,
                    'message' => 'Not enough product with sku 125 in warehouse with id 1.'
                ]
            ]
        ];

        $data = [
            [
                'sku' => 'dfsh213',
                'quantity' => 600,
                'recipient' => 'Пазолини Корней Свястоплясович'
            ],
            [
                'sku' => 785,
                'quantity' => 15000000,
                'recipient' => 'Пазолини Корней Свястоплясович'
            ]
        ];

        $result[] = [
            1,
            $data,
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => 400,
                    'message' => 'The property sku is incorrect. String value found, but an integer is required. '
                ]
            ]
        ];

        $data = [
            [
                'sku' => 114,
                'quantity' => 100,
                'recipient' => 'Пазолини Корней Свястоплясович'
            ],
            [
                'sku' => 785,
                'quantity' => 150,
                'recipient' => 'Пазолини Корней Свястоплясович'
            ]
        ];

        $result[] = [
            1,
            $data,
            [
                [
                    'transactionId' => 33,
                    'sku' => 114,
                    'quantity' => 100,
                    'direction' => 'dispatch',
                    'sender' => 1,
                    'recipient' => 'Пазолини Корней Свястоплясович'
                ],
                [
                    'transactionId' => 34,
                    'sku' => 785,
                    'quantity' => 150,
                    'direction' => 'dispatch',
                    'sender' => 1,
                    'recipient' => 'Пазолини Корней Свястоплясович'
                ]
            ]
        ];

        return $result;
    }

    /**
     * @dataProvider dataMovementProducts
     */
    public function testMovementProducts($id, $data, $expectedValues)
    {
        if (isset($expectedValues['exception'])) {
            $exception = $expectedValues['exception'];
            $this->expectException($exception['class']);
            $this->expectExceptionCode($exception['code']);
            $this->expectExceptionMessage($exception['message']);
        }

        foreach ($expectedValues as &$value) {
            $value['datetime'] = date('Y-m-d H:i:s');
        }

        $this->request(
            'PUT',
            "/api/v1/warehouses/$id/movement",
            $data
        );

        $this->assertThatResponseHasStatus(201);
        $this->assertEquals(
            $expectedValues,
            $this->responseData()
        );
    }

    public function dataMovementProducts()
    {
        $result = [];

        $data = [
            [
                'sku' => 125,
                'quantity' => 600,
                'warehouseId' => 4
            ],
            [
                'sku' => 785,
                'quantity' => 150,
                'warehouseId' => 2
            ]
        ];

        $result[] = [
            1,
            $data,
            [
                'exception' => [
                    'class' => \LogicException::class,
                    'code' => 400,
                    'message' => 'Not enough product with sku 125 in warehouse with id 1.'
                ]
            ]
        ];

        $data = [
            [
                'sku' => 114,
                'quantity' => 600,
                'warehouseId' => '6re7e4'
            ],
            [
                'sku' => 785,
                'quantity' => 15000000,
                'warehouseId' => 2
            ]
        ];

        $result[] = [
            3,
            $data,
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => 400,
                    'message' => 'The property warehouseId is incorrect. String value found, but an integer is required. '
                ]
            ]
        ];

        $data = [
            [
                'sku' => 125,
                'quantity' => 300,
                'warehouseId' => 1
            ],
            [
                'sku' => 785,
                'quantity' => 150,
                'warehouseId' => 4
            ]
        ];

        $result[] = [
            3,
            $data,
            [
                [
                    'transactionId' => 33,
                    'sku' => 125,
                    'quantity' => 300,
                    'direction' => 'betweenWarehouses',
                    'sender' => 3,
                    'recipient' => 1
                ],
                [
                    'transactionId' => 34,
                    'sku' => 785,
                    'quantity' => 150,
                    'direction' => 'betweenWarehouses',
                    'sender' => 3,
                    'recipient' => 4
                ]
            ]
        ];

        return $result;
    }
}