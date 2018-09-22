<?php

namespace Tests\Validator;

use App\Model\User;
use App\Validator\UserValidator;
use http\Exception\InvalidArgumentException;
use JsonSchema\Exception\ValidationException;
use Tests\XmlTestCase;

class UserValidatorTest extends XmlTestCase
{


    protected function setUp()
    {
        parent::setUp();
        $this->fixture = new UserValidator();
    }
    /**
     * @dataProvider dataValidateInsertData
     */
    public function testValidateInsertData($data, $expectedValues)
    {
        if (isset($expectedValues['exception'])) {
            $exception = $expectedValues['exception'];
            $this->expectException($exception['class']);
            $this->expectExceptionCode($exception['code']);
            $this->expectExceptionMessage($exception['message']);
        }
        $values = $this->fixture->validateInsertData($data);

        $this->assertEquals($values, $expectedValues);

    }

    public function dataValidateInsertData()
    {
        $result = [];
        $template = [
            'login' => 'Petr',
            'name' => 'Петр',
            'surname' => 'Иванов',
            'password' => 'qwerty123',
            'organization' => 'PSU',
            'email' => 'petra@gmail.com',
            'phoneNumber' => '88005553535'
        ];

        $result[] = [
            [
                'login' => 'Petr    ',
                'name' => 'Петр      ',
                'surname' => '       Иванов',
                'password' => 'qwerty123',
                'organization' => 'PSU  ',
                'email' => 'petra@gmail.com  ',
                'phoneNumber' => '  88005553535'
            ],
            $template
        ];
        $values = $template;
        $values['login'] = 'bfdhfbdsjdnasjkdsjkdksjdnjbfjkdbfjkdbjdddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddd';
        //long login
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property login is incorrect. Must be at most 80 characters long.'
                ]
            ]
        ];
        //empty login
        $values = $template;
        $values['login'] = '            ';
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => \InvalidArgumentException::class,
                    'code' => '400',
                    'message' => 'login is empty.'
                ]
            ]
        ];
        //numerical login
        $values = $template;
        $values['login'] = 125;
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property login is incorrect. Integer value found, but a string is required.'
                ]
            ]
        ];

        //long name
        $values = $template;
        $values['name'] = 'bfdhfbdsjdnasjkdsjkdksjdnjbfjkdbfjkdbjdddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddd';
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property name is incorrect. Must be at most 80 characters long.'
                ]
            ]
        ];
        //empty name
        $values = $template;
        $values['name'] = '          ';
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => \InvalidArgumentException::class,
                    'code' => '400',
                    'message' => 'name is empty.'
                ]
            ]
        ];
        //numerical name
        $values = $template;
        $values['name'] = 12;
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property name is incorrect. Integer value found, but a string is required.'
                ]
            ]
        ];
        //long surname
        $values = $template;
        $values['surname'] = 'bfdhfbdsjdnasjkdsjkdksjdnjbfjkdbfjkdbjdddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddd';
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property surname is incorrect. Must be at most 80 characters long.'
                ]
            ]
        ];
        //empty surname
        $values = $template;
        $values['surname'] = '      ';
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => \InvalidArgumentException::class,
                    'code' => '400',
                    'message' => 'surname is empty.'
                ]
            ]
        ];
        //numerical surname
        $values = $template;
        $values['surname'] = 123;
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property surname is incorrect. Integer value found, but a string is required.'
                ]
            ]
        ];

        //long password
        $values = $template;
        $values['password'] = 'bfdhfbdsjdnasjkdsjkdksjdnjbfjkdbfjkdbjdddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddd';
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property password is incorrect. Must be at most 40 characters long.'
                ]
            ]
        ];
        //short password
        $values = $template;
        $values['password'] = 'qwerty';
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property password is incorrect. Must be at least 8 characters long. '
                ]
            ]
        ];
        //empty password
        $values = $template;
        $values['password'] = '         ';
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => \InvalidArgumentException::class,
                    'code' => '400',
                    'message' => 'password is empty.'
                ]
            ]
        ];
        //numerical password
        $values = $template;
        $values['password'] = 120;
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property password is incorrect. Integer value found, but a string is required.'
                ]
            ]
        ];

        //long organization
        $values = $template;
        $values['organization'] = 'bfdhfbdsjdnasjkdsjkdbfdhfbdsjdnasjkdsjkdksjdnjbfjkdbfjkdbjdddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddd';
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property organization is incorrect. Must be at most 100 characters long.'
                ]
            ]
        ];
        //empty organization
        $values = $template;
        $values['organization'] = '      ';
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => \InvalidArgumentException::class,
                    'code' => '400',
                    'message' => 'organization is empty.'
                ]
            ]
        ];
        //numerical organization
        $values = $template;
        $values['organization'] = 15;
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property organization is incorrect. Integer value found, but a string is required. '
                ]
            ]
        ];

        //long email
        $values = $template;
        $values['email'] = 'bfdhfbdsjdnasjkdsjkdbfdhfbdsjdnasjkdsjkdksjdnjbfjkdbfjkdbjdddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddd@gmail.com';
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property email is incorrect. Must be at most 80 characters long. '
                ]
            ]
        ];
        //empty email
        $values = $template;
        $values['email'] = '      ';
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => \InvalidArgumentException::class,
                    'code' => '400',
                    'message' => 'email is empty.'
                ]
            ]
        ];
        //numerical email
        $values = $template;
        $values['email'] = 15;
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property email is incorrect. Integer value found, but a string is required. '
                ]
            ]
        ];
        //incorrect email
        $values = $template;
        $values['email'] = 'petr.igmail.com';
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => \InvalidArgumentException::class,
                    'code' => '400',
                    'message' => 'email is incorrect.'
                ]
            ]
        ];
        //correct numbers
        $template['phoneNumber'] = '+79261234567';
        $result[] = [
            $template,
            $template
        ];
        $template['phoneNumber'] = '79261234567';
        $result[] = [
            $template,
            $template
        ];
        $template['phoneNumber'] = '+7 926 123 45 67';
        $result[] = [
            $template,
            $template
        ];
        $template['phoneNumber'] = '8(926)123-45-67';
        $result[] = [
            $template,
            $template
        ];
        $template['phoneNumber'] = '123-45-67';
        $result[] = [
            $template,
            $template
        ];
        $template['phoneNumber'] = '9261234567';
        $result[] = [
            $template,
            $template
        ];
        $template['phoneNumber'] = '(495)1234567';
        $result[] = [
            $template,
            $template
        ];
        $template['phoneNumber'] = '(495) 123 45 67';
        $result[] = [
            $template,
            $template
        ];
        $template['phoneNumber'] = '(495)123-45-67';
        $result[] = [
            $template,
            $template
        ];
        $template['phoneNumber'] = '8-926-123-45-67';
        $result[] = [
            $template,
            $template
        ];
        $template['phoneNumber'] = '8 927 1234 234';
        $result[] = [
            $template,
            $template
        ];
        $template['phoneNumber'] = '8 927 12 12 888';
        $result[] = [
            $template,
            $template
        ];
        $template['phoneNumber'] = '8 927 12 555 12';
        $result[] = [
            $template,
            $template
        ];
        $template['phoneNumber'] = '8 927 123 8 123';
        $result[] = [
            $template,
            $template
        ];
        //long phoneNumber
        $values = $template;
        $values['phoneNumber'] = '36253453274537325436543654345327453645364343463245324536462';
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property phoneNumber is incorrect. Must be at most 25 characters long. '
                ]
            ]
        ];
        //empty phoneNumber
        $values = $template;
        $values['phoneNumber'] = '      ';
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => \InvalidArgumentException::class,
                    'code' => '400',
                    'message' => 'phoneNumber is empty.'
                ]
            ]
        ];
        //incorrect phoneNumber
        $values = $template;
        $values['phoneNumber'] = '234637n';
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => \InvalidArgumentException::class,
                    'code' => '400',
                    'message' => 'phoneNumber is incorrect.'
                ]
            ]
        ];
        //incorrect phoneNumber
        $values = $template;
        $values['phoneNumber'] = 'fbsj dj dshsuid';
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => \InvalidArgumentException::class,
                    'code' => '400',
                    'message' => 'phoneNumber is incorrect.'
                ]
            ]
        ];
        //long login, numerical organization, no phone number specified
        $values = $template;
        unset($values['phoneNumber']);
        $values['login'] = 'Petyfddfsjkdfsdkfsdjfjdsfdsjfdsjkfjdkfdjfjkdsfjfjdfffffffffffffffffffffdddddddddddddddddddddddddddddddddddddddddsjkfdskfkdsa2у3';
        $values['organization'] = 3424;
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property phoneNumber is required. The property login is incorrect. Must be at most 80 characters long. The property organization is incorrect. Integer value found, but a string is required. '
                ]
            ]
        ];
        //additional property
        $values = $template;
        $values['new'] = 'hello';
        $result[] = [
            $values,
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property new is not defined and the definition does not allow additional properties. '
                ]
            ]
        ];

        return $result;
    }
    /**
     * @dataProvider dataValidateUpdateData
     */
    public function testValidateUpdateData($data, $expectedValues)
    {
        if (isset($expectedValues['exception'])) {
            $exception = $expectedValues['exception'];
            $this->expectException($exception['class']);
            $this->expectExceptionCode($exception['code']);
            $this->expectExceptionMessage($exception['message']);
        }
        $values = $this->fixture->validateUpdateData($data, $this->user);

        $this->assertEquals($values, $expectedValues);
    }

    public function dataValidateUpdateData()
    {
        $result = [];
        $template = [
            'login' => 'Petr',
            'name' => 'Петр',
            'surname' => 'Иванов',
            'hash' => 'ejrherhejkrhe',
            'organization' => 'PSU',
            'email' => 'petr@gmail.com',
            'phoneNumber' => '88005553535'
        ];
        //correct login
        $values = $template;
        $values['login'] = 'Petr2';
        $result[] = [
            [
                'login' => 'Petr2'
            ],
            $values
        ];
        //correct login, name
        $values = $template;
        $values['login'] = 'Petr2';
        $values['name'] = 'Петя';
        $result[] = [
            [
                'login' => 'Petr2',
                'name' => 'Петя'
            ],
            $values
        ];
        //long login
        $result[] = [
            [
                'login' => 'bfdhfbdsjdnasjkdsjkdksjdnjbfjkdbfjkdbjdddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddd'
            ],
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property login is incorrect. Must be at most 80 characters long.'
                ]
            ]
        ];

        //empty login
        $result[] = [
            [
                'login' => '          '
            ],
            [
                'exception' => [
                    'class' => \InvalidArgumentException::class,
                    'code' => '400',
                    'message' => 'login is empty.'
                ]
            ]
        ];

        //numerical login
        $result[] = [
            [
                'login' => 125
            ],
            [
                'exception' => [
                    'class' => \InvalidArgumentException::class,
                    'code' => '400',
                    'message' => 'The property login is incorrect. Integer value found, but a string is required.'
                ]
            ]
        ];

        //long name
        $result[] = [
            [
                'name' => 'bfdhfbdsjdnasjkdsjkdksjdnjbfjkdbfjkdbjdddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddd'
            ],
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property name is incorrect. Must be at most 80 characters long.'
                ]
            ]
        ];
        //empty name

        $result[] = [
            [
                'name' => '          '
            ],
            [
                'exception' => [
                    'class' => \InvalidArgumentException::class,
                    'code' => '400',
                    'message' => 'name is empty.'
                ]
            ]
        ];
        //numerical name
        $result[] = [
            [
                'name' => 12
            ],
            [
                'exception' => [
                    'class' => \InvalidArgumentException::class,
                    'code' => '400',
                    'message' => 'The property name is incorrect. Integer value found, but a string is required.'
                ]
            ]
        ];
        //long surname
        $result[] = [
            [
                'surname' => 'bfdhfbdsjdnasjkdsjkdksjdnjbfjkdbfjkdbjdddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddd'
            ],
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property surname is incorrect. Must be at most 80 characters long.'
                ]
            ]
        ];
        //empty surname
        $result[] = [
            [
                'surname' => '      '
            ],
            [
                'exception' => [
                    'class' => \InvalidArgumentException::class,
                    'code' => '400',
                    'message' => 'surname is empty.'
                ]
            ]
        ];
        //numerical surname
        $result[] = [
            [
                'surname' => 123
            ],
            [
                'exception' => [
                    'class' => \InvalidArgumentException::class,
                    'code' => '400',
                    'message' => 'The property surname is incorrect. Integer value found, but a string is required.'
                ]
            ]
        ];
        //long password
        $result[] = [
            [
                'password' => 'bfdhfbdsjdnasjkdsjkdksjdnjbfjkdbfjkdbjdddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddd'
            ],
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property password is incorrect. Must be at most 40 characters long.'
                ]
            ]
        ];

        //short password
        $result[] = [
            [
                'password' => 'qwerty'
            ],
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property password is incorrect. Must be at least 8 characters long. '
                ]
            ]
        ];
        //empty password
        $result[] = [
            [
                'password' => '         '
            ],
            [
                'exception' => [
                    'class' => \InvalidArgumentException::class,
                    'code' => '400',
                    'message' => 'password is empty.'
                ]
            ]
        ];
        //numerical password
        $result[] = [
            [
                'password' => 120
            ],
            [
                'exception' => [
                    'class' => \InvalidArgumentException::class,
                    'code' => '400',
                    'message' => 'The property password is incorrect. Integer value found, but a string is required.'
                ]
            ]
        ];

        //long organization;
        $result[] = [
            [
                'organization' => 'bfdhfbdsjdnasjkdsjkdbfdhfbdsjdnasjkdsjkdksjdnjbfjkdbfjkdbjdddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddd'
            ],
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property organization is incorrect. Must be at most 100 characters long.'
                ]
            ]
        ];
        //empty organization
        $result[] = [
            [
                'organization' => '      '
            ],
            [
                'exception' => [
                    'class' => \InvalidArgumentException::class,
                    'code' => '400',
                    'message' => 'organization is empty.'
                ]
            ]
        ];
        //numerical organization
        $result[] = [
            [
                'organization' => 15
            ],
            [
                'exception' => [
                    'class' => \InvalidArgumentException::class,
                    'code' => '400',
                    'message' => 'The property organization is incorrect. Integer value found, but a string is required. '
                ]
            ]
        ];

        //long email
        $result[] = [
            [
                'email' => 'bfdhfbdsjdnasjkdsjkdbfdhfbdsjdnasjkdsjkdksjdnjbfjkdbfjkdbjdddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddddd@gmail.com'
            ],
            [
                'exception' => [
                    'class' => ValidationException::class,
                    'code' => '400',
                    'message' => 'The property email is incorrect. Must be at most 80 characters long. '
                ]
            ]
        ];
        //empty email
        $result[] = [
            [
                'email' => '      '
            ],
            [
                'exception' => [
                    'class' => \InvalidArgumentException::class,
                    'code' => '400',
                    'message' => 'email is empty.'
                ]
            ]
        ];
        //numerical email
        $result[] = [
            [
                'email' => 15
            ],
            [
                'exception' => [
                    'class' => \InvalidArgumentException::class,
                    'code' => '400',
                    'message' => 'The property email is incorrect. Integer value found, but a string is required. '
                ]
            ]
        ];
        //incorrect email
        $result[] = [
            [
                'email' => 'petr.igmail.com'
            ],
            [
                'exception' => [
                    'class' => \InvalidArgumentException::class,
                    'code' => '400',
                    'message' => 'email is incorrect.'
                ]
            ]
        ];
        //correct numbers
        $template['phoneNumber'] = '+79261234567';
        $result[] = [
            [
                'phoneNumber' => '+79261234567'
            ],
            $template
        ];
        $template['phoneNumber'] = '79261234567';
        $result[] = [
            [
                'phoneNumber' => '79261234567'
            ],
            $template
        ];
        $template['phoneNumber'] = '+7 926 123 45 67';
        $result[] = [
            [
                'phoneNumber' => '+7 926 123 45 67'
            ],
            $template
        ];
        $template['phoneNumber'] = '8(926)123-45-67';
        $result[] = [
            [
                'phoneNumber' => '8(926)123-45-67'
            ],
            $template
        ];
        $template['phoneNumber'] = '123-45-67';
        $result[] = [
            [
                'phoneNumber' => '123-45-67'
            ],
            $template
        ];
        $template['phoneNumber'] = '9261234567';
        $result[] = [
            [
                'phoneNumber' => '9261234567'
            ],
            $template
        ];
        $template['phoneNumber'] = '(495)1234567';
        $result[] = [
            [
                'phoneNumber' => '(495)1234567'
            ],
            $template
        ];
        $template['phoneNumber'] = '(495) 123 45 67';
        $result[] = [
            [
                'phoneNumber' => '(495) 123 45 67'
            ],
            $template
        ];
        $template['phoneNumber'] = '(495)123-45-67';
        $result[] = [
            [
                'phoneNumber' => '(495)123-45-67'
            ],
            $template
        ];
        $template['phoneNumber'] = '8-926-123-45-67';
        $result[] = [
            [
                'phoneNumber' => '8-926-123-45-67'
            ],
            $template
        ];
        $template['phoneNumber'] = '8 927 1234 234';
        $result[] = [
            [
                'phoneNumber' => '8 927 1234 234'
            ],
            $template
        ];
        $template['phoneNumber'] = '8 927 12 12 888';
        $result[] = [
            [
                'phoneNumber' => '8 927 12 12 888'
            ],
            $template
        ];
        $template['phoneNumber'] = '8 927 12 555 12';
        $result[] = [
            [
                'phoneNumber' => '8 927 12 555 12'
            ],
            $template
        ];
        $template['phoneNumber'] = '8 927 123 8 123';
        $result[] = [
            [
                'phoneNumber' => '8 927 123 8 123'
            ],
            $template
        ];
        //long phoneNumber
        $result[] = [
            [
                'phoneNumber' => '36253453274537325436543654345327453645364343463245324536462'
            ],
            [
                'exception' => [
                    'class' => \InvalidArgumentException::class,
                    'code' => '400',
                    'message' => 'phoneNumber is incorrect.'
                ]
            ]
        ];
        //empty phoneNumber
        $result[] = [
            [
                'phoneNumber' => '      '
            ],
            [
                'exception' => [
                    'class' => \InvalidArgumentException::class,
                    'code' => '400',
                    'message' => 'phoneNumber is incorrect.'
                ]
            ]
        ];
        //incorrect phoneNumber
        $result[] = [
            [
                'phoneNumber' => '234637n'
            ],
            [
                'exception' => [
                    'class' => \InvalidArgumentException::class,
                    'code' => '400',
                    'message' => 'phoneNumber is incorrect.'
                ]
            ]
        ];
        //incorrect phoneNumber
        $result[] = [
            [
                'phoneNumber' => 'fbsj dj dshsuid'
            ],
            [
                'exception' => [
                    'class' => \InvalidArgumentException::class,
                    'code' => '400',
                    'message' => 'phoneNumber is incorrect.'
                ]
            ]
        ];
        //long login, numerical organization
        $result[] = [
            [
                'login' => 'Petyfddfsjkdfsdkfsdjfjdsfdsjfdsjkfjdkfdjfjkdsfjfjdfffffffffffffffffffffdddddddddddddddddddddddddddddddddddddddddsjkfdskfkdsa2у3',
                'organization' => 3424
            ],
            [
                'exception' => [
                    'class' => \InvalidArgumentException::class,
                    'code' => '400',
                    'message' => 'The property organization is incorrect. Integer value found, but a string is required. '
                ]
            ]
        ];
        //additional property
        $result[] = [
            [
                'new' => 'hello'
            ],
            [
                'exception' => [
                    'class' => \InvalidArgumentException::class,
                    'code' => '400',
                    'message' => 'Updates parameters are not found.'
                ]
            ]
        ];

        return $result;
    }
}