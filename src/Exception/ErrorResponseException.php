<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Exception;

class ErrorResponseException extends \Exception
{
    private $errorCode = null;

    private $errorText = null;

    private $responseData = null;

    /**
     * @param string|null $errorCode
     * @param string|null $errorText
     * @param array|null $responseData
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct(
        $errorCode = null,
        $errorText = null,
        $responseData = null,
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