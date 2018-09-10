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
        $bodyParams = $request->getParsedBody();
        $login = $this->validateVar(trim($bodyParams['login']), 'string', 'login');
        $name = $this->validateVar(trim($bodyParams['name']), 'string', 'name');
        $surname = $this->validateVar(trim($bodyParams['surname']), 'string', 'surname');
        $organization = $this->validateVar(trim($bodyParams['organization']), 'string', 'organization');
        $email = $this->validateVar(trim($bodyParams['email']), 'email', 'email');
        $password = $this->validateVar(trim($bodyParams['password']), 'string', 'password');
        $phoneNumber = $this->validateVar(trim($bodyParams['phoneNumber']), 'string', 'phoneNumber');;

        if ($this->userService->getOneByNameAndOrg($name, $surname, $organization) !== null) {
            throw new \LogicException("user {$name} {$surname} is exist in organization {$organization}!", 400);
        }
        if ($this->userService->getOneByLogin($login) !== null) {
            throw new \LogicException("user with login {$login} is exist!", 400);
        }
        if ($this->userService->getOneByEmail($email) !== null) {
            throw new \LogicException("user with email {$email} is exist!", 400);
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

        return $response->withJson($user->getUserArray(), 201);
    }
}