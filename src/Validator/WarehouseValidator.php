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

        $values['address'] = $this->validateVar(trim($values['address']), 'string', 'address');
        $values['capacity'] = $this->validateVar($values['capacity'], 'int', 'capacity');

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
            throw new \LogicException(
                'updates parameters are not found!',
                400
            );
        }

        $values = [];

        $values['address'] = isset($data['address'])
            ? $this->validateVar(trim($data['address']), 'string', 'address')
            : $warehouse->getAddress();
        $values['capacity'] = isset($data['capacity'])
            ? $this->validateVar($data['capacity'], 'int', 'capacity')
            : $warehouse->getCapacity();

        $this->jsonSchemaValidator->checkBySchema($values, $this->schemaPath);

        return $values;
    }

    public function dispatchProductsData ($data)
    {
        $this->jsonSchemaValidator->checkBySchema($data, $this->schemaPath);

        $values['sku'] = $this->validateVar($data['sku'], 'int', 'sku');
        $values['quantity'] = $this->validateVar($data['quantity'], 'int', 'quantity');
        $values['recipient'] = $this->validateVar(trim($data['recipient']), 'string', 'recipient');

        $this->jsonSchemaValidator->checkBySchema(
            $data,
            __DIR__ . '/../../resources/jsonSchema/dispatchProducts.json'
        );

        return $values;
    }

    public function receiptProductsData ($data)
    {
        $this->jsonSchemaValidator->checkBySchema($data, $this->schemaPath);

        $values['sku'] = $this->validateVar($data['sku'], 'int', 'sku');
        $values['quantity'] = $this->validateVar($data['quantity'], 'int', 'quantity');
        $values['sender'] = $this->validateVar(trim($data['sender']), 'string', 'sender');

        $this->jsonSchemaValidator->checkBySchema(
            $data,
            __DIR__ . '/../../resources/jsonSchema/receiptProducts.json'
        );

        return $values;
    }

    public function movementProductsData($data)
    {
        $this->jsonSchemaValidator->checkBySchema($data, $this->schemaPath);

        $values['sku'] = $this->validateVar($data['sku'], 'int', 'sku');
        $values['quantity'] = $this->validateVar($data['quantity'], 'int', 'quantity');
        $values['warehouseId'] = $this->validateVar($data['warehouseId'], 'int', 'warehouseId');

        $this->jsonSchemaValidator->checkBySchema(
            $data,
            __DIR__ . '/../../resources/jsonSchema/movementProducts.json'
        );

        return $values;
    }
}