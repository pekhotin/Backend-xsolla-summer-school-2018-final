<?php

namespace App\Controller;

use Slim\App;

abstract class BaseController
{
    /**
     * @var App
     */
    protected $app;

    public function __construct(App $app)
    {
        $this->app = $app;
    }

    public function __invoke()
    {

    }
}