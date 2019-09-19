<?php

namespace Archman\PaymentLib\Exception;

class ErrorResponseException extends \Exception
{
    private $errorCode = null;

    private $errorText = null;

    private $responseData = null;

    public function __construct(
        ?string $errorCode = null,
        ?string $errorText = null,
        ?array $responseData = null,
        $message = "",
        $code = 0,
        \Throwable $previous = null
    ) {
        $this->errorCode = $errorCode;
        $this->errorText = $errorText;
        $this->responseData = $responseData;

        parent::__construct($message, $code, $previous);
    }

    /**
     * @return string|null
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * @return string|null
     */
    public function getErrorText()
    {
        return $this->errorText;
    }

    /**
     * @return array|null
     */
    public function getResponseData()
    {
        return $this->responseData;
    }
}