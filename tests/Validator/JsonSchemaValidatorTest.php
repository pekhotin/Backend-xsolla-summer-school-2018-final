<?php

namespace Tests\Validator;

use App\Validator\JsonSchemaValidator;
use JsonSchema\Exception\InvalidSchemaException;
use JsonSchema\Exception\ValidationException;
use JsonSchema\Validator;
use PHPUnit\Framework\TestCase;

class JsonSchemaValidatorTest extends TestCase
{
    /**
     * @var JsonSchemaValidator
     */
    private $fixture;

    protected function setUp()
    {
        $this->fixture = new JsonSchemaValidator(new Validator());
    }
    /**
     * @dataProvider dataCheckBySchema
     */
    public function testCheckBySchema($data, $schemaPath, $expectedValues)
    {
        if (isset($expectedValues['exception'])) {
            $exception = $expectedValues['exception'];
            $this->expectException($exception['class']);
            $this->expectExceptionCode($exception['code']);
            $this->expectExceptionMessage($exception['message']);
        }

        $values = $this->fixture->checkBySchema($data, $schemaPath);

        $this->assertEquals($values, $expectedValues);
    }

    public function dataCheckBySchema()
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
            $template,
            __DIR__ . '/../../resources/jsonSchema/product.json',
            $template
        ];

        $template = [
            'address' => 'Perm Kompros 125',
            'capacity' => 125000
        ];

        $result[] = [
            $template,
            __DIR__ . '/../../resources/jsonSchema/warehouse.json',
            $template
        ];

        $template = [
            'sku' => 125,
            'recipient' => 'Иванов Иван Иванович',
            'quantity' => 500
        ];

        $result[] = [
            $template,
            __DIR__ . '/../../resources/jsonSchema/dispatchProducts.json',
            $template
        ];

        $template = [
            'sku' => 125,
            'sender' => 'Иванов Иван Иванович',
            'quantity' => 500
        ];

        $result[] = [
            $template,
            __DIR__ . '/../../resources/jsonSchema/receiptProducts.json',
            $template
        ];

        $template = [
            'sku' => 125,
            'warehouseId' => 4,
            'quantity' => 500
        ];

        $result[] = [
            $template,
            __DIR__ . '/../../resources/jsonSchema/movementProducts.json',
            $template
        ];

        $template = [
            'name' => 'Ivan',
            'surname' => 'Ivanov',
            'login' => 'ewwewsa',
            'password' => 'qweqweqwqe',
            'organization' => 'PSU',
            'email' => 'test@mail.ru',
            'phoneNumber' => '563-76-89'
        ];

        $result[] = [
            $template,
            __DIR__ . '/../../resources/jsonSchema/user.json',
            $template
        ];

        $template = [
            'sku' => 565,
            'name' => 'Огурец обыкновенный',
            'price' => 45.5,
            'size' => 5
        ];

        $result[] = [
            $template,
            __DIR__ . '/../../resources/jsonSchema/product.json',
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => 400,
                    'message' => 'The property type is required.'
                ]
            ]
        ];

        $result[] = [
            $template,
            __DIR__ . '/../../resources/jsonSchema/productTTT.json',
            [
                'exception' => [
                    'class' => InvalidSchemaException::class,
                    'code' => 500,
                    'message' => 'Json schema not found by path /home/xsolla/new_warehouse/tests/Validator/../../resources/jsonSchema/productTTT.json'
                ]
            ]
        ];

        $result[] = [
            $template,
            __DIR__ . '/../../resources/jsonSchema/error.json',
            [
                'exception' => [
                    'class' => InvalidSchemaException::class,
                    'code' => 500,
                    'message' => 'Incorrect json schema in /home/xsolla/new_warehouse/tests/Validator/../../resources/jsonSchema/error.json'
                ]
            ]
        ];

        return $result;
    }
}