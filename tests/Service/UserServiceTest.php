<?php

namespace Tests\Service;

use App\Model\User;
use App\Repository\UserRepository;
use App\Service\UserService;
use Tests\XmlTestCase;

class UserServiceTest extends XmlTestCase
{
    /**
     * @var UserService
     */
    static private $userService = null;

    public function testGetOneByLogin()
    {
        if (self::$userService === null) {
            self::$userService = new UserService(new UserRepository($this->dbal));
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
        $user = self::$userService->getOneByLogin($login);
        $this->assertEquals($user, $expectedUser);

        $login = 'test3';
        $expectedUser = null;
        $user = self::$userService->getOneByLogin($login);
        $this->assertEquals($user, $expectedUser);

        $login = 'test1';
        $expectedUser = null;
        $user = self::$userService->getOneByLogin($login, 1);
        $this->assertEquals($user, $expectedUser);
    }

    public function testGetOneByEmail()
    {
        if (self::$userService === null) {
            self::$userService = new UserService(new UserRepository($this->dbal));
        }
        $email = 'rzhevsky.dk@gmail.com';
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
        $user = self::$userService->getOneByEmail($email);
        $this->assertEquals($user, $expectedUser);

        $email = 'test@gmail.com';
        $expectedUser = null;
        $user = self::$userService->getOneByEmail($email);
        $this->assertEquals($user, $expectedUser);

        $email = 'rzhevsky.dk@gmail.com';
        $expectedUser = null;
        $user = self::$userService->getOneByEmail($email, 1);
        $this->assertEquals($user, $expectedUser);
    }

    public function testGetOneByNameAndOrg()
    {
        if (self::$userService === null) {
            self::$userService = new UserService(new UserRepository($this->dbal));
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
        $user = self::$userService->getOneByNameAndOrg($name, $surname, $organization);
        $this->assertEquals($user, $expectedUser);

        $name = 'Пенелопа';
        $surname = 'Дробыш';
        $organization = 'Xsolla';
        $expectedUser = null;
        $user = self::$userService->getOneByNameAndOrg($name, $surname, $organization);
        $this->assertEquals($user, $expectedUser);

        $name = 'Пенелопа';
        $surname = 'Дробыш';
        $organization = 'PSU';
        $expectedUser = null;
        $user = self::$userService->getOneByNameAndOrg($name, $surname, $organization, 2);
        $this->assertEquals($user, $expectedUser);
    }

    public function testAdd()
    {
        if (self::$userService === null) {
            self::$userService = new UserService(new UserRepository($this->dbal));
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
        $user = self::$userService->add($expectedUser);
        $this->assertEquals($user, $expectedUser);
    }

    public function testUpdate()
    {
        if (self::$userService === null) {
            self::$userService = new UserService(new UserRepository($this->dbal));
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
        $result = self::$userService->update($user, $values);
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
        $result = self::$userService->update($user, $values);
        $this->assertEquals($result, $expectedUser);
    }

    public function testDelete()
    {
        if (self::$userService === null) {
            self::$userService = new UserService(new UserRepository($this->dbal));
        }
        $value = self::$userService->delete(1);
        $this->assertEquals($value, null);
    }
}