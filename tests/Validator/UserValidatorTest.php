<?php

namespace Tests\Validator;

use App\Validator\UserValidator;
use JsonSchema\Exception\ValidationException;
use PHPUnit\Framework\Error\Error;
use PHPUnit\Framework\TestCase;

class UserValidatorTest extends TestCase
{
    protected $fixture;

    protected function setUp()
    {
        $this->fixture = new UserValidator();
    }

    protected function tearDown()
    {
        $this->fixture = NULL;
    }
    /**
     * @dataProvider dataValidateInsertData
     */
    public function testValidateInsertData($data, $expectedValues)
    {
        $values = $this->fixture->validateInsertData($data);
        $this->assertEquals($values, $expectedValues);
    }

    public function dataValidateInsertData()
    {
        $values[0] = [
            "login" => "  test1",
	        "name" => "     Petr",
	        "surname"=> "           Ivanov",
	        "password"=> "   qwerty12",
	        "organization"=> "PSU",
	        "email"=> "petra@gmail.com",
	        "phoneNumber"=> "88005553535"
        ];

        $expectedValues[0] = [
            "login" => "test1",
            "name" => "Petr",
            "surname"=> "Ivanov",
            "password"=> "qwerty12",
            "organization"=> "PSU",
            "email"=> "petra@gmail.com",
            "phoneNumber"=> "88005553535"
        ];
        $values[1] = [
            "login" => "2test    ",
            "name" => "Петя ",
            "surname"=> "Иванов",
            "password"=> "qwerty12",
            "organization"=> "PSU",
            "email"=> "petra@gmail.com",
            "phoneNumber"=> "88005553535"
        ];

        $expectedValues[1] = [
            "login" => "2test",
            "name" => "Петя",
            "surname"=> "Иванов",
            "password"=> "qwerty12",
            "organization"=> "PSU",
            "email"=> "petra@gmail.com",
            "phoneNumber"=> "88005553535"
        ];
        return [
            [$values[0], $expectedValues[0]],
            [$values[1], $expectedValues[1]]
        ];
    }

    /**
     * @expectedException JsonSchema\Exception\ValidationException
     */
    public function testValidateInsertData2()
    {
        $data = [
            "login" => "",
            "name" => "Petr",
            "surname"=> "Ivanov",
            "password"=> "qwerty12",
            "organization"=> "PSU",
            "email"=> "petra@gmail.com",
            "phoneNumber"=> "88005553535"
        ];
        $this->fixture->validateInsertData($data);
    }
}