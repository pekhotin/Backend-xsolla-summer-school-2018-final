<?php

namespace App\Controller;

use App\Validator\BaseValidator;
use JsonSchema\Validator;
use Slim\App;
use App\Service\UserService;
use Slim\Http\Request;
use App\Model\User;

abstract class BaseController
{
    /**
     * @var App
     */
    protected $app;

    /**
     * @var UserService
     */
    protected $userService;

    /**
     * @var User
     */
    protected $user;
    /**
     * @var BaseValidator
     */
    protected $validator;

    /**
     * BaseController constructor.
     *
     * @param App $app
     * @param UserService $userService
     */
    public function __construct(App $app, UserService $userService)
    {
        $this->app = $app;
        $this->userService = $userService;
    }

    protected function initUser(Request $request)
    {
        $login = $request->getAttribute('user');
        $this->user = $this->userService->getOneByLogin($login);
    }
}