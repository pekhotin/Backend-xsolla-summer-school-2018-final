<?php

namespace Tests\Repository;

use App\Model\User;
use App\Repository\UserRepository;
use PHPUnit\Framework\TestCase;

class UserServiceTest extends TestCase
{
    /**
     * @param User $user
     * @dataProvider dataAdd
     */
    public function testAdd($user)
    {
        //$userService = new UserService()
    }

    public function dataAdd()
    {
        return [

        ];
    }
}