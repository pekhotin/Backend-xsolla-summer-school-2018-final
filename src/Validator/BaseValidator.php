<?php

namespace App\Validator;

use JsonSchema\Validator;

class BaseValidator
{
    /**
     * @var JsonSchemaValidator
     */
    protected $jsonSchemaValidator;

    /**
     * @var array
     */
    protected $data;

    /**
     * @var string
     */
    protected $schemaPath;

    /**
     * BaseValidator constructor.
     */
    public function __construct()
    {
        $this->jsonSchemaValidator = new JsonSchemaValidator(new Validator());
    }

    /**
     * @param $var
     * @param string $type
     * @param string $name
     * @return float|int|null|string
     */
    public function validateVar($var, $type, $name)
    {
        switch ($type) {

            case 'int':
                if (filter_var($var, FILTER_VALIDATE_INT) === false) {
                    throw new \LogicException("{$name} is not integer!", 400);
                }
                if ($var <= 0) {
                    throw new \LogicException("{$name} must be greater than 0!", 400);
                }
                return (int)$var;

            case 'string':
                if (empty($var)) {
                    throw new \LogicException("{$name} is empty!", 400);
                }
                return (string)$var;

            case 'float':
                if (filter_var($var, FILTER_VALIDATE_FLOAT) === false) {
                    throw new \LogicException( "{$name} is not float!", 400);
                }
                return (float)$var;
            case 'date':
                $date = date_parse_from_format('Y-m-d', $var);
                if(checkdate($date['month'], $date['day'], $date['year']) === false) {
                    throw new \LogicException("date format is not 'Y-m-d'!", 400);
                }
                return $var;
            default:
                return null;
        }
    }
}