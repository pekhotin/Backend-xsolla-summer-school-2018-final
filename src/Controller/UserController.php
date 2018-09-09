<?php

namespace App\Controller;

use Slim\Http\Request;
use Slim\Http\Response;
use App\Model\User;

class UserController extends BaseController
{

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
            $login = $this->validateVar(trim($bodyParams['login']), 'string');
            $name = $this->validateVar(trim($bodyParams['name']), 'string');
            $surname = $this->validateVar(trim($bodyParams['surname']), 'string');
            $organization = $this->validateVar(trim($bodyParams['organization']), 'string');
            $email = $this->validateVar(trim($bodyParams['email']), 'email');
            $password = $this->validateVar(trim($bodyParams['password']), 'string');
            $phoneNumber = $this->validateVar(trim($bodyParams['phoneNumber']), 'string');;

            if ($this->userService->getOneByNameAndOrg($name, $surname, $organization) !== null) {
                throw new \LogicException(__CLASS__ . " register() user {$name} {$surname} is exist in organization {$organization}!");
            }
            if ($this->userService->getOneByLogin($login) !== null) {
                throw new \LogicException(__CLASS__ . " register() user with login {$login} is exist!");
            }
            if ($this->userService->getOneByEmail($email) !== null) {
                throw new \LogicException(__CLASS__ . " register() user with email {$email} is exist!");
            }

            $user = new User(
                null,
                $login,
                $name,
                $surname,
                password_hash($password, PASSWORD_DEFAULT),
                $organization,
                $email,
                $phoneNumber
            );

            $this->userService->add($user);

            return $response
                ->withStatus(201)
                ->withHeader('Content-Type', 'application/json')
                ->withJson($user->getUserArray());

        } catch(\LogicException $exception) {

            error_log($exception->getMessage());

            return $response
                ->withStatus(400)
                ->withHeader('Content-Type', 'application/json');
        }
    }
}