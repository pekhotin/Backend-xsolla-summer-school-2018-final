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
        $this->jsonSchemaValidator->checkBySchema($bodyParams, __DIR__ . '/../../resources/jsonSchema/product.json');
        $sku = $this->validateVar(trim($bodyParams['sku']), 'int', 'sku');

        if ($this->productService->getOneBySku($sku, $this->user) !== null) {
            throw new \LogicException("product with sku {$sku} already exists!", 400);
        }

        $product = new Product(
            null,
            $sku,
            $this->validateVar(trim($bodyParams['name']), 'string', 'name'),
            $this->validateVar(trim($bodyParams['price']), 'float', 'price'),
            $this->validateVar(trim($bodyParams['size']), 'int', 'size'),
            $this->validateVar(trim($bodyParams['type']), 'string', 'type')
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

        $sku = $this->validateVar(trim($args['sku']), 'int', 'sku');
        $product = $this->productService->getOneBySku($sku, $this->user);

        if ($product === null) {
            throw new \LogicException("product with sku {$sku} not found!", 404);
        }

        $newSku = null;
        if (isset($bodyParams['sku'])) {
            $newSku = $this->validateVar(trim($bodyParams['sku']), 'int', 'sku');
            if ($this->productService->getOneBySku($newSku, $this->user) !== null) {
                throw new \LogicException("product with sku {$newSku} already exists!", 400);
            }
        }

        $name = null;
        if (isset($bodyParams['name'])) {
            $name = $this->validateVar(trim($bodyParams['name']), 'string)', 'name');
        }

        $price = null;
        if (isset($bodyParams['price'])) {
            $price = $this->validateVar(trim($bodyParams['price']), 'float', 'price');
        }

        $size = null;
        if (isset($bodyParams['size'])) {
            $size = $this->validateVar(trim($bodyParams['size']), 'int', 'size');
            if ($this->transactionService->getMovementsByProduct($product->getId()) !== null) {
                throw new \LogicException("product with id {$product->getId()} already participated in the movements", 400);
            }
        }

        $type = null;
        if (isset($bodyParams['type'])) {
            $type = $this->validateVar(trim($bodyParams['type']), 'string', 'type');
        }

        if ($name === null && $price === null && $size === null  && $type === null && $sku === null) {
            throw new \LogicException('updates parameters are not found!', 400);
        }

        $product = new Product(
            $product->getId(),
            $newSku,
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
        $sku = $this->validateVar(trim($args['sku']), 'int', 'sku');
        $product = $this->productService->getOneBySku($sku, $this->user);

        if ($product === null) {
            throw new \LogicException(
                "product with sku {$sku} not found!",
                404
            );
        }

        if ($this->transactionService->getMovementsByProduct($product->getId()) !== null) {
            throw new \LogicException(
                "product with sku {$sku} already participated in the movements",
                400
            );
        }

        $this->productService->remove($product->getId());

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

        $sku = $this->validateVar(trim($args['sku']), 'int', 'sku');
        $product = $this->productService->getOneBySku($sku, $this->user);

        if ($product === null) {
            throw new \LogicException("product with sku {$sku} not found!", 404);
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
        $sku = $this->validateVar(trim($args['sku']), 'int', 'sku');

        $product = $this->productService->getOneBySku($sku, $this->user);
        if ($product === null) {
            throw new \LogicException("product with sku {$sku} not found!", 404);
        }

        $residues = $this->stateService->getResiduesByProduct($product->getId());
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
        $sku= $this->validateVar(trim($args['sku']), 'int', 'sku');
        $date = $this->validateVar(trim($args['date']), 'date', 'date');
        $product = $this->productService->getOneBySku($sku, $this->user);
        if ($product === null) {
            throw new \LogicException("product with sku {$sku} not found!", 404);
        }

        $residues = $this->stateService->getResiduesByProductForDate($product->getId(), $date);

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
        $sku = $this->validateVar(trim($args['sku']), 'int', 'sku');
        $product = $this->productService->getOneBySku($sku, $this->user);
        if ($product === null) {
            throw new \LogicException("product with sku {$sku} not found!", 404);
        }

        $transactions = $this->transactionService->getMovementsByProduct($product->getId());
        return $response->withJson($transactions);
    }
}
