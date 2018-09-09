<?php

namespace App\Service;

use App\Model\User;
use App\Repository\UserRepository;

class UserService
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * UserService constructor.
     *
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository) {
        $this->userRepository = $userRepository;
    }

    /**
     * @param User $user
     */
    public function add(User $user)
    {
        $this->userRepository->insert($user);
    }

    /**
     * @param string $login
     *
     * @return User|null
     */
    public function getOneByLogin(string $login)
    {
        return $this->userRepository->findByLogin($login);
    }

    /**
     * @param string $email
     *
     * @return User|null
     */
    public function getOneByEmail(string $email)
    {
        return $this->userRepository->findByEmail($email);
    }


    /**
     * @param $name
     * @param $surname
     * @param $organization
     *
     * @return User|null
     */
    public function getOneByNameAndOrg(string $name, string $surname, string $organization)
    {
        return $this->userRepository->findByNameAndOrg($name, $surname, $organization);
    }
}