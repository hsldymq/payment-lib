<?php
namespace Archman\PaymentLib\Exception;

class InvalidParameterException extends \Exception
{
    private $paramName;

    public function __construct(string $paramName, $message = "", $code = 0, \Throwable $previous = null)
    {
        $this->paramName = $paramName;

        parent::__construct($message, $code, $previous);
    }

    public function getInvalidParamName(): string
    {
        return $this->paramName;
    }
}