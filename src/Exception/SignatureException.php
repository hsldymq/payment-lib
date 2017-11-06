<?php
namespace Archman\PaymentLib\Exception;

class SignatureException extends \Exception
{
    private $data;

    protected $message = 'Failed To Validate Signature';

    public function __construct(array $data, $message = "", $code = 0, \Throwable $previous = null)
    {
        $this->data = $data;
        parent::__construct($message ?: $this->message, $code, $previous);
    }

    public function getInvalidSignedData(): array
    {
        return $this->data;
    }
}