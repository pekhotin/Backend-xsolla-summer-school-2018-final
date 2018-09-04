<?php

require __DIR__ . '/../vendor/autoload.php';

date_default_timezone_set('Asia/Yekaterinburg');

session_start();

$app = new \Slim\App;

require __DIR__ . '/../src/dependencies.php';

require __DIR__ . '/../src/routes.php';

$app->run();
