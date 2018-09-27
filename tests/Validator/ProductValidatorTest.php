<?php

namespace Tests\Validator;

use App\Model\Product;
use App\Validator\ProductValidator;
use JsonSchema\Exception\ValidationException;
use PHPUnit\Framework\TestCase;

class ProductValidatorTest extends TestCase
{
    /**
     * @var Product
     */
    protected $product;

    protected $fixture;

    protected function tearDown()
    {
        $this->fixture = null;
    }

    protected function setUp()
    {
        $this->product = new Product(
            1,
            565,
            'Огурец обыкновенный',
            45.5,
            5,
            'food'
        );
        $this->fixture = new ProductValidator();
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
            'sku' => 565,
            'name' => 'Огурец обыкновенный',
            'price' => 45.5,
            'size' => 5,
            'type' => 'food'
        ];
        $result[] = [
            [
                'sku' => 565,
                'name' => 'Огурец обыкновенный      ',
                'price' => 45.5,
                'size' => 5,
                'type' => '      food'
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

        //empty name
        $values = $template;
        $values['name'] = '          ';
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => \InvalidArgumentException::class,
                    'code' => '400',
                    'message' => 'name is empty.'
                ]
            ]
        ];
        //numerical name
        $values = $template;
        $values['name'] = 12;
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property name is incorrect. Integer value found, but a string is required.'
                ]
            ]
        ];
        //long name
        $values = $template;
        $values['name'] = 'bfdhfbdsjdnasjkdsjkdksjdnjbfjkdbfjkdbjdddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddd';
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property name is incorrect. Must be at most 100 characters long.'
                ]
            ]
        ];

        //string price
        $values = $template;
        $values['price'] = 'cYFBjfkhs';
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property price is incorrect. String value found, but a number is required.'
                ]
            ]
        ];
        //zero price
        $values = $template;
        $values['price'] = 0;
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property price is incorrect. Must have a minimum value of 1. '
                ]
            ]
        ];
        //null price
        $values = $template;
        $values['price'] = null;
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property price is incorrect. NULL value found, but a number is required. '
                ]
            ]
        ];

        //minus sku
        $values = $template;
        $values['price'] = -15;
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property price is incorrect. Must have a minimum value of 1. '
                ]
            ]
        ];

        //long price
        $values = $template;
        $values['price'] = 2147483667309242345678888888888888888888877470;
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property price is incorrect. Must have a maximum value of 2147483647. '
                ]
            ]
        ];


        //string size
        $values = $template;
        $values['size'] = 'cYFBjfkhs';
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property size is incorrect. String value found, but an integer is required.'
                ]
            ]
        ];
        //zero size
        $values = $template;
        $values['size'] = 0;
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property size is incorrect. Must have a minimum value of 1. '
                ]
            ]
        ];
        //null size
        $values = $template;
        $values['size'] = null;
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property size is incorrect. NULL value found, but an integer is required. '
                ]
            ]
        ];

        //minus size
        $values = $template;
        $values['size'] = -15;
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property size is incorrect. Must have a minimum value of 1. '
                ]
            ]
        ];

        //long size
        $values = $template;
        $values['size'] = 2147483667309242345678888888888888888888877470;
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property size is incorrect. Must have a maximum value of 2147483647. '
                ]
            ]
        ];

        //long type
        $values = $template;
        $values['type'] = 'bfdhfbdsjdnasjkdsjkdksjdnjbfjkdbfjkdbjdddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddd';
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property type is incorrect. Must be at most 40 characters long.'
                ]
            ]
        ];
        //empty type
        $values = $template;
        $values['type'] = '         ';
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => \InvalidArgumentException::class,
                    'code' => '400',
                    'message' => 'type is empty.'
                ]
            ]
        ];
        //numerical password
        $values = $template;
        $values['type'] = 120;
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property type is incorrect. Integer value found, but a string is required.'
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
        $values = $this->fixture->validateUpdateData($data, $this->product);

        $this->assertEquals($values, $expectedValues);
    }

    public function dataValidateUpdateData()
    {
        $result = [];
        $template = [
            'sku' => 565,
            'name' => 'Огурец обыкновенный',
            'price' => 45.5,
            'size' => 5,
            'type' => 'food'
        ];
        //correct sku
        $values = $template;
        $values['sku'] = 758;
        $result[] = [
            [
                'sku' => 758
            ],
            $values
        ];
        //correct sku, name
        $values = $template;
        $values['sku'] = 235;
        $values['name'] = 'Огурец пупырчатый';
        $result[] = [
            [
                'sku' => 235,
                'name' => 'Огурец пупырчатый'
            ],
            $values
        ];
        //string sku
        $result[] = [
            [
                'sku' => 'cYFBjfkhs'
            ],
            [
                'exception' => [
                    'class' => \InvalidArgumentException::class,
                    'code' => '400',
                    'message' => 'sku is not integer.'
                ]
            ]
        ];
        //zero sku
        $result[] = [
            [
                'sku' => 0
            ],
            [
                'exception' => [
                    'class' => \InvalidArgumentException::class,
                    'code' => '400',
                    'message' => 'sku must be greater than 0.'
                ]
            ]
        ];
        //null sku
        $result[] = [
            [
                'sku' => null
            ],
            [
                'exception' => [
                    'class' => \InvalidArgumentException::class,
                    'code' => '400',
                    'message' => 'Updates parameters are not found.'
                ]
            ]
        ];

        //minus sku
        $result[] = [
            [
                'sku' => -15
            ],
            [
                'exception' => [
                    'class' => \InvalidArgumentException::class,
                    'code' => '400',
                    'message' => 'sku must be greater than 0.'
                ]
            ]
        ];
        //long sku
        $result[] = [
            [
                'sku' => 21474836470
            ],
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property sku is incorrect. Must have a maximum value of 2147483647. '
                ]
            ]
        ];
        //empty name
        $result[] = [
            [
                'name' => '          '
            ],
            [
                'exception' => [
                    'class' => \InvalidArgumentException::class,
                    'code' => '400',
                    'message' => 'name is empty.'
                ]
            ]
        ];
        //numerical name
        $result[] = [
            [
                'name' => 12
            ],
            [
                'exception' => [
                    'class' => \InvalidArgumentException::class,
                    'code' => '400',
                    'message' => 'The property name is incorrect. A string value is required.'
                ]
            ]
        ];
        //long name
        $result[] = [
            [
                'name' => 'bfdhfbdsjdnasjkdsjkdksjdnjbfjkdbfjkdbjdddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddd'
            ],
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property name is incorrect. Must be at most 100 characters long.'
                ]
            ]
        ];

        //string price
        $result[] = [
            [
                'price' => 'cYFBjfkhs'
            ],
            [
                'exception' => [
                    'class' => \InvalidArgumentException::class,
                    'code' => '400',
                    'message' => 'price is not float.'
                ]
            ]
        ];
        //zero price
        $result[] = [
            [
                'price' => 0
            ],
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property price is incorrect. Must have a minimum value of 1. '
                ]
            ]
        ];
        //null price
        $result[] = [
            [
                'price' => null
            ],
            [
                'exception' => [
                    'class' => \InvalidArgumentException::class,
                    'code' => '400',
                    'message' => 'Updates parameters are not found.'
                ]
            ]
        ];

        //minus sku
        $result[] = [
            [
                'price' => -15
            ],
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property price is incorrect. Must have a minimum value of 1. '
                ]
            ]
        ];

        //long price
        $result[] = [
            [
                'price' => 2147483667309242345678888888888888888888877470
            ],
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property price is incorrect. Must have a maximum value of 2147483647. '
                ]
            ]
        ];


        //string size
        $result[] = [
            [
                'size' => 'cYFBjfkhs'
            ],
            [
                'exception' => [
                    'class' => \InvalidArgumentException::class,
                    'code' => '400',
                    'message' => 'size is not integer.'
                ]
            ]
        ];
        //zero size
        $result[] = [
            [
                'size' => 0
            ],
            [
                'exception' => [
                    'class' => \InvalidArgumentException::class,
                    'code' => '400',
                    'message' => 'size must be greater than 0.'
                ]
            ]
        ];
        //null size
        $result[] = [
            [
                'size' => null
            ],
            [
                'exception' => [
                    'class' => \InvalidArgumentException::class,
                    'code' => '400',
                    'message' => 'Updates parameters are not found.'
                ]
            ]
        ];

        //minus size
        $result[] = [
            [
                'size' => -15
            ],
            [
                'exception' => [
                    'class' => \InvalidArgumentException::class,
                    'code' => '400',
                    'message' => 'size must be greater than 0.'
                ]
            ]
        ];

        //long siz
        $result[] = [
            [
                'size' => 2147483667309242345678888888888888888888877470
            ],
            [
                'exception' => [
                    'class' => \InvalidArgumentException::class,
                    'code' => '400',
                    'message' => 'size is not integer.'
                ]
            ]
        ];

        //long type
        $result[] = [
            [
                'type' => 'bfdhfbdsjdnasjkdsjkdksjdnjbfjkdbfjkdbjdddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddd'
            ],
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property type is incorrect. Must be at most 40 characters long.'
                ]
            ]
        ];
        //empty type
        $result[] = [
            [
                'type' => '         '
            ],
            [
                'exception' => [
                    'class' => \InvalidArgumentException::class,
                    'code' => '400',
                    'message' => 'type is empty.'
                ]
            ]
        ];
        //numerical password
        $result[] = [
            [
                'type' => 120
            ],
            [
                'exception' => [
                    'class' => \InvalidArgumentException::class,
                    'code' => '400',
                    'message' => 'The property type is incorrect. A string value is required.'
                ]
            ]
        ];
        //additional property
        $result[] = [
            [
                'sku' => 34,
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
}