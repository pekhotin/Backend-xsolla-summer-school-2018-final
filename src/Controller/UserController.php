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
        $this->jsonSchemaValidator->checkBySchema($bodyParams, __DIR__ . '/../../resources/jsonSchema/user.json');

        $login = $this->validateVar(trim($bodyParams['login']), 'string', 'login');
        $name = $this->validateVar(trim($bodyParams['name']), 'string', 'name');
        $surname = $this->validateVar(trim($bodyParams['surname']), 'string', 'surname');
        $organization = $this->validateVar(trim($bodyParams['organization']), 'string', 'organization');
        $email = $this->validateVar(trim($bodyParams['email']), 'email', 'email');
        $password = $this->validateVar(trim($bodyParams['password']), 'string', 'password');
        $phoneNumber = $this->validateVar(trim($bodyParams['phoneNumber']), 'string', 'phoneNumber');

        if ($this->userService->getOneByNameAndOrg($name, $surname, $organization) !== null) {
            throw new \LogicException(
                "user {$name} {$surname} is exist in organization {$organization}!",
                400
            );
        }
        if ($this->userService->getOneByLogin($login) !== null) {
            throw new \LogicException(
                "user with login {$login} is exist!",
                400);
        }
        if ($this->userService->getOneByEmail($email) !== null) {
            throw new \LogicException(
                "user with email {$email} is exist!",
                400);
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
    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function getMe(Request $request, Response $response)
    {
        $this->initUser($request);
        return $response->withJson($this->user->getUserArray(), 201);
    }
    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function updateMe(Request $request, Response $response)
    {
        $this->initUser($request);
        $bodyParams = $request->getParsedBody();
        $values = [];

        $values['name'] = isset($bodyParams['name'])
            ? $this->validateVar($bodyParams['name'], 'string', 'name')
            : $this->user->getName();
        $values['surname'] = isset($bodyParams['surname'])
            ? $this->validateVar($bodyParams['surname'], 'string', 'surname')
            : $this->user->getSurname();
        $values['login'] = isset($bodyParams['login'])
            ? $this->validateVar($bodyParams['login'], 'string', 'login')
            : $this->user->getLogin();
        $values['organization'] = isset($bodyParams['organization'])
            ? $this->validateVar($bodyParams['organization'], 'string', 'organization')
            : $this->user->getOrganization();
        $values['email'] = isset($bodyParams['email'])
            ? $this->validateVar($bodyParams['email'], 'email', 'email')
            : $this->user->getEmail();
        $values['hash'] = isset($bodyParams['password'])
            ? password_hash($this->validateVar($bodyParams['password'], 'string', 'password'), PASSWORD_DEFAULT)
            : $this->user->getPasswordHash();
        $values['phoneNumber'] = isset($bodyParams['phoneNumber'])
            ? $this->validateVar($bodyParams['phoneNumber'], 'string', 'phoneNumber')
            : $this->user->getPhoneNumber();

        if ($this->userService->getOneByNameAndOrg(
            $values['name'],
            $values['surname'],
            $values['organization'],
            $this->user->getId()) !== null
        ) {
            throw new \LogicException(
                "user {$values['name']} {$values['surname']} is exist in organization {$values['organization']}!",
                400
            );
        }
        if ($this->userService->getOneByLogin($values['login'], $this->user->getId()) !== null ) {
            throw new \LogicException(
                "user with login {$values['login']} is exist!",
                400
            );
        }
        if ($this->userService->getOneByEmail($values['email'], $this->user->getId()) !== null) {
            throw new \LogicException(
                "user with email {$values['email']} is exist!",
                400);
        }

        // добавить проверку для номера телефона

        $this->user = $this->userService->update($this->user, $values);
        return $response->withJson($this->user->getUserArray(), 200);
    }
    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function deleteMe(Request $request, Response $response)
    {
        $this->initUser($request);

        $this->userService->delete($this->user);

        return $response
            ->withStatus(204)
            ->withHeader('Content-Type', 'application/json');
    }
}