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
     * @param $var
     * @param string $type
     * @param string $name
     * @return float|int|null|string
     */
    protected function validateVar($var, $type, $name)
    {
        switch ($type) {

            case 'int':
                if (filter_var($var, FILTER_VALIDATE_INT) === false) {
                    throw new \LogicException("{$name} is not integer!", 400);
                }
                return (int)$var;

            case 'string':
                if (empty($var)) {
                    throw new \LogicException("{$name} is empty!", 400);
                }
                return (string)$var;

            case 'float':
                if (filter_var($var, FILTER_VALIDATE_FLOAT) === false) {
                    throw new \LogicException( "{$name} is not float!", 400);
                }
                return (float)$var;
            case 'email':
                if(filter_var($var, FILTER_VALIDATE_EMAIL) === false) {
                    throw new \LogicException( "{$name} is incorrect!", 400);
                }
                return (string)$var;
            case 'date':
                $date = date_parse_from_format('Y-m-d', $var);
                if(checkdate($date['month'], $date['day'], $date['year']) === false) {
                    throw new \LogicException("date format is not 'Y-m-d'!", 400);
                }
                return $var;
            default:
                return null;
        }
    }
}