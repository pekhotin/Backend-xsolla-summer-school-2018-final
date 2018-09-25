<?php

namespace Tests\Repository;


use App\Model\User;
use App\Repository\UserRepository;
use Tests\XmlTestCase;

class UserRepositoryTest extends XmlTestCase
{
    /**
     * @var UserRepository
     */
    static private $userRepository = null;

    public function testFindByLogin()
    {
        if (self::$userRepository === null) {
            self::$userRepository = new UserRepository($this->dbal);
        }
        $login = 'test1';
        $expectedUser = new User(
            1,
            'test1',
            'Дуремар',
            'Ржевский',
            '$2y$10$pllP8SaAQ6ZqNZY7.1bXve0E14AaCS0fdInsgQbhdO8IypghGWiYu',
            'PSU',
            'rzhevsky.dk@gmail.com',
            '88005553535'
        );
        $user = self::$userRepository->findByLogin($login);
        $this->assertEquals($user, $expectedUser);

        $login = 'test3';
        $expectedUser = null;
        $user = self::$userRepository->findByLogin($login);
        $this->assertEquals($user, $expectedUser);

        $login = 'test1';
        $expectedUser = null;
        $user = self::$userRepository->findByLogin($login, 1);
        $this->assertEquals($user, $expectedUser);
    }

    public function testFindById()
    {
        if (self::$userRepository === null) {
            self::$userRepository = new UserRepository($this->dbal);
        }
        $id = 1;
        $expectedUser = new User(
            1,
            'test1',
            'Дуремар',
            'Ржевский',
            '$2y$10$pllP8SaAQ6ZqNZY7.1bXve0E14AaCS0fdInsgQbhdO8IypghGWiYu',
            'PSU',
            'rzhevsky.dk@gmail.com',
            '88005553535'
        );

        $user = self::$userRepository->findById($id);
        $this->assertEquals($user, $expectedUser);

        $id = 12;
        $expectedUser = null;

        $user = self::$userRepository->findById($id);
        $this->assertEquals($user, $expectedUser);
    }

    public function testFindByEmail()
    {
        if (self::$userRepository === null) {
            self::$userRepository = new UserRepository($this->dbal);
        }
        $email = 'penelopa_2.0@gmail.com';
        $expectedUser = new User(
            2,
            'test2',
            'Пенелопа',
            'Дробыш',
            '$2y$10$l6UdWlQxMlE6Ln9AAGfEiefpLM6NSQTU9xOglf4yiLEKAwvxLTIfu',
            'PSU',
            'penelopa_2.0@gmail.com',
            '8(999)999-99-99'
        );

        $user = self::$userRepository->findByEmail($email);
        $this->assertEquals($user, $expectedUser);

        $email = 'petushok@gmail.com';
        $expectedUser = null;
        $user = self::$userRepository->findByLogin($email);
        $this->assertEquals($user, $expectedUser);

        $email = 'penelopa_2.0@gmail.com';
        $expectedUser = null;
        $user = self::$userRepository->findByLogin($email, 2);
        $this->assertEquals($user, $expectedUser);
    }

    public function testFindByNameAndOrg()
    {
        if (self::$userRepository === null) {
            self::$userRepository = new UserRepository($this->dbal);
        }

        $name = 'Пенелопа';
        $surname = 'Дробыш';
        $organization = 'PSU';
        $expectedUser = new User(
            2,
            'test2',
            'Пенелопа',
            'Дробыш',
            '$2y$10$l6UdWlQxMlE6Ln9AAGfEiefpLM6NSQTU9xOglf4yiLEKAwvxLTIfu',
            'PSU',
            'penelopa_2.0@gmail.com',
            '8(999)999-99-99'
        );
        $user = self::$userRepository->findByNameAndOrg($name, $surname, $organization);
        $this->assertEquals($user, $expectedUser);

        $name = 'Пенелопа';
        $surname = 'Дробыш';
        $organization = 'Xsolla';
        $expectedUser = null;
        $user = self::$userRepository->findByNameAndOrg($name, $surname, $organization);
        $this->assertEquals($user, $expectedUser);

        $name = 'Пенелопа';
        $surname = 'Дробыш';
        $organization = 'PSU';
        $expectedUser = null;
        $user = self::$userRepository->findByNameAndOrg($name, $surname, $organization, 2);
        $this->assertEquals($user, $expectedUser);
    }

    public function testInsert()
    {
        if (self::$userRepository === null) {
            self::$userRepository = new UserRepository($this->dbal);
        }

        $expectedUser = new User(
            3,
            'test3',
            'Светлана',
            'Поросятникова',
            '$2y$10$l6UdWlQxMlE6Ln9AAGfEiefpLM6NSQTU9xOglf4yiLEKAwvxLTIfu',
            'PSU',
            'porosyatnikova.sb@gmail.com',
            '8(800)555-35-35'
        );
        $user = self::$userRepository->insert($expectedUser);
        $this->assertEquals($user, $expectedUser);
    }

    public function testUpdate()
    {
        if (self::$userRepository === null) {
            self::$userRepository = new UserRepository($this->dbal);
        }

        $user = new User(
            1,
            'test1',
            'Дуремар',
            'Ржевский',
            '$2y$10$pllP8SaAQ6ZqNZY7.1bXve0E14AaCS0fdInsgQbhdO8IypghGWiYu',
            'PSU',
            'rzhevsky.dk@gmail.com',
            '88005553535'
        );
        $values = [
            'login' => 'Durem@r',
            'organization' => 'PGNIU',
            'hash' => 'gffbbsidshafhdsjfkhdsjkfhdskhfgdhfgdhfgd'
        ];
        $expectedUser = new User(
            1,
            'Durem@r',
            'Дуремар',
            'Ржевский',
            'gffbbsidshafhdsjfkhdsjkfhdskhfgdhfgdhfgd',
            'PGNIU',
            'rzhevsky.dk@gmail.com',
            '88005553535'
        );
        $result = self::$userRepository->update($user, $values);
        $this->assertEquals($result, $expectedUser);

        $user = new User(
            125,
            'test125',
            'Дуремар',
            'Русаков',
            '$2y$10$pllP8SaAQ6ZqNZY7.1bXve0E14AaCS0fdInsgQbhdO8IypghGWiYu',
            'PSU',
            'rusakov.dk@gmail.com',
            '88005553535'
        );
        $values = [
            'login' => 'Durem@r',
            'organization' => 'PGNIU',
            'hash' => 'gffbbsidshafhdsjfkhdsjkfhdskhfgdhfgdhfgd'
        ];
        $expectedUser = null;
        $result = self::$userRepository->update($user, $values);
        $this->assertEquals($result, $expectedUser);
    }

    public function testDelete()
    {
        if (self::$userRepository === null) {
            self::$userRepository = new UserRepository($this->dbal);
        }
        $value = self::$userRepository->delete(1);
        $expectedValue = self::$userRepository->findById(1);
        $this->assertEquals($value, $expectedValue);
    }
}