<?php

namespace App\Controller;

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

    /**
     * @param string $var
     * @param string $type
     *
     * @return float|int|null|string
     */
    protected function validateVar($var, $type)
    {
        switch ($type) {

            case 'int':
                if (filter_var($var, FILTER_VALIDATE_INT) === false) {
                    throw new \LogicException(__CLASS__ . " validateVar() {$var} is not integer!");
                }
                return (int)$var;

            case 'string':
                if (empty($var)) {
                    throw new \LogicException(__CLASS__ . " validateVar() {$var} is empty!");
                }
                return (string)$var;

            case 'float':
                if (filter_var($var, FILTER_VALIDATE_FLOAT) === false) {
                    throw new \LogicException(__CLASS__ . " validateVar() {$var} is not float!");
                }
                return (float)$var;
            case 'email':
                if(filter_var($var, FILTER_VALIDATE_EMAIL) === false) {
                    throw new \LogicException(__CLASS__ . " validateVar() {$var} is not email!");
                }
                return (string)$var;
            default:
                return null;
        }
    }
}