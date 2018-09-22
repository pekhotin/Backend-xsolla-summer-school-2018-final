<?php

namespace App\Validator;

use App\Model\User;
use App\Service\UserService;

class UserValidator extends BaseValidator
{
    /**
     * UserValidator constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->schemaPath = __DIR__ . '/../../resources/jsonSchema/user.json';
    }
    // добавить проверку для номера телефона
    /**
     * @param array $values
     *
     * @return array
     */
    public function validateInsertData($values)
    {
        $this->jsonSchemaValidator->checkBySchema($values, $this->schemaPath);

        foreach ($values as $key => &$value) {
            if ($key !== 'password') {
                $value = $this->validateVar($value, 'string', $key);
            }
        }

        $values['password'] = $this->validateVar($values['password'], 'password', 'password');
        $values['phoneNumber'] = $this->validateVar($values['phoneNumber'], 'phone',  'phoneNumber');

        if(filter_var($values['email'], FILTER_VALIDATE_EMAIL) === false) {
            throw new \InvalidArgumentException(
                'email is incorrect.',
                400
            );
        }

        $this->jsonSchemaValidator->checkBySchema($values, $this->schemaPath);

        return $values;
    }
    /**
     * @param array $data
     * @param User $user
     *
     * @return array
     */
    public function validateUpdateData($data, $user)
    {
        if (!isset($data['name']) &&
            !isset($data['surname']) &&
            !isset($data['login']) &&
            !isset($data['organization']) &&
            !isset($data['email']) &&
            !isset($data['password']) &&
            !isset($data['phoneNumber']
            )) {
            throw new \InvalidArgumentException(
                'Updates parameters are not found.',
                400
            );
        }

        $values['name'] = isset($data['name'])
            ? $this->validateVar($data['name'], 'string', 'name')
            : $user->getName();
        $values['surname'] = isset($data['surname'])
            ? $this->validateVar($data['surname'], 'string', 'surname')
            : $user->getSurname();
        $values['login'] = isset($data['login'])
            ? $this->validateVar($data['login'], 'string', 'login')
            : $user->getLogin();
        $values['organization'] = isset($data['organization'])
            ? $this->validateVar($data['organization'], 'string', 'organization')
            : $user->getOrganization();
        $values['email'] = isset($data['email'])
            ? $this->validateVar($data['email'], 'string', 'email')
            : $user->getEmail();
        $values['password'] = isset($data['password'])
            ? $this->validateVar($data['password'], 'password', 'password')
            : 'qwerty123';
        $values['phoneNumber'] = isset($data['phoneNumber'])
            ? $this->validateVar($data['phoneNumber'], 'phone', 'phoneNumber')
            : $user->getPhoneNumber();

        $this->jsonSchemaValidator->checkBySchema($values, $this->schemaPath);

        if(filter_var($values['email'], FILTER_VALIDATE_EMAIL) === false) {
            throw new \InvalidArgumentException(
                "email is incorrect.",
                400
            );
        }
        if (isset($data['password']) !== false) {
            $values['hash'] = password_hash($values['password'], PASSWORD_DEFAULT);

        } else {
            $values['hash'] = $user->getPasswordHash();
        }
        unset($values['password']);

        return $values;
    }
}