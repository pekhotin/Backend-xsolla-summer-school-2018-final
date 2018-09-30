<?php

namespace Tests\Validator;

use App\Validator\BaseValidator;
use PHPUnit\Framework\TestCase;

class BaseValidatorTest extends TestCase
{
    /**
     * @var BaseValidator
     */
    private $fixture = null;

    protected function setUp()
    {
        $this->fixture = new BaseValidator();
    }

    /**
     * @dataProvider dataValidateVar
     */
    public function testValidateVar($var, $type, $name, $expectedValues)
    {
        if (isset($expectedValues['exception'])) {
            $exception = $expectedValues['exception'];
            $this->expectException($exception['class']);
            $this->expectExceptionCode($exception['code']);
            $this->expectExceptionMessage($exception['message']);
        }

        $values = $this->fixture->validateVar($var, $type, $name);

        $this->assertEquals($values, $expectedValues);
    }

    public function dataValidateVar()
    {
        $result = [];

        $result[] = [
            125,
            'int',
            'testInt',
            125
        ];

        $result[] = [
            '   string  ',
            'string',
            'testString',
            'string'
        ];

        $result[] = [
            2250.5,
            'float',
            'testFloat',
            2250.5
        ];

        $result[] = [
            '2018-09-15',
            'date',
            'testDate',
            '2018-09-15'
        ];

        $result[] = [
            '8 800 555 35 35',
            'phone',
            'testPhone',
            '8 800 555 35 35'
        ];

        $result[] = [
            'qweqweqwe',
            'password',
            'testPassword',
            'qweqweqwe'
        ];

        $result[] = [
            125.15,
            'int',
            'testInt',
            [
                'exception' => [
                    'class' => \InvalidArgumentException::class,
                    'code' => 400,
                    'message' => 'testInt is not integer.'
                ]
            ]
        ];

        $result[] = [
            -125,
            'int',
            'testInt',
            [
                'exception' => [
                    'class' => \InvalidArgumentException::class,
                    'code' => 400,
                    'message' => 'testInt must be greater than 0.'
                ]
            ]
        ];

        $result[] = [
            '    ',
            'string',
            'testString',
            [
                'exception' => [
                    'class' => \InvalidArgumentException::class,
                    'code' => 400,
                    'message' => 'testString is empty.'
                ]
            ]
        ];

        $result[] = [
            125,
            'string',
            'testString',
            [
                'exception' => [
                    'class' => \InvalidArgumentException::class,
                    'code' => 400,
                    'message' => 'The property testString is incorrect. A string value is required.'
                ]
            ]
        ];

        $result[] = [
            '2250.5rub',
            'float',
            'testFloat',
            [
                'exception' => [
                    'class' => \InvalidArgumentException::class,
                    'code' => 400,
                    'message' => 'testFloat is not float'
                ]
            ]
        ];

        $result[] = [
            '2250.5rub',
            'float',
            'testFloat',
            [
                'exception' => [
                    'class' => \InvalidArgumentException::class,
                    'code' => 400,
                    'message' => 'testFloat is not float'
                ]
            ]
        ];

        $result[] = [
            '15-09-2018',
            'date',
            'testDate',
            '15-09-2018'
        ];

        $result[] = [
            '8800vgghg',
            'phone',
            'testPhone',
            [
                'exception' => [
                    'class' => \InvalidArgumentException::class,
                    'code' => 400,
                    'message' => 'testPhone is incorrect.'
                ]
            ]
        ];

        $result[] = [
            '           ',
            'password',
            'testPassword',
            [
                'exception' => [
                    'class' => \InvalidArgumentException::class,
                    'code' => 400,
                    'message' => 'testPassword is empty.'
                ]
            ]
        ];


        return $result;
    }
}