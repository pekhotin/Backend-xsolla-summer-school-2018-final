<?php

namespace Tests\Validator;

use App\Model\Warehouse;
use App\Validator\WarehouseValidator;
use JsonSchema\Exception\ValidationException;
use PHPUnit\Framework\TestCase;

class WarehouseValidatorTest extends TestCase
{
    /**
     * @var Warehouse
     */
    protected $warehouse;

    protected $fixture;

    protected function tearDown()
    {
        $this->fixture = null;
    }

    protected function setUp()
    {
        $this->warehouse = new Warehouse(
            1,
            'Perm, 1 Lenina st.',
            800000
        );
        $this->fixture = new WarehouseValidator();
    }

    /**
     * @dataProvider dataValidateInsertData
     */
    public function testValidateInsertData($data, $expectedValues)
    {
        if (isset($expectedValues['exception'])) {
            $exception = $expectedValues['exception'];
            $this->expectException($exception['class']);
            $this->expectExceptionCode($exception['code']);
            $this->expectExceptionMessage($exception['message']);
        }
        $values = $this->fixture->validateInsertData($data);

        $this->assertEquals($values, $expectedValues);
    }
    public function dataValidateInsertData()
    {
        $result = [];
        $template = [
            'address' => 'Perm, 1 Lenina st.',
            'capacity' => 800000
        ];
        $result[] = [
            [
                'address' => '     Perm, 1 Lenina st.    ',
                'capacity' => 800000
            ],
            $template
        ];
        //string capacity
        $values = $template;
        $values['capacity'] = 'cYFBjfkhs';
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property capacity is incorrect. String value found, but an integer is required. '
                ]
            ]
        ];
        //zero capacity
        $values = $template;
        $values['capacity'] = 0;
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property capacity is incorrect. Must have a minimum value of 1. '
                ]
            ]
        ];
        //null capacity
        $values = $template;
        $values['capacity'] = null;
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property capacity is incorrect. NULL value found, but an integer is required. '
                ]
            ]
        ];
        //minus capacity
        $values = $template;
        $values['capacity'] = -15;
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property capacity is incorrect. Must have a minimum value of 1. '
                ]
            ]
        ];
        //long capacity
        $values = $template;
        $values['capacity'] = 214748364700;
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property capacity is incorrect. Must have a maximum value of 2147483647. '
                ]
            ]
        ];
        //empty address
        $values = $template;
        $values['address'] = '          ';
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => \InvalidArgumentException::class,
                    'code' => '400',
                    'message' => 'address is empty.'
                ]
            ]
        ];
        //numerical address
        $values = $template;
        $values['address'] = 12;
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property address is incorrect. Integer value found, but a string is required.'
                ]
            ]
        ];
        //long address
        $values = $template;
        $values['address'] = 'bfdhfbdsjdnasjkdsjkdksjdnjbfjkdbfjkdbjddddddddddddddddddddddddddddddjkdsjkdksjdnjbfjkdbfjkdbjdddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddd';
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property address is incorrect. Must be at most 150 characters long.'
                ]
            ]
        ];
        //numerical address, long capacity
        $values = $template;
        $values['address'] = 142;
        $values['capacity'] = 214748364700;
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property address is incorrect. Integer value found, but a string is required. The property capacity is incorrect. Must have a maximum value of 2147483647. '
                ]
            ]
        ];
        return $result;
    }

    /**
     * @dataProvider dataValidateUpdateData
     */
    public function testValidateUpdateData($data, $expectedValues)
    {
        if (isset($expectedValues['exception'])) {
            $exception = $expectedValues['exception'];
            $this->expectException($exception['class']);
            $this->expectExceptionCode($exception['code']);
            $this->expectExceptionMessage($exception['message']);
        }
        $values = $this->fixture->validateUpdateData($this->warehouse, $data);

        $this->assertEquals($values, $expectedValues);
    }

    public function dataValidateUpdateData()
    {
        $result = [];
        $template = [
            'address' => 'Perm, 1 Lenina st.',
            'capacity' => 800000
        ];
        //correct address
        $values = $template;
        $values['address'] = 'Perm, 2 Lenina st.';
        $result[] = [
            [
                'address' => 'Perm, 2 Lenina st.    ',
            ],
            $values
        ];

        //string capacity
        $result[] = [
            [
                'capacity' => 'cYFBjfkhs'
            ],
            [
                'exception' => [
                    'class' => \InvalidArgumentException::class,
                    'code' => '400',
                    'message' => 'capacity is not integer.'
                ]
            ]
        ];
        //zero capacity
        $result[] = [
            [
                'capacity' => 0
            ],
            [
                'exception' => [
                    'class' => \InvalidArgumentException::class,
                    'code' => '400',
                    'message' => 'capacity must be greater than 0.'
                ]
            ]
        ];
        //null capacity
        $result[] = [
            [
                'capacity' => null
            ],
            [
                'exception' => [
                    'class' => \InvalidArgumentException::class,
                    'code' => '400',
                    'message' => 'updates parameters are not found.'
                ]
            ]
        ];
        //minus capacity
        $result[] = [
            [
                'capacity' => -15
            ],
            [
                'exception' => [
                    'class' => \InvalidArgumentException::class,
                    'code' => '400',
                    'message' => 'capacity must be greater than 0.'
                ]
            ]
        ];
        //long capacity
        $result[] = [
            [
                'capacity' => 214748364700
            ],
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property capacity is incorrect. Must have a maximum value of 2147483647. '
                ]
            ]
        ];
        //empty address
        $result[] = [
            [
                'address' => '          '
            ],
            [
                'exception' => [
                    'class' => \InvalidArgumentException::class,
                    'code' => '400',
                    'message' => 'address is empty.'
                ]
            ]
        ];
        //numerical address
        $result[] = [
            [
                'address' => 12
            ],
            [
                'exception' => [
                    'class' => \InvalidArgumentException::class,
                    'code' => '400',
                    'message' => 'The property address is incorrect. A string value is required.'
                ]
            ]
        ];
        //long address
        $result[] = [
            [
                'address' => 'bfdhfbdsjdnasjkdsjkdksjdnjbfjkdbfjkdbjddddddddddddddddddddddddddddddjkdsjkdksjdnjbfjkdbfjkdbjdddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddd'
            ],
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property address is incorrect. Must be at most 150 characters long.'
                ]
            ]
        ];
        //numerical address, long capacity
        $result[] = [
            [
                'address' => 142,
                'capacity' => 214748364700
            ],
            [
                'exception' => [
                    'class' => \InvalidArgumentException::class,
                    'code' => '400',
                    'message' => 'The property address is incorrect. A string value is required.'
                ]
            ]
        ];
        //additional property
        $result[] = [
            [
                'address' => 'new address',
                'userid' => 120
            ],
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property userid is not defined and the definition does not allow additional properties. '
                ]
            ]
        ];
        return $result;
    }


    /**
     * @dataProvider dataDispatchProductsData
     */
    public function testDispatchProductsData($data, $expectedValues)
    {
        if (isset($expectedValues['exception'])) {
            $exception = $expectedValues['exception'];
            $this->expectException($exception['class']);
            $this->expectExceptionCode($exception['code']);
            $this->expectExceptionMessage($exception['message']);
        }
        $values = $this->fixture->dispatchProductsData($data);

        $this->assertEquals($values, $expectedValues);
    }
    public function dataDispatchProductsData()
    {
        $result = [];
        $template = [
            'sku' => 152,
            'quantity' => 800000,
            'recipient' => 'Монте-Кристо Самуил Яковлевич'
        ];
        $result[] = [
            [
                'sku' => 152,
                'quantity' => 800000,
                'recipient' => '    Монте-Кристо Самуил Яковлевич     '
            ],
            $template
        ];

        //string sku
        $values = $template;
        $values['sku'] = 'cYFBjfkhs';
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property sku is incorrect. String value found, but an integer is required. '
                ]
            ]
        ];
        //zero sku
        $values = $template;
        $values['sku'] = 0;
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property sku is incorrect. Must have a minimum value of 1. '
                ]
            ]
        ];
        //null sku
        $values = $template;
        $values['sku'] = null;
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property sku is incorrect. NULL value found, but an integer is required. '
                ]
            ]
        ];
        //minus sku
        $values = $template;
        $values['sku'] = -15;
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property sku is incorrect. Must have a minimum value of 1. '
                ]
            ]
        ];
        //long quantity
        $values = $template;
        $values['sku'] = 21474836470;
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property sku is incorrect. Must have a maximum value of 2147483647. '
                ]
            ]
        ];
        //string quantity
        $values = $template;
        $values['quantity'] = 'cYFBjfkhs';
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property quantity is incorrect. String value found, but an integer is required. '
                ]
            ]
        ];
        //zero quantity
        $values = $template;
        $values['quantity'] = 0;
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property quantity is incorrect. Must have a minimum value of 1. '
                ]
            ]
        ];
        //null quantity
        $values = $template;
        $values['quantity'] = null;
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property quantity is incorrect. NULL value found, but an integer is required. '
                ]
            ]
        ];
        //minus quantity
        $values = $template;
        $values['quantity'] = -15;
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property quantity is incorrect. Must have a minimum value of 1. '
                ]
            ]
        ];
        //long quantity
        $values = $template;
        $values['quantity'] = 21474836470;
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property quantity is incorrect. Must have a maximum value of 2147483647. '
                ]
            ]
        ];
        //empty recipient
        $values = $template;
        $values['recipient'] = '          ';
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => \InvalidArgumentException::class,
                    'code' => '400',
                    'message' => 'recipient is empty.'
                ]
            ]
        ];
        //numerical recipient
        $values = $template;
        $values['recipient'] = 12;
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property recipient is incorrect. Integer value found, but a string is required.'
                ]
            ]
        ];
        //long recipient
        $values = $template;
        $values['recipient'] = 'bfdhfbdsjdnasjkdsjkdksjdnjbfjkdbfjkdbjddddddddddddddddddddddddddddddjkdsjkdksjdnjbfjkdbfjkdbjdddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddd';
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property recipient is incorrect. Must be at most 150 characters long.'
                ]
            ]
        ];
        //numerical address, long capacity
        $values = $template;
        $values['recipient'] = 142;
        $values['quantity'] = 214748364700;
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property quantity is incorrect. Must have a maximum value of 2147483647. The property recipient is incorrect. Integer value found, but a string is required.'
                ]
            ]
        ];

        return $result;
    }

    /**
     * @dataProvider dataReceiptProductsData
     */
    public function testReceiptProductsData($data, $expectedValues)
    {
        if (isset($expectedValues['exception'])) {
            $exception = $expectedValues['exception'];
            $this->expectException($exception['class']);
            $this->expectExceptionCode($exception['code']);
            $this->expectExceptionMessage($exception['message']);
        }
        $values = $this->fixture->receiptProductsData($data);

        $this->assertEquals($values, $expectedValues);
    }
    public function dataReceiptProductsData()
    {
        $result = [];
        $template = [
            'sku' => 152,
            'quantity' => 800000,
            'sender' => 'Бандерлогин-Бочковский Бонапарт Владимирович'
        ];
        $result[] = [
            [
                'sku' => 152,
                'quantity' => 800000,
                'sender' => '    Бандерлогин-Бочковский Бонапарт Владимирович     '
            ],
            $template
        ];
        //string sku
        $values = $template;
        $values['sku'] = 'cYFBjfkhs';
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property sku is incorrect. String value found, but an integer is required. '
                ]
            ]
        ];
        //zero sku
        $values = $template;
        $values['sku'] = 0;
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property sku is incorrect. Must have a minimum value of 1. '
                ]
            ]
        ];
        //null sku
        $values = $template;
        $values['sku'] = null;
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property sku is incorrect. NULL value found, but an integer is required. '
                ]
            ]
        ];
        //minus sku
        $values = $template;
        $values['sku'] = -15;
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property sku is incorrect. Must have a minimum value of 1. '
                ]
            ]
        ];
        //long sku
        $values = $template;
        $values['sku'] = 21474836470;
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property sku is incorrect. Must have a maximum value of 2147483647. '
                ]
            ]
        ];
        //string quantity
        $values = $template;
        $values['quantity'] = 'cYFBjfkhs';
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property quantity is incorrect. String value found, but an integer is required. '
                ]
            ]
        ];
        //zero quantity
        $values = $template;
        $values['quantity'] = 0;
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property quantity is incorrect. Must have a minimum value of 1. '
                ]
            ]
        ];
        //null quantity
        $values = $template;
        $values['quantity'] = null;
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property quantity is incorrect. NULL value found, but an integer is required. '
                ]
            ]
        ];
        //minus quantity
        $values = $template;
        $values['quantity'] = -15;
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property quantity is incorrect. Must have a minimum value of 1. '
                ]
            ]
        ];
        //long quantity
        $values = $template;
        $values['quantity'] = 21474836470;
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property quantity is incorrect. Must have a maximum value of 2147483647.'
                ]
            ]
        ];
        //empty sender
        $values = $template;
        $values['sender'] = '          ';
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => \InvalidArgumentException::class,
                    'code' => '400',
                    'message' => 'sender is empty.'
                ]
            ]
        ];
        //numerical recipient
        $values = $template;
        $values['sender'] = 12;
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property sender is incorrect. Integer value found, but a string is required.'
                ]
            ]
        ];
        //long recipient
        $values = $template;
        $values['sender'] = 'bfdhfbdsjdnasjkdsjkdksjdnjbfjkdbfjkdbjddddddddddddddddddddddddddddddjkdsjkdksjdnjbfjkdbfjkdbjdddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddd';
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property sender is incorrect. Must be at most 150 characters long.'
                ]
            ]
        ];
        //numerical sender, long quantity
        $values = $template;
        $values['sender'] = 142;
        $values['quantity'] = 214748364700;
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property quantity is incorrect. Must have a maximum value of 2147483647. The property sender is incorrect. Integer value found, but a string is required. '
                ]
            ]
        ];

        return $result;
    }

    /**
     * @dataProvider dataMovementProductsData
     */
    public function testMovementProductsData($data, $expectedValues)
    {
        if (isset($expectedValues['exception'])) {
            $exception = $expectedValues['exception'];
            $this->expectException($exception['class']);
            $this->expectExceptionCode($exception['code']);
            $this->expectExceptionMessage($exception['message']);
        }
        $values = $this->fixture->movementProductsData($data);

        $this->assertEquals($values, $expectedValues);
    }
    public function dataMovementProductsData()
    {
        $result = [];
        $template = [
            'sku' => 152,
            'quantity' => 800000,
            'warehouseId' => 15
        ];
        $result[] = [
            [
                'sku' => 152,
                'quantity' => 800000,
                'warehouseId' => 15
            ],
            $template
        ];

        //string sku
        $values = $template;
        $values['sku'] = 'cYFBjfkhs';
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property sku is incorrect. String value found, but an integer is required. '
                ]
            ]
        ];
        //zero sku
        $values = $template;
        $values['sku'] = 0;
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property sku is incorrect. Must have a minimum value of 1. '
                ]
            ]
        ];
        //null sku
        $values = $template;
        $values['sku'] = null;
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property sku is incorrect. NULL value found, but an integer is required. '
                ]
            ]
        ];
        //minus sku
        $values = $template;
        $values['sku'] = -15;
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property sku is incorrect. Must have a minimum value of 1. '
                ]
            ]
        ];
        //long sku
        $values = $template;
        $values['sku'] = 21474836470;
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property sku is incorrect. Must have a maximum value of 2147483647. '
                ]
            ]
        ];
        //string quantity
        $values = $template;
        $values['quantity'] = 'cYFBjfkhs';
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property quantity is incorrect. String value found, but an integer is required. '
                ]
            ]
        ];
        //zero quantity
        $values = $template;
        $values['quantity'] = 0;
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property quantity is incorrect. Must have a minimum value of 1. '
                ]
            ]
        ];
        //null quantity
        $values = $template;
        $values['quantity'] = null;
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property quantity is incorrect. NULL value found, but an integer is required. '
                ]
            ]
        ];
        //minus quantity
        $values = $template;
        $values['quantity'] = -15;
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property quantity is incorrect. Must have a minimum value of 1. '
                ]
            ]
        ];
        //long quantity
        $values = $template;
        $values['quantity'] = 21474836470;
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property quantity is incorrect. Must have a maximum value of 2147483647.'
                ]
            ]
        ];
        //string warehouseId
        $values = $template;
        $values['warehouseId'] = 'cYFBjfkhs';
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property warehouseId is incorrect. String value found, but an integer is required. '
                ]
            ]
        ];
        //zero warehouseId
        $values = $template;
        $values['warehouseId'] = 0;
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property warehouseId is incorrect. Must have a minimum value of 1. '
                ]
            ]
        ];
        //null warehouseId
        $values = $template;
        $values['warehouseId'] = null;
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property warehouseId is incorrect. NULL value found, but an integer is required. '
                ]
            ]
        ];
        //minus warehouseId
        $values = $template;
        $values['warehouseId'] = -15;
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property warehouseId is incorrect. Must have a minimum value of 1. '
                ]
            ]
        ];
        //long warehouseId
        $values = $template;
        $values['warehouseId'] = 21474836470;
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property warehouseId is incorrect. Must have a maximum value of 2147483647. '
                ]
            ]
        ];
        //string warehouseId, long quantity
        $values = $template;
        $values['warehouseId'] = '4fvgg';
        $values['quantity'] = 214748364700;
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property quantity is incorrect. Must have a maximum value of 2147483647. The property warehouseId is incorrect. String value found, but an integer is required. '
                ]
            ]
        ];

        return $result;
    }

}