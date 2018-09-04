<?php

namespace App\Controller;

use App\Service\UserService;
use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Model\User;

class UserController extends BaseController
{
    /**
     * @var UserService
     */
    private $userService;

    /**
     * UserController constructor.
     *
     * @param App $app
     * @param UserService $userService
     */
    public function __construct(App $app, UserService $userService)
    {
        parent::__construct($app);
        $this->userService = $userService;
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function register(Request $request, Response $response)
    {
        try {

            $bodyParams = $request->getParsedBody();

            if (!isset($bodyParams['name']) || empty(trim($bodyParams['name']))) {
                throw new \LogicException(__CLASS__ . ' register() name is undefined!');
            }

            $name = (string)trim($bodyParams['name']);

            if (!isset($bodyParams['surname']) || empty(trim($bodyParams['surname']))) {
                throw new \LogicException(__CLASS__ . ' register() surname is undefined!');
            }

            $surname = (string)trim($bodyParams['surname']);

            if (!isset($bodyParams['organization']) || empty(trim($bodyParams['organization']))) {
                throw new \LogicException(__CLASS__ . ' register() organization is undefined!');
            }

            $organization = (string)trim($bodyParams['organization']);

            if (!isset($bodyParams['email']) || !filter_var(trim($bodyParams['email']), FILTER_VALIDATE_EMAIL)) {
                throw new \LogicException(__CLASS__ . ' register() email is undefined!');
            }

            if (!isset($bodyParams['password']) || empty(trim($bodyParams['password']))) {
                throw new \LogicException(__CLASS__ . ' register() password is undefined!');
            }

            if (!isset($bodyParams['phoneNumber']) || empty(trim($bodyParams['phoneNumber']))) {
                throw new \LogicException(__CLASS__ . ' register() phoneNumber is undefined!');
            }

            if ($this->userService->findByNameAndOrganisation($name, $surname, $organization)) {
                throw new \LogicException(__CLASS__ . " register() user {$name} {$surname} is exist in organization {$organization}!");
            }

            //проверка на email

            $user = new User(
                null,
                $name,
                $surname,
                $organization,
                (string)trim($bodyParams['email']),
                (string)trim($bodyParams['password']),
                (string)trim($bodyParams['phoneNumber'])
            );

            $this->userService->add($user);

            return $response
                ->withStatus(201)
                ->withHeader('Content-Type', 'application/json')
                ->withJson($user->getUserArray(), 201);

        } catch(\LogicException $exception) {

            error_log($exception->getMessage());

            return $response
                ->withStatus(400)
                ->withHeader('Content-Type', 'application/json');
        }
    }
}