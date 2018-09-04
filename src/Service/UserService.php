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
    public function add($user)
    {
        $this->userRepository->insert($user);
    }


    /**
     * @param string $name
     * @param string $surname
     * @param string $organization
     *
     * @return User|bool
     */
    public function findByNameAndOrganisation($name, $surname, $organization)
    {
        return $this->userRepository->findByNameAndOrganisation($name, $surname, $organization);
    }
}