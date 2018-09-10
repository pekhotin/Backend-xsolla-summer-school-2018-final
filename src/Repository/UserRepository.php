<?php

namespace App\Repository;

use App\Model\User;
use Doctrine\DBAL\Connection;

class UserRepository extends AbstractRepository
{
    /**
     * @param string $login
     *
     * @return User|null
     */
    public function findByLogin($login)
    {
        $row = $this->dbConnection->fetchAssoc(
            'SELECT * FROM Users WHERE login = ?',
            [$login]
        );

        if ($row === false) {
            return null;
        }

        return new User(
            $row['id'],
            $row['login'],
            $row['name'],
            $row['surname'],
            $row['hash'],
            $row['organization'],
            $row['email'],
            $row['phoneNumber']
        );
    }
    /**
     * @param string $email
     *
     * @return User|null
     */
    public function findByEmail($email)
    {
        $row = $this->dbConnection->fetchAssoc(
            'SELECT * FROM Users WHERE email = ?',
            [$email]
        );

        if ($row === false) {
            return null;
        }

        return new User(
            $row['id'],
            $row['login'],
            $row['name'],
            $row['surname'],
            $row['hash'],
            $row['organization'],
            $row['email'],
            $row['phoneNumber']
        );
    }
    /**
     * @param string $name
     * @param string $surname
     * @param string $organization
     *
     * @return User|null
     */
    public function findByNameAndOrg($name, $surname, $organization)
    {
        $row = $this->dbConnection->fetchAssoc(
            'SELECT * FROM Users WHERE name = ? AND surname = ? AND organization = ?',
            [$name, $surname, $organization]
        );

        if ($row === false) {
            return null;
        }

        return new User(
            $row['id'],
            $row['login'],
            $row['name'],
            $row['surname'],
            $row['hash'],
            $row['organization'],
            $row['email'],
            $row['phoneNumber']
        );
    }
    /**
     * @param User $user
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
    }


}