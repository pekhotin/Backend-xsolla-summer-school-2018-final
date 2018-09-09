<?php

namespace App\Controller;

use App\Model\ProductBatch;
use App\Model\Transaction;

use App\Service\TransactionService;
use App\Service\{UserService, WarehouseService, StateService, ProductService};
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
    public function addWarehouse(Request $request, Response $response) {
        try {
            $this->initUser($request);
            $bodyParams = $request->getParsedBody();
            $address = $this->validateVar(trim($bodyParams['address']), 'string');
            $capacity = $this->validateVar(trim($bodyParams['capacity']), 'int');

            $warehouse = new Warehouse(
                null,
                $address,
                $capacity
            );

            if ($this->warehouseService->getOneByAddress($address, $this->user) !== null) {
                throw new \LogicException(__CLASS__ . ' addWarehouse() warehouse with this address already exists!');
            }

            $this->warehouseService->add($warehouse, $this->user);

            return $response
                ->withStatus(201)
                ->withHeader('Content-Type', 'application/json')
                ->withJson($warehouse->getWarehouseArray());

        } catch(\LogicException $exception) {

            error_log($exception->getMessage());

            return $response
                ->withStatus(400)
                ->withHeader('Content-Type', 'application/json');
        }
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
        try {
            $this->initUser($request);
            $bodyParams = $request->getParsedBody();

            $id = $this->validateVar(trim($args['id']), 'int');
            //$warehouse = ;

            if ($this->warehouseService->getOne($id, $this->user) === null) {
                throw new \LogicException(__CLASS__ . ' updateWarehouse() warehouse with id ' . $id . ' not found!', 404);
            }

            $address = null;

            if (isset($bodyParams['address'])) {
                $address = $this->validateVar(trim($bodyParams['address']), 'string');
                if ($this->warehouseService->getOneByAddress($address, $this->user) !== null) {
                    throw new \LogicException(__CLASS__ . ' updateWarehouse() warehouse with this address already exists!');
                }
            }

            $capacity = null;

            if (isset($bodyParams['capacity'])) {
                $capacity = $this->validateVar(trim($bodyParams['capacity']), 'int');
                if ($this->stateService->getFilling($id) > $capacity) {
                    throw new \LogicException(__CLASS__ . ' updateWarehouse() new capacity can not be less than filling!');
                }
            }

            if ($address === null && $capacity === null) {
                throw new \LogicException(__CLASS__ . ' updateWarehouse() updates parameters are not found!');
            }

            $warehouse = new Warehouse(
                $id,
                $address,
                $capacity
            );

            $warehouse = $this->warehouseService->update($warehouse, $this->user);

            return $response
                ->withStatus(200)
                ->withHeader('Content-Type', 'application/json')
                ->withJson($warehouse->getWarehouseArray());

        } catch(\LogicException $exception) {

            error_log($exception->getMessage());
            $code = 400;

            if ($exception->getCode() === 404) {
                $code = 404;
            }

            return $response
                ->withStatus($code)
                ->withHeader('Content-Type', 'application/json');
        }
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
        try {
            $this->initUser($request);
            $id = $this->validateVar(trim($args['id']), 'int');

            if ($this->warehouseService->getOne($id, $this->user) === null) {
                throw new \LogicException(__CLASS__ . ' deleteWarehouse() warehouse with id ' . $id . ' not found!', 404);
            }

            if ($this->transactionService->findByWarehouseId($id) !== null) {
                throw new \LogicException(__CLASS__ . " deleteWarehouse() warehouse with id {$id} already participated in the movement!");
            }
            $this->warehouseService->remove($id);

            return $response
                ->withStatus(204)
                ->withHeader('Content-Type', 'application/json');

        } catch(\LogicException $exception) {

            error_log($exception->getMessage());
            $code = 400;

            if ($exception->getCode() === 404) {
                $code = 404;
            }

            return $response
                ->withStatus($code)
                ->withHeader('Content-Type', 'application/json');
        }
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
        try {
            $this->initUser($request);
            $id = $this->validateVar(trim($args['id']), 'int');
            $warehouse = $this->warehouseService->getOne($id, $this->user);

            if($warehouse === null) {
                throw new \LogicException(__CLASS__ . ' getWarehouse() warehouse with id ' . $id . ' not found!', 404);
            }

            return $response
                ->withStatus(200)
                ->withHeader('Content-Type', 'application/json')
                ->withJson($warehouse->getWarehouseArray());

        } catch(\LogicException $exception) {

            error_log($exception->getMessage());
            $code = 400;

            if ($exception->getCode() === 404) {
                $code = 404;
            }

            return $response
                ->withStatus($code)
                ->withHeader('Content-Type', 'application/json');
        }
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function getAllWarehouses(Request $request, Response $response)
    {
        try {
            $this->initUser($request);
            $warehouses = $this->warehouseService->getAll($this->user);

            $warehousesArray = [];

            foreach ($warehouses as $warehouse) {
                $warehousesArray[] = $warehouse->getWarehouseArray();
            }

            return $response
                ->withStatus(200)
                ->withHeader('Content-Type', 'application/json')
                ->withJson($warehousesArray);

        } catch(\LogicException $exception) {

            error_log($exception->getMessage());

            return $response
                ->withStatus(404)
                ->withHeader('Content-Type', 'application/json');
        }
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     *
     * @return Response
     */
    public function receiptProducts(Request $request, Response $response, $args)
    {
        try {
            //добавить добавление нескольких товаров
            $this->initUser($request);
            $direction = 'receipt';
            $bodyParams = $request->getParsedBody();

            $warehouseId = $this->validateVar(trim($args['id']), 'int');
            $productId = $this->validateVar(trim($bodyParams['productId']), 'int');
            $quantity = $this->validateVar(trim($bodyParams['quantity']), 'int');
            $sender = $this->validateVar(trim($bodyParams['sender']), 'string');

            $warehouse = $this->warehouseService->getOne($warehouseId, $this->user);

            if ($warehouse === null) {
                throw new \LogicException(__CLASS__ . ' receiptProducts() warehouse with id ' . $warehouseId . ' not found!', 404);
            }

            $product = $this->productService->getOne($productId, $this->user);
            if ($product === null) {
                throw new \LogicException(__CLASS__ . ' receiptProducts() product with id ' . $productId . ' not found!', 404);
            }

            $filling = $this->stateService->getFilling($warehouseId);
            if (($warehouse->getCapacity() - $filling) < ($quantity * $product->getSize())) {
                throw new \LogicException(__CLASS__ . ' receiptProducts() not enough space on warehouse!');
            }

            $transaction = new Transaction(
                null,
                $warehouseId,
                $productId,
                $quantity,
                $direction,
                (string)date('Y-m-d H:i:s'),
                $sender,
                null

            );

            $this->transactionService->add($transaction);
            $this->stateService->addProducts($warehouseId, $productId, $quantity);

            return $response
                ->withStatus(200)
                ->withHeader('Content-Type', 'application/json')
                ->withJson([
                    'transactionId' => $transaction->getId(),
                    'productId' => $productId,
                    'quantity' => $transaction->getQuantity(),
                    'from' => $transaction->getSender(),
                    'to' => $warehouseId
                ]);

        } catch (\LogicException $exception) {

            error_log($exception->getMessage());

            return $response
                ->withStatus(400)
                ->withHeader('Content-Type', 'application/json');
        }
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     *
     * @return Response
     *
     * @throws \Doctrine\DBAL\Exception\InvalidArgumentException
     */
    public function dispatchProducts(Request $request, Response $response, $args)
    {
        try {
            //добавить отправление нескольких товаров
            //выводить error json массив
            $this->initUser($request);
            $direction = 'dispatch';
            $bodyParams = $request->getParsedBody();

            $warehouseId = $this->validateVar(trim($args['id']), 'int');
            $productId = $this->validateVar(trim($bodyParams['productId']), 'int');
            $quantity = $this->validateVar(trim($bodyParams['quantity']), 'int');
            $recipient = $this->validateVar(trim($bodyParams['recipient']), 'string');

            if ($this->validateVar(trim($bodyParams['productId']), 'int') === null) {
                throw new \LogicException(__CLASS__ . ' dispatchProducts() product with id ' . $productId . ' not found!', 404);
            }

            if ($this->warehouseService->getOne($warehouseId, $this->user) === null) {
                throw new \LogicException(__CLASS__ . ' dispatchProducts() warehouse with id ' . $warehouseId . ' not found!', 404);
            }

            $thisQuantity = $this->stateService->quantityProductInWarehouse($warehouseId, $productId);

            if ($thisQuantity < $quantity) {
                throw new \LogicException(__CLASS__ . ' dispatchProducts() not enough product on warehouse!', 400);
            }

            $transaction = new Transaction(
                null,
                $warehouseId,
                $productId,
                $quantity,
                $direction,
                (string)date('Y-m-d H:i:s'),
                null,
                $recipient
            );

            $this->transactionService->add($transaction);
            $this->stateService->removeProducts($warehouseId, $productId, $quantity);

            return $response
                ->withStatus(200)
                ->withHeader('Content-Type', 'application/json')
                ->withJson([
                    'transactionId' => $transaction->getId(),
                    'productId' => $productId,
                    'quantity' => $transaction->getQuantity(),
                    'from' => $warehouseId,
                    'to' => $transaction->getRecipient()
                ]);

        } catch (\LogicException $exception) {
            error_log($exception->getMessage());
            return $response
                ->withStatus($exception->getCode())
                ->withHeader('Content-Type', 'application/json');
        }
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     *
     * @return Response
     *
     * @throws \Doctrine\DBAL\Exception\InvalidArgumentException
     */
    public function movementProducts(Request $request, Response $response, $args)
    {
        try {
            //отправление нескольких товаров
            $this->initUser($request);
            $direction = 'betweenWarehouses';
            $bodyParams = $request->getParsedBody();

            $warehouseId = $this->validateVar(trim($args['id']), 'int');
            $productId = $this->validateVar(trim($bodyParams['productId']), 'int');
            $quantity = $this->validateVar(trim($bodyParams['quantity']), 'int');
            $newWarehouseId = $this->validateVar(trim($args['warehouseId']), 'int');

            $product = $this->productService->getOne($productId, $this->user);
            $newWarehouse = $this->warehouseService->getOne($newWarehouseId, $this->user);

            if ($this->warehouseService->getOne($warehouseId, $this->user) === null) {
                throw new \LogicException(__CLASS__ . ' movementProducts() warehouse with id ' . $warehouseId . ' not found!', 404);
            }
            if ($product === null) {
                throw new \LogicException(__CLASS__ . ' movementProducts() product with id ' . $warehouseId . ' not found!', 404);
            }
            if ($newWarehouse === null) {
                throw new \LogicException(__CLASS__ . ' movementProducts() warehouse with id ' . $newWarehouseId . ' not found!', 404);
            }

            $thisQuantity = $this->stateService->quantityProductInWarehouse($warehouseId, $productId);
            $filling = $this->stateService->getFilling($newWarehouseId);

            if ($thisQuantity < $quantity) {
                throw new \LogicException(__CLASS__ . " movementProducts() not enough product on warehouse with id {$warehouseId}!");
            }
            if (($newWarehouse->getCapacity() - $filling) < ($quantity * $product->getSize())) {
                throw new \LogicException(__CLASS__ . " movementProducts() not enough space on warehouse with id {$newWarehouseId}!");
            }

            $transaction = new Transaction(
                null,
                $warehouseId,
                $productId,
                $quantity,
                $direction,
                (string)date('Y-m-d'),
                null,
                $newWarehouseId
            );

            $this->transactionService->add($transaction);
            $this->stateService->movementProducts($warehouseId, $productId, $quantity, $newWarehouseId);

            return $response
                ->withStatus(201)
                ->withHeader('Content-Type', 'application/json')
                ->withJson([
                    'transactionId' => $transaction->getId(),
                    'productId' => $productId,
                    'quantity' => $transaction->getQuantity(),
                    'from' => $warehouseId,
                    'to' => $newWarehouseId
                ]);

        } catch (\LogicException $exception) {

            error_log($exception->getMessage());
            $code = 400;

            if ($exception->getCode() === 404) {
                $code = 404;
            }

            return $response
                ->withStatus($code)
                ->withHeader('Content-Type', 'application/json');
        }
    }
}