<?php

namespace Tests\Service;

use App\Service\JsonSchemaValidator;
use JsonSchema\Validator;
use PHPUnit\Framework\TestCase;

class JsonSchemaValidatorTest extends TestCase
{
    /**
     * @dataProvider dataGetJsonSchema
     */
    public function testCheckBySchema($data, $schemaPath, $expectedResult)
    {
        $jsonSchemaValidator = new JsonSchemaValidator(
            new Validator()
        );
        $result = $jsonSchemaValidator->checkBySchema($data, $schemaPath);

        $this->assertEquals($result, $expectedResult);
    }

    public function dataGetJsonSchema()
    {
        $data
        return [
            [50, 10, 10, 50],
            [0, 10, 10, 100],
            [100, 10, 10, 0],
        ];
    }
}