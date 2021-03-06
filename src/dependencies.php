<?php

use \Psr\Container\ContainerInterface;
use \App\Controller\{
	ProductController,
    WarehouseController,
    UserController
};
use App\Service\CustomHandler;
use App\Service\{
    ConnectionFactory,
    WarehouseService,
    ProductService,
    UserService,
    StateService,
    TransactionService,
    AuthenticationFactory
};
use App\Repository\{
    WarehouseRepository,
    ProductRepository,
    UserRepository,
    StateRepository,
    TransactionRepository
};

$container = $app->getContainer();

$container['dbal'] = function () {
    return ConnectionFactory::getConnection();
};

$container['errorHandler'] = function () {
    return new CustomHandler();
};
$app->add(AuthenticationFactory::getAuthentication());

$container['user.controller'] = function (ContainerInterface $c) use ($app) {
    return new UserController($app, $c->get('user.service'));
};
$container['user.service'] = function (ContainerInterface $c) use ($app) {
    return new UserService($c->get('user.repository'));
};
$container['user.repository'] = function (ContainerInterface $c) use ($app) {
    return new UserRepository($c->get('dbal'));
};

$container['state.service'] = function (ContainerInterface $c) use ($app) {
    return new StateService($c->get('state.repository'));
};
$container['state.repository'] = function (ContainerInterface $c) use ($app) {
    return new StateRepository($c->get('dbal'));
};

$container['product.controller'] = function (ContainerInterface $c) use ($app) {
    return new ProductController(
        $app,
        $c->get('user.service'),
        $c->get('product.service'),
        $c->get('state.service'),
        $c->get('transaction.service')
    );
};
$container['product.service'] = function (ContainerInterface $c) use ($app) {
    return new ProductService($c->get('product.repository'));
};
$container['product.repository'] = function (ContainerInterface $c) use ($app) {
    return new ProductRepository($c->get('dbal'));
};

$container['transaction.service'] = function (ContainerInterface $c) use ($app) {
    return new TransactionService($c->get('transaction.repository'));
};
$container['transaction.repository'] = function (ContainerInterface $c) use ($app) {
    return new TransactionRepository($c->get('dbal'));
};

$container['warehouse.controller'] = function (ContainerInterface $c) use ($app) {
    return new WarehouseController(
        $app,
        $c->get('user.service'),
        $c->get('warehouse.service'),
        $c->get('state.service'),
        $c->get('product.service'),
        $c->get('transaction.service')
    );
};
$container['warehouse.service'] = function (ContainerInterface $c) use ($app) {
    return new WarehouseService($c->get('warehouse.repository'));
};
$container['warehouse.repository'] = function (ContainerInterface $c) use ($app) {
    return new WarehouseRepository($c->get('dbal'));
};
