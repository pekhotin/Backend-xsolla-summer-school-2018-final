<?php

namespace App\Repository;

use App\Model\User;
use Doctrine\DBAL\Connection;
use phpDocumentor\Reflection\Types\This;

class UserRepository extends AbstractRepository
{
    /**
     * @param string $login
     * @param int $userId
     *
     * @return User|null
     */
    public function findByLogin($login, $userId = 0)
    {
        $row = $this->dbConnection->fetchAssoc(
            'SELECT * FROM Users WHERE login = ? AND id != ?',
            [
                $login,
                $userId
            ]
        );

        if ($row === false) {
            return null;
        }

        return new User(
            (int)$row['id'],
            (string)$row['login'],
            (string)$row['name'],
            (string)$row['surname'],
            (string)$row['hash'],
            (string)$row['organization'],
            (string)$row['email'],
            (string)$row['phoneNumber']
        );
    }

    /**
     * @param string $email
     * @param int $userId
     *
     * @return User|null
     */
    public function findByEmail($email, $userId = 0)
    {
        $row = $this->dbConnection->fetchAssoc(
            'SELECT * FROM Users WHERE email = ? AND id != ?',
            [$email,$userId]
        );

        if ($row === false) {
            return null;
        }

        return new User(
            (int)$row['id'],
            (string)$row['login'],
            (string)$row['name'],
            (string)$row['surname'],
            (string)$row['hash'],
            (string)$row['organization'],
            (string)$row['email'],
            (string)$row['phoneNumber']
        );
    }

    /**
     * @param string $name
     * @param string $surname
     * @param string $organization
     * @param int $userId
     * @return User|null
     */
    public function findByNameAndOrg($name, $surname, $organization, $userId = 0)
    {
        $row = $this->dbConnection->fetchAssoc(
            'SELECT * FROM Users WHERE name = ? AND surname = ? AND organization = ? AND id != ?',
            [
                $name,
                $surname,
                $organization,
                $userId
            ]
        );

        if ($row === false) {
            return null;
        }

        return new User(
            (int)$row['id'],
            (string)$row['login'],
            (string)$row['name'],
            (string)$row['surname'],
            (string)$row['hash'],
            (string)$row['organization'],
            (string)$row['email'],
            (string)$row['phoneNumber']
        );
    }

    /**
     * @param User $user
     *
     * @return User
     */
    public function insert($user)
    {
        $values = [
            'login' => $user->getLogin(),
            'name' => $user->getName(),
            'surname' => $user->getSurname(),
            'hash' => $user->getPasswordHash(),
            'organization' => $user->getOrganization(),
            'email' => $user->getEmail(),
            'phoneNumber' => $user->getPhoneNumber()
        ];

        $this->dbConnection->insert(
            'Users',
            $values
        );

        $user->setId($this->dbConnection->lastInsertId());
        return $user;
    }
    /**
     * @param User $user
     * @param array $values
     *
     * @return User
     */
    public function update($user, $values)
    {
        $this->dbConnection->update(
            'Users',
            $values,
            ['id' => $user->getId()]
        );

        return $this->findById($user->getId());
    }

    /**
     * @param User $user
     *
     * @throws \Doctrine\DBAL\Exception\InvalidArgumentException
     */
    public function delete($user)
    {
        $this->dbConnection->delete(
            'Users',
            ['id' => $user->getId()]
        );
    }
    /**
     * @param int $userId
     *
     * @return User|null
     */
    public function findById($userId)
    {
        $row = $this->dbConnection->fetchAssoc(
            'SELECT * FROM Users WHERE id = ?',
            [$userId]
        );

        if ($row === false) {
            return null;
        }

        return new User(
            (int)$row['id'],
            (string)$row['login'],
            (string)$row['name'],
            (string)$row['surname'],
            (string)$row['hash'],
            (string)$row['organization'],
            (string)$row['email'],
            (string)$row['phoneNumber']
        );
    }
}