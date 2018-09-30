<?php

namespace App\Controller;

use App\Service\StateService;
use App\Service\TransactionService;
use App\Service\UserService;
use App\Validator\ProductValidator;
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
        $this->validator = new ProductValidator();
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
        $values = $this->validator->validateInsertData($bodyParams);

        if ($this->productService->getOneBySku($values['sku'], $this->user->getId()) !== null) {
            throw new \LogicException("product with sku {$values['sku']} already exists.", 400);
        }

        $product = new Product(
            null,
            $values['sku'],
            $values['name'],
            $values['price'],
            $values['size'],
            $values['type']
        );

        $this->productService->add($product, $this->user->getId());

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

        $sku = $this->validator->validateVar($args['sku'], 'int', 'sku');
        $product = $this->productService->getOneBySku($sku, $this->user->getId());

        if ($product === null) {
            throw new \LogicException("product with sku {$sku} not found.", 404);
        }

        $values = $this->validator->validateUpdateData($bodyParams, $product);

        if (isset($bodyParams['size'])) {
            if ($this->transactionService->getMovementsByProduct($product->getId()) !== null) {
                throw new \LogicException("product with sku {$sku} already participated in the movements.", 400);
            }
        }
        if (isset($bodyParams['sku'])) {
            $productWithSku = $this->productService->getOneBySku($values['sku'], $this->user->getId());
            if ($productWithSku !== null) {
                throw new \LogicException("product with sku {$values['sku']} already exists.", 400);
            }
        }

        $product = new Product(
            $product->getId(),
            $values['sku'],
            $values['name'],
            $values['price'],
            $values['size'],
            $values['type']
        );

        $product = $this->productService->update($product);

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
        $sku = $this->validator->validateVar($args['sku'], 'int', 'sku');
        $product = $this->productService->getOneBySku($sku, $this->user->getId());

        if ($product === null) {
            throw new \LogicException(
                "product with sku {$sku} not found.",
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

        $sku = $this->validator->validateVar($args['sku'], 'int', 'sku');
        $product = $this->productService->getOneBySku($sku, $this->user->getId());

        if ($product === null) {
            throw new \LogicException("product with sku {$sku} not found.", 404);
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
        $products = $this->productService->getAll($this->user->getId());
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
        $sku = $this->validator->validateVar($args['sku'], 'int', 'sku');

        $product = $this->productService->getOneBySku($sku, $this->user->getId());
        if ($product === null) {
            throw new \LogicException("product with sku {$sku} not found.", 404);
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
        $sku= $this->validator->validateVar($args['sku'], 'int', 'sku');
        $date = $this->validator->validateVar(trim($args['date']), 'date', 'date');

        $product = $this->productService->getOneBySku($sku, $this->user->getId());
        if ($product === null) {
            throw new \LogicException("product with sku {$sku} not found.", 404);
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
        $sku = $this->validator->validateVar($args['sku'], 'int', 'sku');
        $product = $this->productService->getOneBySku($sku, $this->user->getId());
        if ($product === null) {
            throw new \LogicException("product with sku {$sku} not found.", 404);
        }

        $transactions = $this->transactionService->getMovementsByProduct($product->getId());
        return $response->withJson($transactions);
    }
}
