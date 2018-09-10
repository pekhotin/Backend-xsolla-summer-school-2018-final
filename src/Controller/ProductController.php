<?php

namespace App\Controller;

use App\Service\StateService;
use App\Service\TransactionService;
use App\Service\UserService;
use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Model\Product;
use App\Service\ProductService;

class ProductController extends BaseController
{
    /**
     * @var ProductService
     */
    private $productService;

    /**
     * @var StateService
     */
    private $stateService;

    /**
     * @var TransactionService
     */
    private $transactionService;

    /**
     * ProductController constructor.
     *
     * @param App $app
     * @param UserService $userService
     * @param ProductService $productService
     * @param StateService $stateService
     * @param TransactionService $transactionService
     */
    public function __construct(
        App $app,
        UserService $userService,
        ProductService $productService,
        StateService $stateService,
        TransactionService $transactionService
    )
    {
        parent::__construct($app, $userService);
        $this->productService = $productService;
        $this->stateService = $stateService;
        $this->transactionService = $transactionService;
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function addProduct(Request $request, Response $response)
    {
        $this->initUser($request);
        $bodyParams = $request->getParsedBody();
        //json схема
        $name = $this->validateVar(trim($bodyParams['name']), 'string', 'name');
        //проверить имя на уникальность
        $price = $this->validateVar(trim($bodyParams['price']), 'float', 'price');
        $size = $this->validateVar(trim($bodyParams['size']), 'int', 'size');
        $type = $this->validateVar(trim($bodyParams['type']), 'string', 'type');

        $product = new Product(
            null,
            $name,
            $price,
            $size,
            $type
        );

        $this->productService->add($product, $this->user);

        return $response->withJson($product->getProductArray(), 201);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     *
     * @return Response
     */
    public function updateProduct(Request $request, Response $response, $args)
    {
        $this->initUser($request);
        $bodyParams = $request->getParsedBody();

        $id = $this->validateVar(trim($args['id']), 'int', 'id');
        if ($this->productService->getOne($id, $this->user) === null) {
            throw new \LogicException("product with id {$id} not found!", 404);
        }

        $name = null;
        //проверяем имя на уникальность
        if (isset($bodyParams['name'])) {
            $name = $this->validateVar(trim($bodyParams['name']), 'string)', 'name');
        }

        $price = null;
        if (isset($bodyParams['price'])) {
            $price = $this->validateVar(trim($bodyParams['price']), 'float', 'price');
        }

        $size = null;
        //если продукт участвовал в перемещениях, мы не можеим изменить его размер
        if (isset($bodyParams['size'])) {
            $size = $this->validateVar(trim($bodyParams['size']), 'int', 'size');
        }

        $type = null;
        if (isset($bodyParams['type'])) {
            $type = $this->validateVar(trim($bodyParams['type']), 'string', 'type');
        }

        if ($name === null && $price === null && $size === null  && $type === null ) {
            throw new \LogicException('updates parameters are not found!', 400);
        }

        $product = new Product(
            $id,
            $name,
            $price,
            $size,
            $type
        );

        $product = $this->productService->update($product, $this->user);

        return $response->withJson($product->getProductArray(), 200);
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
    public function deleteProduct(Request $request, Response $response, $args)
    {
        $this->initUser($request);
        $id = $this->validateVar(trim($args['id']), 'int', 'id');

        if ($this->productService->getOne($id, $this->user) === null) {
            throw new \LogicException("product with id {$id} not found!", 404);
        }

        if ($this->transactionService->getMovementsByProduct($id) !== null) {
            throw new \LogicException("product with id {$id} already participated in the movements", 400);
        }

        $this->productService->remove($id);

        return $response->withStatus(204);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     *
     * @return Response
     */
    public function getProduct(Request $request, Response $response, $args)
    {
        $this->initUser($request);

        $id = $this->validateVar(trim($args['id']), 'int', 'id');
        $product = $this->productService->getOne($id, $this->user);
        if ($product === null) {
            throw new \LogicException("product with id {$id} not found!", 404);
        }

        return $response->withJson($product->getProductArray(), 200);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function getAllProducts(Request $request, Response $response)
    {
        //добавить лимит?
        $this->initUser($request);
        $products = $this->productService->getAll($this->user);
        $productsArray = [];

        foreach ($products as $product) {
            $productsArray[] = $product->getProductArray();
        }

        return $response->withJson($productsArray, 200);
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
        $productId = $this->validateVar(trim($args['id']), 'int', 'id');

        if ($this->productService->getOne($productId, $this->user) === null) {
            throw new \LogicException("product with id {$productId} not found!", 404);
        }

        $products = $this->stateService->getResiduesByProduct($productId);
        return $response->withJson($products, 200);
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
        $productId = $this->validateVar(trim($args['id']), 'int', 'id');
        $date = $this->validateVar(trim($args['date']), 'date', 'date');

        if ($this->productService->getOne($productId, $this->user) === null) {
            throw new \LogicException("product with id {$productId} not found!", 404);
        }

        $products = $this->stateService->getResiduesByProductForDate($productId, $date);

        return $response->withJson($products, 200);
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
        $productId = $this->validateVar(trim($args['id']), 'int', 'id');

        if ($this->productService->getOne($productId, $this->user) === null) {
            throw new \LogicException("getResidues() product with id {$productId} ' not found!", 404);
        }

        $transactions = $this->transactionService->getMovementsByProduct($productId);

        return $response->withJson($transactions, 200);
    }
}