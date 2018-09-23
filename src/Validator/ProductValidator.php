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
        $values['name'] = $this->validateVar($data['name'], 'string', 'name');
        $values['price'] = $this->validateVar($data['price'], 'float', 'price');
        $values['size'] = $this->validateVar($data['size'], 'int', 'size');
        $values['type'] = $this->validateVar($data['type'], 'string', 'type');

        $this->jsonSchemaValidator->checkBySchema($data, $this->schemaPath);

        return $values;
    }

    /**
     * @param array $data
     * @param Product $product
     *
     * @return array
     */
    public function validateUpdateData($data, $product)
    {
        if (!isset($data['name']) &&
            !isset($data['price']) &&
            !isset($data['size']) &&
            !isset($data['type']) &&
            !isset($data['sku'])
        ) {
            throw new \InvalidArgumentException(
                'Updates parameters are not found.',
                400
            );
        }

        $data['sku'] = isset($data['sku'])
            ? $this->validateVar($data['sku'], 'int', 'sku')
            : $product->getSku();
        $data['name'] = isset($data['name'])
            ? $this->validateVar($data['name'], 'string', 'name')
            : $product->getName();
        $data['price'] = isset($data['price'])
            ? $this->validateVar($data['price'], 'float', 'price')
            : $product->getPrice();
        $data['size'] = isset($data['size'])
            ? $this->validateVar($data['size'], 'int', 'size')
            : $product->getSize();
        $data['type'] = isset($data['type'])
            ? $this->validateVar($data['type'], 'string', 'type')
            : $product->getType();

        $this->jsonSchemaValidator->checkBySchema($data, $this->schemaPath);

        return $data;
    }
}