<?php
namespace Archman\PaymentLib\Exception;

class SignatureException extends \Exception
{
    protected $message = 'Failed To Validate Signature';
}