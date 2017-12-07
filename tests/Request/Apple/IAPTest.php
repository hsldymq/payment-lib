<?php
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
            ->setReceiptData($receipt)
            ->setEnvironment(true);
        $data = Client::sendRequest($request);

        $this->assertEquals(0, $data['status']);
    }

    public function testSandboxValidation()
    {
        $receipt = Config::get('appleReceipt', 'sandbox');
        $request = (new IAPReceiptValidation())
            ->setReceiptData($receipt)
            ->setEnvironment(false);
        $data = Client::sendRequest($request);

        $this->assertEquals(0, $data['status']);
    }

    /**
     * @expectedException \Archman\PaymentLib\Exception\ErrorResponseException
     */
    public function testWrongEnvReceipt_ExpectException()
    {
        $receipt = Config::get('appleReceipt', 'production');
        $request = (new IAPReceiptValidation())
            ->setReceiptData($receipt)
            ->setEnvironment(false);
        Client::sendRequest($request);
    }

    /**
     * @expectedException \Archman\PaymentLib\Exception\ErrorResponseException
     */
    public function testInvalidReceipt_ExceptExcetion()
    {
        $request = (new IAPReceiptValidation())
            ->setReceiptData('test')
            ->setEnvironment(false);
        Client::sendRequest($request);
    }
}