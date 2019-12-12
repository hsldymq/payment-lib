<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Test\Request\Apple;

use PHPUnit\Framework\TestCase;
use Archman\PaymentLib\Test\Config;
use Archman\PaymentLib\Request\Apple\IAPReceiptValidation;
use Archman\PaymentLib\Request\Client;

class IAPTest extends TestCase
{
    public function testProductionValidation()
    {
        $receipt = Config::get('appleReceipt', 'production');
        $request = (new IAPReceiptValidation())
            ->setReceiptData(strval($receipt))
            ->setEnvironment(true);
        $data = $request->send();

        $this->assertEquals(0, $data['status']);
    }

    public function testSandboxValidation()
    {
        $receipt = Config::get('appleReceipt', 'sandbox');
        $request = (new IAPReceiptValidation())
            ->setReceiptData(strval($receipt))
            ->setEnvironment(false);
        $data = $request->send();

        $this->assertEquals(0, $data['status']);
    }

    /**
     * @expectedException \Archman\PaymentLib\Exception\ErrorResponseException
     */
    public function testWrongEnvReceipt_ExpectException()
    {
        $receipt = Config::get('appleReceipt', 'production');
        (new IAPReceiptValidation())
            ->setReceiptData(strval($receipt))
            ->setEnvironment(false)
            ->send();
    }

    /**
     * @expectedException \Archman\PaymentLib\Exception\ErrorResponseException
     */
    public function testInvalidReceipt_ExceptExcetion()
    {
        (new IAPReceiptValidation())
            ->setReceiptData('test')
            ->setEnvironment(false)
            ->send();
    }
}