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
    private $name;

    /**
     * @var string
     */
    private $surname;

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
    private $password;

    /**
     * @var string
     */
    private $phoneNumber;

    /**
     * User constructor.
     *
     * @param int $id
     * @param string $name
     * @param string $surname
     * @param string $organization
     * @param string $email
     * @param string $password
     * @param string $phoneNumber
     */
    public function __construct($id, $name, $surname, $organization, $email, $password, $phoneNumber)
    {
        $this->id = $id;
        $this->name = $name;
        $this->surname = $surname;
        $this->organization = $organization;
        $this->email = $email;
        $this->password = $password;
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
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
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
            'id' => $this->id,
            'name' => $this->name,
            'surname' => $this->surname,
            'organization' => $this->organization,
            'email' => $this->email,
            'password' => $this->password,
            'phoneNumber' => $this->phoneNumber
        ];
    }
}