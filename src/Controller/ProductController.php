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
    public function addProduct(Request $request, Response $response) {
        try {
            $this->initUser($request);
            $bodyParams = $request->getParsedBody();
            //json схема
            $name = $this->validateVar(trim($bodyParams['name']), 'string');
            //проверить имя на уникальность
            $price = $this->validateVar(trim($bodyParams['price']), 'float');
            $size = $this->validateVar(trim($bodyParams['size']), 'int');
            $type = $this->validateVar(trim($bodyParams['type']), 'string');

            $product = new Product(
                null,
                $name,
                $price,
                $size,
                $type
            );

            $this->productService->add($product, $this->user);

            return $response
                ->withStatus(201)
                ->withHeader('Content-Type', 'application/json')
                ->withJson($product->getProductArray());

        } catch(\LogicException $exception) {

            error_log($exception->getMessage());
            $code = 400;

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
    public function updateProduct(Request $request, Response $response, $args)
    {
        try {
            $this->initUser($request);
            $bodyParams = $request->getParsedBody();

            $id = $this->validateVar(trim($args['id']), 'int');
            if ($this->productService->getOne($id, $this->user) === null) {
                throw new \LogicException(__CLASS__ . ' updateProduct () product with id ' . $id . ' not found!', 404);
            }

            $name = null;
            //проверяем имя на уникальность
            if (isset($bodyParams['name'])) {
                $name = $this->validateVar(trim($bodyParams['name']), 'string)');
            }

            $price = null;
            if (isset($bodyParams['price'])) {
                $price = $this->validateVar(trim($bodyParams['price']), 'float');
            }

            $size = null;
            //если продукт участвовал в перемещениях, мы не можеим изменить его размер
            if (isset($bodyParams['size'])) {
                $size = $this->validateVar(trim($bodyParams['size']), 'int');
            }

            $type = null;
            if (isset($bodyParams['type'])) {
                $type = $this->validateVar(trim($bodyParams['type']), 'string');
            }

            if ($name === null && $price === null && $size === null  && $type === null ) {
                throw new \LogicException(__CLASS__ . ' updateProduct() updates parameters are not found!');
            }

            $product = new Product(
                $id,
                $name,
                $price,
                $size,
                $type
            );

            $product = $this->productService->update($product, $this->user);

            return $response
                ->withStatus(200)
                ->withHeader('Content-Type', 'application/json')
                ->withJson($product->getProductArray());

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
    public function deleteProduct(Request $request, Response $response, $args)
    {
        try {
            $this->initUser($request);
            $id = $this->validateVar(trim($args['id']), 'int');
            if ($this->productService->getOne($id, $this->user) === null) {
                throw new \LogicException(__CLASS__ . ' deleteProduct () product with id ' . $id . ' not found!', 404);
            }

            //проверить участвовал ли протукт в перемещениях
            $this->productService->remove($id);

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
    public function getProduct(Request $request, Response $response, $args)
    {
        try {
            $this->initUser($request);

            $id = $this->validateVar(trim($args['id']), 'int');
            $product = $this->productService->getOne($id, $this->user);
            if ($product === null) {
                throw new \LogicException(__CLASS__ . ' getProduct() product with id ' . $id . ' not found!', 404);
            }

            return $response
                ->withStatus(200)
                ->withHeader('Content-Type', 'application/json')
                ->withJson($product->getProductArray());

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
    public function getAllProducts(Request $request, Response $response)
    {
        try {

            //добавить лимит?
            $this->initUser($request);
            $products = $this->productService->getAll($this->user);
            $productsArray = [];

            foreach ($products as $product) {
                $productsArray[] = $product->getProductArray();
            }

            return $response
                ->withStatus(200)
                ->withHeader('Content-Type', 'application/json')
                ->withJson($productsArray);

        } catch(\LogicException $exception) {

            error_log($exception->getMessage());

            return $response
                ->withStatus(404)
                ->withHeader('Content-Type', 'application/json');
        }
    }

    public function getResidues(Request $request, Response $response, $args)
    {
        try {
            $this->initUser($request);
            $productId = $this->validateVar(trim($args['id']), 'int');

            if ($this->productService->getOne($productId, $this->user) === null) {
                throw new \LogicException(__CLASS__ . ' getResidues() product with id ' . $productId . ' not found!', 404);
            }

            $products = $this->stateService->getResiduesByProduct($productId);
            return $response
                ->withStatus(201)
                ->withHeader('Content-Type', 'application/json')
                ->withJson($products);

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

    public function getResiduesForDate(Request $request, Response $response, $args)
    {
        try {
            $this->initUser($request);
            $productId = $this->validateVar(trim($args['id']), 'int');
            $date = $this->validateVar(trim($args['date']), 'date');

            if ($this->productService->getOne($productId, $this->user) === null) {
                throw new \LogicException(__CLASS__ . ' getResiduesForDate() product with id ' . $productId . ' not found!', 404);
            }

            $products = $this->stateService->getResiduesByProductForDate($productId, $date);
            return $response
                ->withStatus(201)
                ->withHeader('Content-Type', 'application/json')
                ->withJson($products);

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

    public function getMovements(Request $request, Response $response, $args)
    {
        try {
            $this->initUser($request);
            $productId = $this->validateVar(trim($args['id']), 'int');

            if ($this->productService->getOne($productId, $this->user) === null) {
                throw new \LogicException(__CLASS__ . ' getResidues() product with id ' . $productId . ' not found!', 404);
            }

            $transactions = $this->transactionService->getMovementsByProduct($productId);
            return $response
                ->withStatus(201)
                ->withHeader('Content-Type', 'application/json')
                ->withJson($transactions);

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