<?php

namespace App\Validator;

use App\Model\Warehouse;

class WarehouseValidator extends BaseValidator
{
    public function __construct()
    {
        parent::__construct();
        $this->schemaPath = __DIR__ . '/../../resources/jsonSchema/warehouse.json';
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function validateInsertData($data)
    {
        $values = [];

        $this->jsonSchemaValidator->checkBySchema($data, $this->schemaPath);

        $values['address'] = $this->validateVar($data['address'], 'string', 'address');
        $values['capacity'] = $this->validateVar($data['capacity'], 'int', 'capacity');

        $this->jsonSchemaValidator->checkBySchema($data, $this->schemaPath);

        return $values;
    }

    /**
     * @param Warehouse $warehouse
     * @param array $data
     *
     * @return array
     */
    public function validateUpdateData($warehouse, $data)
    {
        if (!isset($data['address']) && !isset($data['capacity'])) {
            throw new \InvalidArgumentException(
                'updates parameters are not found.',
                400
            );
        }

        $data['address'] = isset($data['address'])
            ? $this->validateVar($data['address'], 'string', 'address')
            : $warehouse->getAddress();
        $data['capacity'] = isset($data['capacity'])
            ? $this->validateVar($data['capacity'], 'int', 'capacity')
            : $warehouse->getCapacity();

        $this->jsonSchemaValidator->checkBySchema($data, $this->schemaPath);

        return $data;
    }

    public function dispatchProductsData ($data)
    {
        $this->jsonSchemaValidator->checkBySchema(
            $data,
            __DIR__ . '/../../resources/jsonSchema/dispatchProducts.json'
        );

        $data['sku'] = $this->validateVar($data['sku'], 'int', 'sku');
        $data['quantity'] = $this->validateVar($data['quantity'], 'int', 'quantity');
        $data['recipient'] = $this->validateVar($data['recipient'], 'string', 'recipient');

        $this->jsonSchemaValidator->checkBySchema(
            $data,
            __DIR__ . '/../../resources/jsonSchema/dispatchProducts.json'
        );

        return $data;
    }

    public function receiptProductsData ($data)
    {
        $this->jsonSchemaValidator->checkBySchema(
            $data,
            __DIR__ . '/../../resources/jsonSchema/receiptProducts.json'
        );

        $data['sku'] = $this->validateVar($data['sku'], 'int', 'sku');
        $data['quantity'] = $this->validateVar($data['quantity'], 'int', 'quantity');
        $data['sender'] = $this->validateVar($data['sender'], 'string', 'sender');

        $this->jsonSchemaValidator->checkBySchema(
            $data,
            __DIR__ . '/../../resources/jsonSchema/receiptProducts.json'
        );

        return $data;
    }

    public function movementProductsData($data)
    {
        $this->jsonSchemaValidator->checkBySchema(
            $data,
            __DIR__ . '/../../resources/jsonSchema/movementProducts.json'
        );

        $data['sku'] = $this->validateVar($data['sku'], 'int', 'sku');
        $data['quantity'] = $this->validateVar($data['quantity'], 'int', 'quantity');
        $data['warehouseId'] = $this->validateVar($data['warehouseId'], 'int', 'warehouseId');

        $this->jsonSchemaValidator->checkBySchema(
            $data,
            __DIR__ . '/../../resources/jsonSchema/movementProducts.json'
        );

        return $data;
    }
}