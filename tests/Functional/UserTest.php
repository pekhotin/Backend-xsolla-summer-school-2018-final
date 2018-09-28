<?php

namespace Tests\Functional;

class UserTest extends ApiTestCase
{
    public function testGetMe()
    {
        $this->request('GET', '/api/v1/me');

        $this->assertThatResponseHasStatus(200);
        $this->assertThatResponseHasContentType('application/json');
        $this->assertCount(6, $this->responseData());

        $this->assertEquals(
            [
                'login' => 'test1',
                'name' => 'Дуремар',
                'surname' => 'Ржевский',
                'organization' => 'PSU',
                'email' => 'rzhevsky.dk@gmail.com',
                'phoneNumber' => '88005553535'
            ],
            $this->responseData()
        );
    }

    /**
     * @dataProvider dataRegister
     */
    public function testRegister($data, $expectedValues)
    {
        if (isset($expectedValues['exception'])) {
            $exception = $expectedValues['exception'];
            $this->expectException($exception['class']);
            $this->expectExceptionCode($exception['code']);
            $this->expectExceptionMessage($exception['message']);
        }

        $this->request(
            'POST',
            '/api/v1/users',
            $data
        );
        $this->assertThatResponseHasStatus(201);
        $this->assertThatResponseHasContentType('application/json');
        $this->assertCount(6, $this->responseData());
        $this->assertEquals($this->responseData(), $expectedValues);
    }

    public function dataRegister()
    {
        $result = [];

        $result[] = [
            [
                'login' => 'test1',
                'name' => 'Роза',
                'surname' => 'Шмелева',
                'organization' => 'PSU',
                'password' => 'qwerty123',
                'email' => 'shmeleva.roza@gmail.com',
                'phoneNumber' => '8(800)555 35 35'
            ],
            [
                'exception' => [
                    'class' => \LogicException::class,
                    'code' => 400,
                    'message' => 'user with login test1 is exist.'
                ]
            ]
        ];

        $result[] = [
            [
                'login' => 'test5',
                'name' => 'Роза',
                'surname' => 'Шмелева',
                'organization' => 'PSU',
                'password' => 'qwerty123',
                'email' => 'penelopa_2.0@gmail.com',
                'phoneNumber' => '8(800)555 35 35'
            ],
            [
                'exception' => [
                    'class' => \LogicException::class,
                    'code' => 400,
                    'message' => 'user with email penelopa_2.0@gmail.com is exist.'
                ]
            ]
        ];

        $result[] = [
            [
                'login' => 'test1',
                'name' => 'Дуремар',
                'surname' => 'Ржевский',
                'organization' => 'PSU',
                'password' => 'qwerty123',
                'email' => 'durya@gmail.com',
                'phoneNumber' => '8(800)555 35 35'
            ],
            [
                'exception' => [
                    'class' => \LogicException::class,
                    'code' => 400,
                    'message' => 'user Дуремар Ржевский is exist in organization PSU.'
                ]
            ]
        ];

        $result[] = [
            [
                'login' => 'test3',
                'name' => 'Анастасия',
                'surname' => 'Коваленко',
                'organization' => 'PSU',
                'password' => 'qwerty123',
                'email' => 'kovalenko.nastya@gmail.com',
                'phoneNumber' => '8(800)555 35 35'
            ],
            [
                'login' => 'test3',
                'name' => 'Анастасия',
                'surname' => 'Коваленко',
                'organization' => 'PSU',
                'email' => 'kovalenko.nastya@gmail.com',
                'phoneNumber' => '8(800)555 35 35'
            ]
        ];

        return $result;
    }
    /**
     * @dataProvider dataUpdateMe
     */
    public function testUpdateMe($data, $expectedValues)
    {
        if (isset($expectedValues['exception'])) {
            $exception = $expectedValues['exception'];
            $this->expectException($exception['class']);
            $this->expectExceptionCode($exception['code']);
            $this->expectExceptionMessage($exception['message']);
        }

        $this->request(
            'PUT',
            '/api/v1/me',
            $data
        );
        $this->assertThatResponseHasStatus(200);
        $this->assertThatResponseHasContentType('application/json');
        $this->assertCount(6, $this->responseData());
        $this->assertEquals($this->responseData(), $expectedValues);
    }

    public function dataUpdateMe()
    {
        $result = [];

        $result[] = [
            [
                'login' => 'test2'
            ],
            [
                'exception' => [
                    'class' => \LogicException::class,
                    'code' => 400,
                    'message' => 'user with login test2 is exist.'
                ]
            ]
        ];

        $result[] = [
            [
                'email' => 'penelopa_2.0@gmail.com',
                'phoneNumber' => '8(800)555 35 35'
            ],
            [
                'exception' => [
                    'class' => \LogicException::class,
                    'code' => 400,
                    'message' => 'user with email penelopa_2.0@gmail.com is exist.'
                ]
            ]
        ];

        $result[] = [
            [
                'name' => 'Пенелопа',
                'surname' => 'Дробыш'
            ],
            [
                'exception' => [
                    'class' => \LogicException::class,
                    'code' => 400,
                    'message' => 'user Пенелопа Дробыш is exist in organization PSU.'
                ]
            ]
        ];

        $result[] = [
            [
                'surname' => 'Коваленко',
                'email' => 'durya@gmail.com'
            ],
            [
                'login' => 'test1',
                'name' => 'Дуремар',
                'surname' => 'Коваленко',
                'organization' => 'PSU',
                'email' => 'durya@gmail.com',
                'phoneNumber' => '88005553535'
            ]
        ];

        return $result;
    }

    public function testDeleteMe()
    {
        $this->request('DELETE', '/api/v1/me');

        $this->assertThatResponseHasStatus(204);
        $this->assertThatResponseHasContentType('application/json');

        $this->assertEquals(
            null,
            $this->responseData()
        );
    }
}
