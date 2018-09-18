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
            $value = $this->validateVar(trim($value), 'string', $key);
        }

        $this->jsonSchemaValidator->checkBySchema($values, $this->schemaPath);

        if(filter_var($values['email'], FILTER_VALIDATE_EMAIL) === false) {
            throw new \LogicException( "email is incorrect!", 400);
        }

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
            throw new \LogicException(
                'updates parameters are not found!',
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
            ? $this->validateVar($data['password'], 'string', 'password')
            : $user->getPasswordHash();
        $values['phoneNumber'] = isset($data['phoneNumber'])
            ? $this->validateVar($data['phoneNumber'], 'string', 'phoneNumber')
            : $user->getPhoneNumber();

        $this->jsonSchemaValidator->checkBySchema($values, $this->schemaPath);

        $values['hash'] = password_hash($values['password'], PASSWORD_DEFAULT);
        unset($values['password']);

        if(filter_var($values['email'], FILTER_VALIDATE_EMAIL) === false) {
            throw new \LogicException( "email is incorrect!", 400);
        }

        return $values;
    }
}