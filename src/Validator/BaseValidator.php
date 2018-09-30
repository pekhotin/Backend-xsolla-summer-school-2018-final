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
                    throw new \InvalidArgumentException(
                        "{$name} is not integer.",
                        400
                    );
                }
                if ($var <= 0) {
                    throw new \InvalidArgumentException(
                        "{$name} must be greater than 0.",
                        400
                    );
                }
                return (int)$var;
            case 'string':
                if (is_string($var) === false) {
                    throw new \InvalidArgumentException(
                        "The property {$name} is incorrect. A string value is required.",
                        400
                    );
                }
                if (empty(trim($var)) === true) {
                    throw new \InvalidArgumentException(
                        "{$name} is empty.",
                        400
                    );
                }
                return (string)trim($var);
            case 'float':
                if (filter_var($var, FILTER_VALIDATE_FLOAT) === false) {
                    throw new \InvalidArgumentException(
                        "{$name} is not float.",
                        400
                    );
                }
                return (float)$var;
            case 'date':
                $date = date_parse_from_format('Y-m-d', trim($var));
                if (checkdate($date['month'], $date['day'], $date['year']) === false) {
                    throw new \InvalidArgumentException(
                        "date format is not 'Y-m-d'.",
                        400
                    );
                }
                return date('Y-m-d', strtotime($var));
            case 'phone':
                //скобки, тире и пробелы можно, буквы нельзя
                if (preg_match('/^((8|\+7)[\- ]?)?(\(?\d{3}\)?[\- ]?)?[\d\- ]{7,10}$/', $var) === 0) {
                    throw new \InvalidArgumentException(
                        "{$name} is incorrect.",
                        400
                    );
                }
                return trim($var);
            case 'password':
                if (is_string($var) === false) {
                    throw new \InvalidArgumentException(
                        "The property {$name} is incorrect. A string value is required.",
                        400
                    );
                }
                if (empty(trim($var))) {
                    throw new \InvalidArgumentException(
                        "{$name} is empty.",
                        400
                    );
                }
                return (string)$var;
            default:
                return null;
        }
    }
}
