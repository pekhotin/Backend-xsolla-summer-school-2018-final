<?php

namespace Tests\Validator;

use App\Validator\UserValidator;
use PHPUnit\Framework\TestCase;

class UserValidatorTest extends TestCase
{
    /**
     * @dataProvider dataValidateInsertData
     */
    public function testValidateInsertData($data, $expectedValues)
    {
        $validator = new UserValidator();
        $values = $validator->validateInsertData($data);
        $this->assertEquals($values, $expectedValues);
    }

    public function dataValidateInsertData()
    {
        $values = [
            "login" => "  dftregh",
	        "name" => "Petr",
	        "surname"=> "Ivanov",
	        "password"=> "qwerty12",
	        "organization"=> "PSU",
	        "email"=> "petra@gmail.com",
	        "phoneNumber"=> "88005553535"
        ];

        $expectedValues = [
            "login" => "dftregh",
            "name" => "Petr",
            "surname"=> "Ivanov",
            "password"=> "qwerty12",
            "organization"=> "PSU",
            "email"=> "petra@gmail.com",
            "phoneNumber"=> "88005553535"
        ];
        $result[] = [$values, $expectedValues];
        return $result;
    }
}