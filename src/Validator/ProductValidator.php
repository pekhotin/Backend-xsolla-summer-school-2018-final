<?php

namespace App\Validator;

use App\Model\Product;

class ProductValidator extends BaseValidator
{
    public function __construct()
    {
        parent::__construct();
        $this->schemaPath = __DIR__ . '/../../resources/jsonSchema/product.json';
    }

    public function validateInsertData($data)
    {
        $this->jsonSchemaValidator->checkBySchema($data, $this->schemaPath);

        $values = [];
        $values['sku'] = $this->validateVar($data['sku'], 'int', 'sku');
        $values['name'] = $this->validateVar(trim($data['name']), 'string', 'name');
        $values['price'] = $this->validateVar($data['price'], 'float', 'price');
        $values['size'] = $this->validateVar($data['size'], 'int', 'size');
        $values['type'] = $this->validateVar(trim($data['type']), 'string', 'type');

        $this->jsonSchemaValidator->checkBySchema($data, $this->schemaPath);

        return $values;
    }

    /**
     * @param array $data
     * @param Product $product
     */
    public function validateUpdateData($data, $product)
    {
        if (!isset($data['name']) && !isset($data['price']) && !isset($data['size']) && !isset($data['type'])) {
            throw new \LogicException(
                'updates parameters are not found!',
                400
            );
        }

        $values = [];

        $values['name'] = isset($data['name'])
            ? $this->validateVar(trim($data['name']), 'string', 'name')
            : $product->getName();
        $values['price'] = isset($data['price'])
            ? $this->validateVar($data['price'], 'float', 'price')
            : $product->getPrice();
        $values['size'] = isset($data['size'])
            ? $this->validateVar($data['size'], 'int', 'size')
            : $product->getSize();
        $values['type'] = isset($data['type'])
            ? $this->validateVar($data['type'], 'string', 'type')
            : $product->getType();

        $this->jsonSchemaValidator->checkBySchema($values, $this->schemaPath);


    }
}