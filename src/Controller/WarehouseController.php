<?php

namespace App\Controller;

use App\Model\ProductBatch;
use App\Model\Transaction;

use App\Service\TransactionService;
use App\Service\{
    WarehouseService,
    ProductBatchService,
    ProductService
};
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
     * @var ProductBatchService
     */
    private $productBatchService;

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
     * @param App $app
     * @param WarehouseService $warehouseService
     * @param ProductBatchService $productBatchService
     * @param ProductService $productService
     * @param TransactionService $transactionService
     */
    public function __construct(
        App $app,
        WarehouseService $warehouseService,
        ProductBatchService $productBatchService,
        ProductService $productService,
        TransactionService $transactionService)
    {
        parent::__construct($app);
        $this->warehouseService = $warehouseService;
        $this->productBatchService = $productBatchService;
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

            $bodyParams = $request->getParsedBody();

            if (!isset($bodyParams['address']) || empty(trim($bodyParams['address']))) {
                throw new \LogicException(__CLASS__ . ' addWarehouse() address is undefined!');
            }

            if (!isset($bodyParams['capacity']) || !filter_var($bodyParams['capacity'], FILTER_VALIDATE_INT)) {
                throw new \LogicException(__CLASS__ . ' addWarehouse() capacity is undefined!');
            }

            $warehouse = new Warehouse(
                null,
                (string)trim($bodyParams['address']),
                (int)$bodyParams['capacity']
            );

            $this->warehouseService->add($warehouse);

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

            $bodyParams = $request->getParsedBody();

            if (!filter_var($args['id'], FILTER_VALIDATE_INT)) {
                throw new \LogicException(__CLASS__ . ' updateWarehouse() warehouse id is not integer!');
            }

            $address = null;

            if (isset($bodyParams['address'])) {
                if (empty(trim($bodyParams['address']))) {
                    throw new \LogicException(__CLASS__ . ' updateWarehouse() address is undefined!');
                }
                $address = (string)trim($bodyParams['address']);
            }

            $capacity = null;

            if (isset($bodyParams['capacity'])) {
                if (!filter_var($bodyParams['capacity'], FILTER_VALIDATE_INT)) {
                    throw new \LogicException(__CLASS__ . ' updateWarehouse() capacity is not integer!');
                }
                $capacity = (int)$bodyParams['capacity'];
            }

            if ($address === null && $capacity === null) {
                throw new \LogicException(__CLASS__ . ' updateWarehouse() updates parameters are not found!');
            }

            $warehouse = new Warehouse(
                (int)$args['id'],
                $address,
                $capacity
            );

            $warehouse = $this->warehouseService->update($warehouse);

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

            if (!filter_var($args['id'], FILTER_VALIDATE_INT)) {
                throw new \LogicException(__CLASS__ . ' addWarehouse() warehouse id is not integer!');
            }

            $warehouse = $this->warehouseService->getOne((int)$args['id']);

            $warehouse = $this->warehouseService->remove($warehouse);

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
     */
    public function getWarehouse(Request $request, Response $response, $args)
    {
        try {

            if (!filter_var($args['id'], FILTER_VALIDATE_INT)) {
                throw new \LogicException(__CLASS__ . ' getWarehouse() id is not integer!');
            }

            $warehouse = $this->warehouseService->getOne((int)$args['id']);

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
     */
    public function getAllWarehouses(Request $request, Response $response, $args)
    {
        try {

            $warehouses = $this->warehouseService->getAll();

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
            //проверка на размер склада
            //добавить добавление нескольких товаров

            $direction = 'receipt';
            $bodyParams = $request->getParsedBody();

            if (!filter_var($args['id'], FILTER_VALIDATE_INT)) {
                throw new \LogicException(__CLASS__ . ' receiptProducts() id is not integer!');
            }
            $warehouse = $this->warehouseService->getOne((int)$args['id']);

            if (!isset($bodyParams['productId']) || !filter_var($bodyParams['productId'], FILTER_VALIDATE_INT)) {
                throw new \LogicException(__CLASS__ . ' receiptProducts() productId is undefined!');
            }
            $product = $this->productService->getOne((int)$bodyParams['productId']);

            if (!isset($bodyParams['quantity']) || !filter_var($bodyParams['quantity'], FILTER_VALIDATE_INT)) {
                throw new \LogicException(__CLASS__ . ' receiptProducts() quantity is undefined!');
            }
            $quantity = (int)$bodyParams['quantity'];

            if (!isset($bodyParams['sender']) || empty(trim($bodyParams['sender']))) {
                throw new \LogicException(__CLASS__ . ' receiptProducts() sender is undefined!');
            }
            $sender = (string)$bodyParams['sender'];

            $productBatch = new ProductBatch(
                null,
                $product,
                $quantity
            );

            $this->productBatchService->add($productBatch, $warehouse);

            $transaction = new Transaction(
                null,
                $warehouse,
                $product,
                $quantity,
                $direction,
                (string)date('Y-m-d H:i:s'),
                $sender,
                null

            );

            $this->transactionService->add($transaction);

            return $response
                ->withStatus(200)
                ->withHeader('Content-Type', 'application/json')
                ->withJson([
                    'transactionId' => $transaction->getId(),
                    'productId' => $product->getId(),
                    'quantity' => $transaction->getQuantity(),
                    'from' => $transaction->getSender(),
                    'to' => $warehouse->getAddress()
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
     */
    public function dispatchProducts(Request $request, Response $response, $args)
    {
        try {
            //добавить отправление нескольких товаров

            $direction = 'dispatch';
            $bodyParams = $request->getParsedBody();

            if (!filter_var($args['id'], FILTER_VALIDATE_INT)) {
                throw new \LogicException(__CLASS__ . ' dispatchProducts() id is not integer!');
            }
            $warehouse = $this->warehouseService->getOne((int)$args['id']);

            if (!isset($bodyParams['productId']) || !filter_var($bodyParams['productId'], FILTER_VALIDATE_INT)) {
                throw new \LogicException(__CLASS__ . ' dispatchProducts() productId is undefined!');
            }
            $product = $this->productService->getOne((int)$bodyParams['productId']);

            if (!isset($bodyParams['quantity']) || !filter_var($bodyParams['quantity'], FILTER_VALIDATE_INT)) {
                throw new \LogicException(__CLASS__ . ' dispatchProducts() quantity is undefined!');
            }
            $quantity = (int)$bodyParams['quantity'];

            if (!isset($bodyParams['recipient']) || empty(trim($bodyParams['recipient']))) {
                throw new \LogicException(__CLASS__ . ' dispatchProducts() sender is undefined!');
            }
            $recipient = (string)$bodyParams['recipient'];

            $productBatch = new ProductBatch(
                null,
                $product,
                $quantity
            );

            $this->productBatchService->remove($productBatch, $warehouse);

            $transaction = new Transaction(
                null,
                $warehouse,
                $product,
                $quantity,
                $direction,
                (string)date('Y-m-d H:i:s'),
                null,
                $recipient
            );

            $this->transactionService->add($transaction);

            return $response
                ->withStatus(200)
                ->withHeader('Content-Type', 'application/json')
                ->withJson([
                    'transactionId' => $transaction->getId(),
                    'productId' => $product->getId(),
                    'quantity' => $transaction->getQuantity(),
                    'from' => $warehouse->getAddress(),
                    'to' => $transaction->getRecipient()
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

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     *
     * @return Response
     */
    public function movementProducts(Request $request, Response $response, $args)
    {
        try {
            //проверить вместимость склада
            //отправление нескольких товаров

            $direction = 'betweenWarehouses';
            $bodyParams = $request->getParsedBody();

            if (!filter_var($args['id'], FILTER_VALIDATE_INT)) {
                throw new \LogicException(__CLASS__ . ' movementProducts() id is not integer!');
            }
            $warehouse = $this->warehouseService->getOne((int)$args['id']);

            if (!isset($bodyParams['productId']) || !filter_var($bodyParams['productId'], FILTER_VALIDATE_INT)) {
                throw new \LogicException(__CLASS__ . ' movementProducts() productId is undefined!');
            }
            $product = $this->productService->getOne((int)$bodyParams['productId']);

            if (!isset($bodyParams['quantity']) || !filter_var($bodyParams['quantity'], FILTER_VALIDATE_INT)) {
                throw new \LogicException(__CLASS__ . ' movementProducts() quantity is undefined!');
            }
            $quantity = (int)$bodyParams['quantity'];

            if (!isset($bodyParams['warehouseId']) || !filter_var($bodyParams['warehouseId'], FILTER_VALIDATE_INT)) {
                throw new \LogicException(__CLASS__ . ' movementProducts() warehouseId is undefined!');
            }
            $newWarehouse = $this->warehouseService->getOne((int)$bodyParams['warehouseId']);

            $productBatch = new ProductBatch(
                null,
                $product,
                $quantity
            );

            $this->productBatchService->movement($productBatch, $warehouse, $newWarehouse);

            $transaction = new Transaction(
                null,
                $warehouse,
                $product,
                $quantity,
                $direction,
                (string)date('Y-m-d'),
                null,
                $newWarehouse->getId()
            );

            $this->transactionService->add($transaction);

            return $response
                ->withStatus(201)
                ->withHeader('Content-Type', 'application/json')
                ->withJson([
                    'transactionId' => $transaction->getId(),
                    'productId' => $product->getId(),
                    'quantity' => $transaction->getQuantity(),
                    'from' => $warehouse->getAddress(),
                    'to' => $newWarehouse->getAddress()
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