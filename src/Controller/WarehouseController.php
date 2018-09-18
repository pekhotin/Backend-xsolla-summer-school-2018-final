<?php

namespace App\Controller;

use App\Model\Transaction;
use App\Service\TransactionService;
use App\Service\UserService;
use App\Service\WarehouseService;
use App\Service\StateService;
use App\Service\ProductService;
use App\Validator\WarehouseValidator;
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
        $this->validator = new WarehouseValidator();
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
        $values = $this->validator->validateInsertData($bodyParams);

        if ($this->warehouseService->getOneByAddress($values['address'], $this->user) !== null) {
            throw new \LogicException(
                "warehouse with address {$values['address']} already exists!",
                400
            );
        }

        $warehouse = new Warehouse(
            null,
            $values['address'],
            $values['capacity']
        );

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
        $id = $this->validator->validateVar(trim($args['id']), 'int', 'id');

        $warehouse = $this->warehouseService->getOne($id, $this->user);

        if ($warehouse === null) {
            throw new \LogicException(
                "warehouse with id {$id} not found!",
                404
            );
        }

        $values = $this->validator->validateUpdateData($warehouse, $bodyParams);

        if ($this->warehouseService->getOneByAddress($values['address'], $this->user) !== null) {
            throw new \LogicException(
                "warehouse with address {$values['address']} already exists!",
                400
            );
        }

        if ($this->stateService->getFilling($id) > $values['capacity']) {
            throw new \LogicException(
                'new capacity can not be less than filling!',
                400
            );
        }

        $warehouse = new Warehouse(
            $id,
            $values['address'],
            $values['capacity']
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
        $id = $this->validator->validateVar(trim($args['id']), 'int', 'id');

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
        $id = $this->validator->validateVar(trim($args['id']), 'int', 'id');
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
        $warehouseId = $this->validator->validateVar(trim($args['id']), 'int', 'warehouseId');

        $warehouse = $this->warehouseService->getOne($warehouseId, $this->user);

        if ($warehouse === null) {
            throw new \LogicException(
                "warehouse with id {$warehouseId} not found!",
                404
            );
        }

        foreach ($bodyParams as $param) {

            $values = $this->validator->receiptProductsData($param);
            $product = $this->productService->getOneBySku($values['sku'], $this->user);

            if ($product === null) {
                throw new \LogicException(
                    "product with sku {$values['sku']} not found!",
                    400
                );
            }

            $transactions[] = new Transaction(
                null,
                $product,
                $values['quantity'],
                $direction,
                (string)date('Y-m-d H:i:s'),
                $values['sender'],
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
        $warehouseId = $this->validator->validateVar(trim($args['id']), 'int', 'warehouseId');
        $warehouse = $this->warehouseService->getOne($warehouseId, $this->user);

        if ($warehouse === null) {
            throw new \LogicException(
                "warehouse with id {$warehouseId} not found!",
                404
            );
        }

        foreach ($bodyParams as $param) {

            $values = $this->validator->dispatchProductsData($param);
            $product = $this->productService->getOneBySku($values['sku'], $this->user);

            if ($product === null) {
                throw new \LogicException(
                    "product with sku {$values['sku']} not found!",
                    400
                );
            }

            $transactions[] = new Transaction(
                null,
                $product,
                $values['quantity'],
                $direction,
                (string)date('Y-m-d H:i:s'),
                $warehouseId,
                $values['recipient']

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
        $warehouseId = $this->validator->validateVar(trim($args['id']), 'int', 'warehouseId');
        $warehouse = $this->warehouseService->getOne($warehouseId, $this->user);

        if ($warehouse === null) {
            throw new \LogicException(
                "warehouse with id {$warehouseId} not found!",
                404
            );
        }

        foreach ($bodyParams as $param) {
            $values = $this->validator->movementProductsData($param);

            $newWarehouse = $this->warehouseService->getOne($values['warehouseId'], $this->user);

            $product = $this->productService->getOneBySku($values, $this->user);
            if ($product === null) {
                throw new \LogicException(
                    "product with sku {$values['sku']} not found!",
                    400
                );
            }
            if ($newWarehouse === null) {
                throw new \LogicException(
                    "warehouse with id {$values['warehouseId']} not found!",
                    400
                );
            }

            $transactions[] = new Transaction(
                null,
                $product,
                $values['quantity'],
                $direction,
                (string)date('Y-m-d H:i:s'),
                $warehouseId,
                $values['warehouseId']
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
        $warehouseId = $this->validator->validateVar(trim($args['id']), 'int', 'id');

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
        $warehouseId = $this->validator->validateVar(trim($args['id']), 'int', 'id');
        $date = $this->validator->validateVar(trim($args['date']), 'date', 'date');

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
        $warehouseId = $this->validator->validateVar(trim($args['id']), 'int', 'id');

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