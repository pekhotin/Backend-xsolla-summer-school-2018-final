<?php

$app->group('/api/v1', function () use ($app) {

    $app->post('/users', 'user.controller:register');

    $app->get('/warehouses', 'warehouse.controller:getAllWarehouses');
    $app->get('/warehouses/{id}', 'warehouse.controller:getWarehouse');
    $app->post('/warehouses', 'warehouse.controller:addWarehouse');
    $app->put('/warehouses/{id}', 'warehouse.controller:updateWarehouse');
    $app->delete('/warehouses/{id}', 'warehouse.controller:deleteWarehouse');

    $app->put('/warehouses/{id}/receipt', 'warehouse.controller:receiptProducts');
    $app->put('/warehouses/{id}/dispatch', 'warehouse.controller:dispatchProducts');
    $app->put('/warehouses/{id}/movement', 'warehouse.controller:movementProducts');
    $app->get('/warehouses/{id}/residues', 'warehouse.controller:getResidues');
    $app->get('/warehouses/{id}/residues/{date}', 'warehouse.controller:getResiduesForDate');
    $app->get('/warehouses/{id}/movements', 'warehouse.controller:getMovements');

    $app->get('/products', 'product.controller:getAllProducts');
    $app->get('/products/{id}', 'product.controller:getProduct');
    $app->post('/products', 'product.controller:addProduct');
    $app->put('/products/{id}', 'product.controller:updateProduct');
    $app->delete('/products/{id}', 'product.controller:deleteProduct');

    $app->get('/products/{id}/residues', 'product.controller:getResidues');
    $app->get('/products/{id}/residues/{date}', 'product.controller:getResiduesForDate');
    $app->get('/products/{id}/movements', 'product.controller:getMovements');
});
