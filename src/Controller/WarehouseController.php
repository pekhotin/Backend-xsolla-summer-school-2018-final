<?php

namespace App\Controller;

use App\Model\Transaction;
use App\Service\TransactionService;
use App\Service\UserService;
use App\Service\WarehouseService;
use App\Service\StateService;
use App\Service\ProductService;
use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Model\Warehouse;

class WarehouseController extends BaseController
{
    /**
     * @var WarehouseService
     */
    private $warehouseService;
    /**
     * @var StateService
     */
    private $stateService;
    /**
     * @var ProductService
     */
    private $productService;
    /**
     * @var TransactionService
     */
    private $transactionService;
    /**
     * WarehouseController constructor.
     *
     * @param App $app
     * @param UserService $userService
     * @param WarehouseService $warehouseService
     * @param StateService $stateService
     * @param ProductService $productService
     * @param TransactionService $transactionService
     */
    public function __construct(
        App $app,
        UserService $userService,
        WarehouseService $warehouseService,
        StateService $stateService,
        ProductService $productService,
        TransactionService $transactionService)
    {
        parent::__construct($app, $userService);
        $this->warehouseService = $warehouseService;
        $this->stateService = $stateService;
        $this->productService = $productService;
        $this->transactionService = $transactionService;
    }
    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function addWarehouse(Request $request, Response $response)
    {
        $this->initUser($request);
        $bodyParams = $request->getParsedBody();
        $this->jsonSchemaValidator->checkBySchema($bodyParams, __DIR__ . '/../../resources/jsonSchema/warehouse.json');

        $address = $this->validateVar(trim($bodyParams['address']), 'string', 'address');
        $capacity = $this->validateVar(trim($bodyParams['capacity']), 'int', 'capacity');

        $warehouse = new Warehouse(
            null,
            $address,
            $capacity
        );

        if ($this->warehouseService->getOneByAddress($address, $this->user) !== null) {
            throw new \LogicException(
                "warehouse with address {$address} already exists!",
                400
            );
        }

        $this->warehouseService->add($warehouse, $this->user);

        return $response->withJson($warehouse->getWarehouseArray(), 201);
    }
    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     *
     * @return Response
     */
    public function updateWarehouse(Request $request, Response $response, $args)
    {
        $this->initUser($request);
        $bodyParams = $request->getParsedBody();
        $id = $this->validateVar(trim($args['id']), 'int', 'id');
        $address = null;
        $capacity = null;

        if ($this->warehouseService->getOne($id, $this->user) === null) {
            throw new \LogicException(
                "warehouse with id {$id} not found!",
                404
            );
        }
        if (isset($bodyParams['address'])) {
            $address = $this->validateVar(trim($bodyParams['address']), 'string', 'address');
            if ($this->warehouseService->getOneByAddress($address, $this->user) !== null) {
                throw new \LogicException(
                    "warehouse with address {$address} already exists!",
                    400
                );
            }
        }
        if (isset($bodyParams['capacity'])) {
            $capacity = $this->validateVar(trim($bodyParams['capacity']), 'int', 'capacity');
            if ($this->stateService->getFilling($id) > $capacity) {
                throw new \LogicException(
                    'new capacity can not be less than filling!',
                    400
                );
            }
        }
        if ($address === null && $capacity === null) {
            throw new \LogicException(
                'updates parameters are not found!',
                400
            );
        }

        $warehouse = new Warehouse(
            $id,
            $address,
            $capacity
        );

        $warehouse = $this->warehouseService->update($warehouse, $this->user);

        return $response->withJson($warehouse->getWarehouseArray(), 200);
    }
    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     *
     * @return Response
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function deleteWarehouse(Request $request, Response $response, $args)
    {
        $this->initUser($request);
        $id = $this->validateVar(trim($args['id']), 'int', 'id');

        if ($this->warehouseService->getOne($id, $this->user) === null) {
            throw new \LogicException(
                "warehouse with id {$id} not found!",
                404
            );
        }
        if ($this->transactionService->getMovementsByWarehouse($id) !== null) {
            throw new \LogicException(
                "warehouse with id {$id} already participated in the movement!",
                400
            );
        }

        $this->warehouseService->remove($id);

        return $response
            ->withStatus(204)
            ->withHeader('Content-Type', 'application/json');
    }
    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     *
     * @return Response
     */
    public function getWarehouse(Request $request, Response $response, $args)
    {
        $this->initUser($request);
        $id = $this->validateVar(trim($args['id']), 'int', 'id');
        $warehouse = $this->warehouseService->getOne($id, $this->user);

        if($warehouse === null) {
            throw new \LogicException(
                "warehouse with id {$id} not found!",
                404
            );
        }

        return $response->withJson($warehouse->getWarehouseArray(), 200);
    }
    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function getAllWarehouses(Request $request, Response $response)
    {
        $this->initUser($request);
        $warehouses = $this->warehouseService->getAll($this->user);
        $warehousesArray = [];

        foreach ($warehouses as $warehouse) {
            $warehousesArray[] = $warehouse->getWarehouseArray();
        }

        return $response->withJson($warehousesArray, 200);
    }
    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     *
     * @return Response
     *
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function receiptProducts(Request $request, Response $response, $args)
    {
        $this->initUser($request);
        $direction = 'receipt';
        $bodyParams = $request->getParsedBody();
        $transactions = [];
        $warehouseId = $this->validateVar(trim($args['id']), 'int', 'warehouseId');
        $warehouse = $this->warehouseService->getOne($warehouseId, $this->user);

        if ($warehouse === null) {
            throw new \LogicException(
                "warehouse with id {$warehouseId} not found!",
                404
            );
        }

        foreach ($bodyParams as $param) {
            $this->jsonSchemaValidator->checkBySchema(
                $param,
                __DIR__ . '/../../resources/jsonSchema/receiptProducts.json'
            );

            $sku = $this->validateVar(trim($param['sku']), 'int', 'sku');
            $quantity = $this->validateVar(trim($param['quantity']), 'int', 'quantity');
            $product = $this->productService->getOneBySku($sku, $this->user);

            if ($product === null) {
                throw new \LogicException(
                    "product with sku {$sku} not found!",
                    400
                );
            }

            $transactions[] = new Transaction(
                null,
                $product,
                $quantity,
                $direction,
                (string)date('Y-m-d H:i:s'),
                $this->validateVar(trim($param['sender']), 'string', 'sender'),
                $warehouseId

            );
        }

        $this->stateService->addProducts($transactions);
        $this->transactionService->add($transactions);

        $transactionsArray = [];
        foreach ($transactions as $transaction) {
            $transactionsArray[] = $transaction->getTransactionInfo();
        }

        return $response->withJson($transactionsArray, 201);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     *
     * @return Response
     *
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function dispatchProducts(Request $request, Response $response, $args)
    {
        $this->initUser($request);
        $direction = 'dispatch';

        $bodyParams = $request->getParsedBody();
        $transactions = [];
        $warehouseId = $this->validateVar(trim($args['id']), 'int', 'warehouseId');
        $warehouse = $this->warehouseService->getOne($warehouseId, $this->user);

        if ($warehouse === null) {
            throw new \LogicException(
                "warehouse with id {$warehouseId} not found!",
                404
            );
        }

        foreach ($bodyParams as $param) {
            $this->jsonSchemaValidator->checkBySchema(
                $param,
                __DIR__ . '/../../resources/jsonSchema/dispatchProducts.json'
            );

            $sku = $this->validateVar(trim($param['sku']), 'int', 'sku');
            $quantity = $this->validateVar(trim($param['quantity']), 'int', 'quantity');
            $product = $this->productService->getOneBySku($sku, $this->user);

            if ($product === null) {
                throw new \LogicException(
                    "product with sku {$sku} not found!",
                    400
                );
            }

            $thisQuantity = $this->stateService->quantityProductInWarehouse($warehouseId, $product->getId());
            if ($thisQuantity < $quantity) {
                throw new \LogicException(
                    "not enough product with sku {$sku} in warehouse!",
                    400
                );
            }

            $transactions[] = new Transaction(
                null,
                $product,
                $quantity,
                $direction,
                (string)date('Y-m-d H:i:s'),
                $warehouseId,
                $this->validateVar(trim($param['recipient']), 'string', 'recipient')

            );
        }

        $this->stateService->removeProducts($transactions);
        $this->transactionService->add($transactions);

        $transactionsArray = [];
        foreach ($transactions as $transaction) {
            $transactionsArray[] = $transaction->getTransactionInfo();
        }

        return $response->withJson($transactionsArray, 201);
    }
    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     *
     * @return Response
     *
     * @throws \Exception
     */
    public function movementProducts(Request $request, Response $response, $args)
    {
        $this->initUser($request);
        $direction = 'betweenWarehouses';
        $bodyParams = $request->getParsedBody();
        $transactions = [];
        $warehouseId = $this->validateVar(trim($args['id']), 'int', 'warehouseId');
        $warehouse = $this->warehouseService->getOne($warehouseId, $this->user);

        if ($warehouse === null) {
            throw new \LogicException(
                "warehouse with id {$warehouseId} not found!",
                404
            );
        }

        foreach ($bodyParams as $param) {
            $this->jsonSchemaValidator->checkBySchema(
                $param,
                __DIR__ . '/../../resources/jsonSchema/movementProducts.json'
            );

            $sku = $this->validateVar(trim($param['sku']), 'int', 'sku');
            $quantity = $this->validateVar(trim($param['quantity']), 'int', 'quantity');
            $newWarehouseId = $this->validateVar(trim($param['warehouseId']), 'int', 'warehouseId');
            $newWarehouse = $this->warehouseService->getOne($newWarehouseId, $this->user);

            $product = $this->productService->getOneBySku($sku, $this->user);
            if ($product === null) {
                throw new \LogicException(
                    "product with sku {$sku} not found!",
                    400
                );
            }
            if ($newWarehouse === null) {
                throw new \LogicException(
                    "warehouse with id {$newWarehouseId} not found!",
                    400
                );
            }

            $transactions[] = new Transaction(
                null,
                $product,
                $quantity,
                $direction,
                (string)date('Y-m-d H:i:s'),
                $warehouseId,
                $newWarehouseId
            );
        }

        $this->stateService->movementProducts($transactions);
        $this->transactionService->add($transactions);

        $transactionsArray = [];
        foreach ($transactions as $transaction) {
            $transactionsArray[] = $transaction->getTransactionInfo();
        }

        return $response->withJson($transactionsArray, 201);
    }
    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     *
     * @return Response
     */
    public function getResidues(Request $request, Response $response, $args)
    {
        $this->initUser($request);
        $warehouseId = $this->validateVar(trim($args['id']), 'int', 'id');

        if ($this->warehouseService->getOne($warehouseId, $this->user) === null) {
            throw new \LogicException(
                "warehouse with id {$warehouseId} not found!",
                404
            );
        }

        $residues = $this->stateService->getResiduesByWarehouse($warehouseId);

        return $response->withJson($residues, 200);
    }
    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     *
     * @return Response
     */
    public function getResiduesForDate(Request $request, Response $response, $args)
    {
        $this->initUser($request);
        $warehouseId = $this->validateVar(trim($args['id']), 'int', 'id');
        $date = $this->validateVar(trim($args['date']), 'date', 'date');

        if ($this->warehouseService->getOne($warehouseId, $this->user) === null) {
            throw new \LogicException(
                "warehouse with id {$warehouseId} not found!",
                404
            );
        }

        $residues = $this->stateService->getResiduesByWarehouseForDate($warehouseId, $date);

        return $response->withJson($residues, 200);
    }
    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     *
     * @return Response
     */
    public function getMovements(Request $request, Response $response, $args)
    {
        $this->initUser($request);
        $warehouseId = $this->validateVar(trim($args['id']), 'int', 'id');

        if ($this->warehouseService->getOne($warehouseId, $this->user) === null) {
            throw new \LogicException(
                "product with id {$warehouseId} not found!",
                404
            );
        }

        $transactions = $this->transactionService->getMovementsByWarehouse($warehouseId);

        return $response->withJson($transactions, 200);
    }
}