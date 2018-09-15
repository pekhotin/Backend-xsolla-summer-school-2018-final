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
     * UserServiceTest constructor.
     *
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param User $user
     *
     * @return User
     */
    public function add(User $user)
    {
        return $this->userRepository->insert($user);
    }
    /**
     * @param string $login
     * @param int $userId
     * @return User|null
     */
    public function getOneByLogin(string $login, int $userId = 0)
    {
        return $this->userRepository->findByLogin($login, $userId);
    }
    /**
     * @param string $email
     * @param int $userId
     *
     * @return User|null
     */
    public function getOneByEmail(string $email, int $userId = 0)
    {
        return $this->userRepository->findByEmail($email, $userId);
    }
    /**
     * @param string $name
     * @param string $surname
     * @param string $organization
     * @param int $userId
     *
     * @return User|null
     */
    public function getOneByNameAndOrg(string $name, string $surname, string $organization, int $userId = 0)
    {
        return $this->userRepository->findByNameAndOrg($name, $surname, $organization, $userId);
    }
    /**
     * @param User $user
     * @param array $values
     *
     * @return User
     */
    public function update(User $user, $values)
    {
        return $this->userRepository->update($user, $values);
    }
    /**
     * @param User $user
     *
     * @throws \Doctrine\DBAL\Exception\InvalidArgumentException
     */
    public function delete(User $user)
    {
        $this->userRepository->delete($user);
    }
}
