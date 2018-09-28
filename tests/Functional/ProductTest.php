<?php

namespace Tests\Functional;

use JsonSchema\Exception\ValidationException;

class ProductTest extends ApiTestCase
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

    public function testGetAllProducts()
    {
        $this->request('GET', '/api/v1/products');

        $this->assertThatResponseHasStatus(200);
        $this->assertThatResponseHasContentType('application/json');
        $this->assertCount(7, $this->responseData());
        $this->assertEquals(
            [
                [
                    "sku" => 5555,
                    "name" => "Куриное филе охлажденное",
                    "price" => 250,
                    "size" => 3,
                    "type" => "food"
                ],
                [
                    "sku" => 125,
                    "name" => "Морковь свежая",
                    "price" => 50,
                    "size" => 5,
                    "type" => "food"
                ],
                [
                    "sku" => 115,
                    "name" => "Шампунь Xеден шолдерс",
                    "price" => 150,
                    "size" => 1,
                    "type" => "household"
                ],
                [
                    "sku" => 114,
                    "name" => "Тапки резиновые с дырками",
                    "price" => 200,
                    "size" => 5,
                    "type" => "household"
                ],
                [
                    "sku" => 2145,
                    "name" => "Термос для еды с контейнерами",
                    "price" => 650,
                    "size" => 3,
                    "type" => "household"
                ],
                [
                    "sku" => 785,
                    "name" => "Молоко 3,2% 1 литр",
                    "price" => 50,
                    "size" => 3,
                    "type" => "food"
                ],
                [
                    "sku" => 65629,
                    "name" => "Помидоры Абакан",
                    "price" => 70,
                    "size" => 5,
                    "type" => "food"
                ]
            ],
            $this->responseData()
        );
    }

    public function testGetProduct()
    {
        $this->request('GET', '/api/v1/products/5555');

        $this->assertThatResponseHasStatus(200);
        $this->assertThatResponseHasContentType('application/json');
        $this->assertCount(5, $this->responseData());
        $this->assertEquals(
            [
                "sku" => 5555,
                "name" => "Куриное филе охлажденное",
                "price" => 250,
                "size" => 3,
                "type" => "food"
            ],
            $this->responseData()
        );

        $this->expectException(\LogicException::class);
        $this->expectExceptionCode(404);
        $this->expectExceptionMessage('product with sku 111 not found.');


        $this->request('GET', '/api/v1/products/111');
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
            '/api/v1/products',
            $data
        );

        $this->assertThatResponseHasStatus(201);
        $this->assertThatResponseHasContentType('application/json');
        $this->assertCount(5, $this->responseData());
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
                'sku' => 4851,
                'name' => 'Чебуреки',
                'price' => 300,
                'size' => 2,
                'type' => 15
            ],
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => 400,
                    'message' => 'The property type is incorrect. Integer value found, but a string is required. '
                ]
            ]
        ];

        $result[] = [
            [
                'sku' => 5555,
                'name' => 'Чебуреки',
                'price' => 300,
                'size' => 2,
                'type' => 'food'
            ],
            [
                'exception' => [
                    'class' => \LogicException::class,
                    'code' => 400,
                    'message' => 'product with sku 5555 already exists.'
                ]
            ]
        ];

        $result[] = [
            [
                'sku' => 5556,
                'name' => 'Чебуреки',
                'price' => 300,
                'size' => 2,
                'type' => 'food'
            ],
            [
                'sku' => 5556,
                'name' => 'Чебуреки',
                'price' => 300,
                'size' => 2,
                'type' => 'food'
            ]
        ];

        return $result;
    }

    /**
     * @dataProvider dataUpdateProduct
     */
    public function testUpdateProduct($sku, $data, $expectedValues)
    {
        if (isset($expectedValues['exception'])) {
            $exception = $expectedValues['exception'];
            $this->expectException($exception['class']);
            $this->expectExceptionCode($exception['code']);
            $this->expectExceptionMessage($exception['message']);
        }

        $this->request(
            'PUT',
            "/api/v1/products/$sku",
            $data
        );

        $this->assertThatResponseHasStatus(200);
        $this->assertThatResponseHasContentType('application/json');
        $this->assertCount(5, $this->responseData());
        $this->assertEquals(
            $expectedValues,
            $this->responseData()
        );
    }

    public function dataUpdateProduct()
    {
        $result = [];

        $result[] = [
            5556,
            [
                'name' => 'Чебуреки',
                'price' => 300,
            ],
            [
                'exception' => [
                    'class' => \LogicException::class,
                    'code' => 404,
                    'message' => 'product with sku 5556 not found.'
                ]
            ]
        ];

        $result[] = [
            111,
            [
                'name' => 'Чебуреки',
                'price' => 300,
            ],
            [
                'exception' => [
                    'class' => \LogicException::class,
                    'code' => 404,
                    'message' => 'product with sku 111 not found.'
                ]
            ]
        ];

        $result[] = [
            5555,
            [
                'name' => 'Чебуреки',
                'price' => '300 rub',
            ],
            [
                'exception' => [
                    'class' => \InvalidArgumentException::class,
                    'code' => 400,
                    'message' => 'price is not float.'
                ]
            ]
        ];

        $result[] = [
            5555,
            [
                'sku' => 125,
                'price' => 300
            ],
            [
                'exception' => [
                    'class' => \LogicException::class,
                    'code' => 400,
                    'message' => 'product with sku 125 already exists.'
                ]
            ]
        ];

        $result[] = [
            5555,
            [
                'sku' => 1201
            ],
            [
                'sku' => 1201,
                'name' => 'Куриное филе охлажденное',
                'price' => 250,
                'size' => 3,
                'type' => 'food'
            ]
        ];

        $result[] = [
            5555,
            [
                'size' => 8
            ],
            [
                'exception' => [
                    'class' => \LogicException::class,
                    'code' => 400,
                    'message' => 'product with sku 5555 already participated in the movements.'
                ]
            ]
        ];

        $result[] = [
            65629,
            [
                'name' => 'Чебуреки',
                'price' => 300,
                'size' => 2
            ],
            [
                'sku' => 65629,
                'name' => 'Чебуреки',
                'price' => 300,
                'size' => 2,
                'type' => 'food'
            ]
        ];

        return $result;
    }

    /**
     * @dataProvider dataDeleteProduct
     */
    public function testDeleteProduct($sku, $expectedValues)
    {
        if (isset($expectedValues['exception'])) {
            $exception = $expectedValues['exception'];
            $this->expectException($exception['class']);
            $this->expectExceptionCode($exception['code']);
            $this->expectExceptionMessage($exception['message']);
        }

        $this->request(
            'DELETE',
            "/api/v1/products/$sku"
        );

        $this->assertThatResponseHasStatus(204);
        $this->assertEquals(
            $expectedValues,
            $this->responseData()
        );
    }

    public function dataDeleteProduct()
    {
        $result = [];

        $result[] = [
            5556,
            [
                'exception' => [
                    'class' => \LogicException::class,
                    'code' => 404,
                    'message' => 'product with sku 5556 not found.'
                ]
            ]
        ];

        $result[] = [
            111,
            [
                'exception' => [
                    'class' => \LogicException::class,
                    'code' => 404,
                    'message' => 'product with sku 111 not found.'
                ]
            ]
        ];

        $result[] = [
            5555,
            [
                'exception' => [
                    'class' => \LogicException::class,
                    'code' => 400,
                    'message' => 'product with sku 5555 already participated in the movements'
                ]
            ]
        ];

        $result[] = [
            65629,
            null
        ];

        return $result;
    }

    public function testGetResidues()
    {
        $this->request('GET', '/api/v1/products/785/residues');

        $this->assertThatResponseHasStatus(200);
        $this->assertThatResponseHasContentType('application/json');
        $this->assertCount(3, $this->responseData());
        $this->assertEquals(
            [
                [
                    "warehouseId" => 1,
                    "quantity" => 250,
                    "cost" => 12500
                ],
                [
                    "warehouseId" => 2,
                    "quantity" => 100,
                    "cost" => 5000
                ],
                [
                    "warehouseId" => 3,
                    "quantity" => 150,
                    "cost" => 7500
                ]
            ],
            $this->responseData()
        );

        $this->expectException(\LogicException::class);
        $this->expectExceptionCode(404);
        $this->expectExceptionMessage('product with sku 111 not found.');

        $this->request('GET', '/api/v1/products/111/residues');
    }

    public function testGetResiduesForDate()
    {
        $this->request('GET', '/api/v1/products/785/residues/2018-09-02');

        $this->assertThatResponseHasStatus(200);
        $this->assertThatResponseHasContentType('application/json');
        $this->assertCount(3, $this->responseData());
        $this->assertEquals(
            [
                [
                    "warehouseId" => 1,
                    "quantity" => 150,
                    "cost" => 7500
                ],
                [
                    "warehouseId" => 2,
                    "quantity" => 100,
                    "cost" => 5000
                ],
                [
                    "warehouseId" => 3,
                    "quantity" => 150,
                    "cost" => 7500
                ]
            ],
            $this->responseData()
        );

        $this->expectException(\LogicException::class);
        $this->expectExceptionCode(404);
        $this->expectExceptionMessage('product with sku 111 not found.');

        $this->request('GET', '/api/v1/products/111/residues/2018-09-02');
    }

    public function testGetMovements()
    {
        $this->request('GET', '/api/v1/products/115/movements');

        $this->assertThatResponseHasStatus(200);
        $this->assertThatResponseHasContentType('application/json');
        $this->assertCount(2, $this->responseData());
        $this->assertEquals(
            [
                [
                    'transactionId' => 1,
                    'sku' => 115,
                    'quantity' => 50,
                    'cost' => 7500,
                    'direction' => 'receipt',
                    'datetime' => '2018-09-01 13:05:45',
                    'sender' => 'Пазолини Корней Свястоплясович',
                    'recipient' => '1'
                ],
                [
                    'transactionId' => 13,
                    'sku' => 115,
                    'quantity' => 50,
                    'cost' => 7500,
                    'direction' => 'dispatch',
                    'datetime' => '2018-09-01 13:20:39',
                    'sender' => '1',
                    'recipient' => 'Петров Иван Иванович',
                ]
            ],
            $this->responseData()
        );

        $this->expectException(\LogicException::class);
        $this->expectExceptionCode(404);
        $this->expectExceptionMessage('product with sku 111 not found.');

        $this->request('GET', '/api/v1/products/111/residues');
    }
}