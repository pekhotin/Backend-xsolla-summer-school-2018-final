<?php

namespace App\Service;

use JsonSchema\Exception\InvalidSchemaException;
use JsonSchema\Exception\ValidationException;
use JsonSchema\Validator;

class JsonSchemaValidator
{
    /**
     * @var Validator
     */
    private $validator;
    /**
     * JsonSchemaValidator constructor.
     *
     * @param Validator $validator
     */
    public function __construct(Validator $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param $data
     * @param string $schemaPath
     *
     * @return bool
     */
    public function checkBySchema($data, string $schemaPath)
    {
        $json = json_encode($data);
        $dataJson = json_decode($json);
        $this->validator->reset();

        $jsonSchema = $this->getJsonSchema($schemaPath);
        $this->validator->check($dataJson, $jsonSchema);

        if (!$this->validator->isValid()) {
            throw new ValidationException($this->validator->getErrors()[0]['message'], 400);
        }

        return true;
    }

    private function getJsonSchema($schemaPath)
    {
        if (!file_exists($schemaPath)) {
            throw new InvalidSchemaException(
                "Json schema not found by path $schemaPath",
                404
            );
        }
        $jsonSchema = json_decode(file_get_contents($schemaPath));

        if ($jsonSchema === null) {
            throw new InvalidSchemaException(
                "Incorrect json schema in {$schemaPath}",
                400
            );
        }

        return $jsonSchema;
    }

}