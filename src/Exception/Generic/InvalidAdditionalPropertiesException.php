<?php

declare(strict_types = 1);

namespace PHPModelGenerator\Exception\Generic;

use PHPModelGenerator\Exception\ValidationException;

/**
 * Class InvalidAdditionalPropertiesException
 *
 * @package PHPModelGenerator\Exception\Generic
 */
class InvalidAdditionalPropertiesException extends ValidationException
{
    protected const MAIN_MESSAGE = 'Provided JSON for %s contains invalid additional properties.';
    protected const TYPE = 'additional property';

    /** @var ValidationException[][] */
    protected $nestedExceptions;

    /**
     * InvalidAdditionalPropertiesException constructor.
     *
     * @param $providedValue
     * @param string $propertyName
     * @param ValidationException[][] $nestedExceptions
     */
    public function __construct($providedValue, string $propertyName, $nestedExceptions)
    {
        $this->nestedExceptions= $nestedExceptions;

        parent::__construct($this->getErrorMessage(), $propertyName, $providedValue);
    }

    /**
     * Get a list of nested exceptions containing all failed validations indexed by the property name

     * @return ValidationException[][]
     */
    public function getNestedExceptions(): array
    {
        return $this->nestedExceptions;
    }

    protected function getErrorMessage(): string
    {
        $output = '';
        foreach ($this->nestedExceptions as $propertyName => $exceptions) {
            $output .= sprintf(
                "\n  - invalid %s '%s'\n    * %s",
                self::TYPE,
                $propertyName,
                implode(
                    "\n    * ",
                    str_replace(
                        "\n",
                        "\n    ",
                        array_map(function (ValidationException $exception): string {
                            return $exception->getMessage();
                        }, $exceptions)
                    )
                )
            );
        }

        return sprintf(static::MAIN_MESSAGE, $this->propertyName) . $output;
    }
}
