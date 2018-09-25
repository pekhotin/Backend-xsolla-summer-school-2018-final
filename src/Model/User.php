<?php

namespace App\Model;

class User
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var string
     */
    private $login;
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $surname;
    /**
     * @var string
     */
    private $passwordHash;
    /**
     * @var string
     */
    private $organization;
    /**
     * @var string
     */
    private $email;
    /**
     * @var string
     */
    private $phoneNumber;
    /**
     * User constructor.
     *
     * @param int $id
     * @param string $login
     * @param string $name
     * @param string $surname
     * @param string $passwordHash
     * @param string $organization
     * @param string $email
     * @param string $phoneNumber
     */
    public function __construct($id, $login, $name, $surname, $passwordHash, $organization, $email, $phoneNumber)
    {
        $this->id = $id;
        $this->login = $login;
        $this->name = $name;
        $this->surname = $surname;
        $this->passwordHash = $passwordHash;
        $this->organization = $organization;
        $this->email = $email;
        $this->phoneNumber = $phoneNumber;
    }
    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }
    /**
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }
    /**
     * @param string $login
     */
    public function setLogin($login)
    {
        $this->login = $login;
    }
    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }
    /**
     * @return string
     */
    public function getSurname()
    {
        return $this->surname;
    }
    /**
     * @param string $surname
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;
    }
    /**
     * @return string
     */
    public function getPasswordHash()
    {
        return $this->passwordHash;
    }
    /**
     * @param string $passwordHash
     */
    public function setPasswordHash($passwordHash)
    {
        $this->passwordHash = $passwordHash;
    }
    /**
     * @return string
     */
    public function getOrganization()
    {
        return $this->organization;
    }
    /**
     * @param string $organization
     */
    public function setOrganization($organization)
    {
        $this->organization = $organization;
    }
    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }
    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }
    /**
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }
    /**
     * @param string $phoneNumber
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;
    }
    /**
     * @return array
     */
    public function getUserArray()
    {
        return [
            'login' => $this->login,
            'name' => $this->name,
            'surname' => $this->surname,
            'organization' => $this->organization,
            'email' => $this->email,
            'phoneNumber' => $this->phoneNumber
        ];
    }
}
