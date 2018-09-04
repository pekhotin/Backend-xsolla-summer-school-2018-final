<?php

namespace App\Controller;

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
     * ProductController constructor.
     *
     * @param App $app
     * @param ProductService $productService
     */
    public function __construct(App $app, ProductService $productService)
    {
        parent::__construct($app);
        $this->productService = $productService;
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function addProduct(Request $request, Response $response) {
        try {

            $bodyParams = $request->getParsedBody();

            if (!isset($bodyParams['name']) || empty(trim($bodyParams['name']))) {
                throw new \LogicException(__CLASS__ . ' register() name is undefined!');
            }

            if (!isset($bodyParams['price']) || !filter_var($bodyParams['price'], FILTER_VALIDATE_FLOAT)) {
                throw new \LogicException(__CLASS__ . ' addProduct() price is undefined!');
            }

            if (!isset($bodyParams['size']) || !filter_var($bodyParams['size'], FILTER_VALIDATE_INT)) {
                throw new \LogicException(__CLASS__ . ' addProduct() size is undefined!');
            }

            if (!isset($bodyParams['type']) || empty(trim($bodyParams['type']))) {
                throw new \LogicException(__CLASS__ . ' addProduct() type is undefined!');
            }

            $product = new Product(
                null,
                (string)trim($bodyParams['name']),
                (float)$bodyParams['price'],
                (int)$bodyParams['size'],
                (string)trim($bodyParams['type'])
            );

            $this->productService->add($product);

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

            $bodyParams = $request->getParsedBody();

            if (!filter_var($args['id'], FILTER_VALIDATE_INT)) {
                throw new \LogicException(__CLASS__ . ' updateProduct() id is not integer!');
            }

            $name = null;

            if (isset($bodyParams['name'])) {
                if (empty(trim($bodyParams['name']))) {
                    throw new \LogicException(__CLASS__ . ' updateProduct() name is undefined!');
                }
                $name = (string)trim($bodyParams['name']);
            }

            $price = null;

            if (isset($bodyParams['price'])) {
                if (!filter_var($bodyParams['price'], FILTER_VALIDATE_FLOAT)) {
                    throw new \LogicException(__CLASS__ . ' updateProduct() price is not float!');
                }
                $price = (float)$bodyParams['price'];
            }

            $size = null;

            if (isset($bodyParams['size'])) {
                if (!filter_var($bodyParams['size'], FILTER_VALIDATE_INT)) {
                    throw new \LogicException(__CLASS__ . ' updateProduct() size is not integer!');
                }
                $size = (int)$bodyParams['size'];
            }

            $type = null;

            if (isset($bodyParams['type'])) {
                if (empty(trim($bodyParams['type']))) {
                    throw new \LogicException(__CLASS__ . ' updateProduct() name is undefined!');
                }
                $type = (string)trim($bodyParams['type']);
            }

            if ($name === null && $price === null && $size === null  && $type === null ) {
                throw new \LogicException(__CLASS__ . ' updateProduct() updates parameters are not found!');
            }

            $product = new Product(
                (int)$args['id'],
                $name,
                $price,
                $size,
                $type
            );

            $product = $this->productService->update($product);

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

            if (!filter_var($args['id'], FILTER_VALIDATE_INT)) {
                throw new \LogicException(__CLASS__ . ' deleteProduct() id is not integer!');
            }

            $product = $this->productService->getOne((int)$args['id']);
            $this->productService->remove($product);

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
     */
    public function getProduct(Request $request, Response $response, $args)
    {
        try {

            if (!filter_var($args['id'], FILTER_VALIDATE_INT)) {
                throw new \LogicException(__CLASS__ . ' getProduct() id is not integer!');
            }

            $product = $this->productService->getOne($args['id']);

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

            $products = $this->productService->getAll();
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


}