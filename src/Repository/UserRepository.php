<?php

namespace App\Repository;

use App\Model\User;
use Doctrine\DBAL\Connection;

class UserRepository extends AbstractRepository
{
    /**
     * @var string
     */
    private $tableName;

    /**
     * UserRepository constructor.
     *
     * @param Connection $dbConnection
     */
    public function __construct(Connection $dbConnection)
    {
        parent::__construct($dbConnection);
        $this->tableName = 'Users';
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
        $row = $this->dbConnection->fetchAssoc(
            'SELECT * FROM ' . $this->tableName . ' WHERE name = ? AND surname = ? AND organization = ?',
            [$name, $surname, $organization]
        );

        if ($row === null) {
            return false;
        }

        return new User(
            $row['id'],
            $row['name'],
            $row['surname'],
            $row['organization'],
            $row['email'],
            $row['password'],
            $row['phoneNumber']
        );
    }

    /**
     * @param User $user
     */
    public function insert(User $user)
    {
        $values = [
            'name' => $user->getName(),
            'surname' => $user->getSurname(),
            'organization' => $user->getOrganization(),
            'email' => $user->getEmail(),
            'password' => $user->getPassword(),
            'phoneNumber' => $user->getPhoneNumber()
        ];

        $this->dbConnection->insert(
            $this->tableName,
            $values
        );

        $user->setId($this->dbConnection->lastInsertId());
    }


}