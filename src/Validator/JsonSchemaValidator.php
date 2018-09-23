<?php

namespace App\Validator;

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
     * @param array $data
     * @param string $schemaPath
     */
    public function checkBySchema($data, string $schemaPath)
    {
        $json = json_encode($data);
        $dataJson = json_decode($json);
        $this->validator->reset();

        $jsonSchema = $this->getJsonSchema($schemaPath);
        $this->validator->check($dataJson, $jsonSchema);
        //сделать нормальный вывод ошибок
        if (!$this->validator->isValid()) {
            throw new ValidationException($this->getErrorMessage($this->validator->getErrors()), 400);
        }
    }
    /**
     * @param string $schemaPath
     *
     * @return mixed
     */
    private function getJsonSchema($schemaPath)
    {
        if (!file_exists($schemaPath)) {
            throw new InvalidSchemaException(
                "Json schema not found by path $schemaPath",
                500
            );
        }
        $jsonSchema = json_decode(file_get_contents($schemaPath));

        if ($jsonSchema === null) {
            throw new InvalidSchemaException(
                "Incorrect json schema in {$schemaPath}",
                500
            );
        }

        return $jsonSchema;
    }
    /**
     * @param $errors
     *
     * @return string
     */
    private function getErrorMessage($errors)
    {
        $message = '';
        foreach ($errors as $error) {
            $constraint = $error['constraint'];
            if ($constraint === 'minLength' ||
                $constraint === 'maxLength' ||
                $constraint === 'type' ||
                $constraint === 'minimum' ||
                $constraint === 'maximum'
            ) {
                $message = $message . "The property {$error['property']} is incorrect. {$error['message']}. ";
            }
            if ($constraint === 'additionalProp' || $constraint === 'required') {
                $message = $message . $error['message'] . '. ';
            }
        }

        return $message;
    }

}