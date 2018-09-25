<?php

namespace App\Controller;

use App\Service\UserService;
use App\Validator\UserValidator;
use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Model\User;

class UserController extends BaseController
{
    public function __construct(App $app, UserService $userService)
    {
        parent::__construct($app, $userService);
        $this->validator = new UserValidator();
    }
    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function register(Request $request, Response $response)
    {
        $bodyParams = $request->getParsedBody();
        $values = $this->validator->validateInsertData($bodyParams);

        if ($this->userService->getOneByNameAndOrg($values['name'], $values['surname'], $values['organization']) !== null) {
            throw new \LogicException(
                "user {$values['name']} {$values['surname']} is exist in organization {$values['organization']}!",
                400
            );
        }
        if ($this->userService->getOneByLogin($values['login']) !== null) {
            throw new \LogicException(
                "user with login {$values['login']} is exist!",
                400);
        }
        if ($this->userService->getOneByEmail($values['email']) !== null) {
            throw new \LogicException(
                "user with email {$values['email']} is exist!",
                400);
        }

        $user = new User(
            null,
            $values['login'],
            $values['name'],
            $values['surname'],
            password_hash( $values['password'], PASSWORD_DEFAULT),
            $values['organization'],
            $values['email'],
            $values['phoneNumber']
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
        $values = $this->validator->validateUpdateData($bodyParams, $this->user);

        if ($this->userService->getOneByNameAndOrg($values['name'], $values['surname'], $values['organization'], $this->user->getId()) !== null) {
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

        $this->user = $this->userService->update($this->user, $values);

        return $response->withJson($this->user->getUserArray(), 200);
    }
    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     *
     * @throws \Doctrine\DBAL\Exception\InvalidArgumentException
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
