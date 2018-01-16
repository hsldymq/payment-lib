<?php
namespace Archman\PaymentLib\Test\Callback\Alipay;

use \PHPUnit\Framework\TestCase;
use \Archman\PaymentLib\Test\Config\AlipayConfig;
use Archman\PaymentLib\SignatureHelper\Alipay\Validator;
use Archman\PaymentLib\Test\Config;

class SignatureTest extends TestCase
{
    public function testPay()
    {
        $cases = Config::get('alipay', 'testCases', 'callback', 'pay');
        foreach ($cases as $each) {
            $sign = $each['signature'];
            $signType = $each['signType'];
            $configData = Config::get('alipay', 'config', $each['appID']);
            $config = new AlipayConfig($configData);
            $validator = new Validator($config);

            $this->assertTrue($validator->validateSignAsync($sign, $signType, $each['data']));
        }
    }

    public function testBatchRefund()
    {
        $cases = Config::get('alipay', 'testCases', 'callback', 'batchRefund');
        foreach ($cases as $each) {
            $sign = $each['signature'];
            $signType = $each['signType'];
            $configData = Config::get('alipay', 'config', $each['appID']);
            $config = new AlipayConfig($configData);
            $validator = new Validator($config, true);

            $this->assertTrue($validator->validateSignAsync($sign, $signType, $each['data']));
        }
    }

    public function testBatchTransfer()
    {
        $cases = Config::get('alipay', 'testCases', 'callback', 'batchTransfer');
        foreach ($cases as $each) {
            $sign = $each['signature'];
            $signType = $each['signType'];
            $configData = Config::get('alipay', 'config', $each['appID']);
            $config = new AlipayConfig($configData);
            $validator = new Validator($config, true);

            $this->assertTrue($validator->validateSignAsync($sign, $signType, $each['data']));
        }
    }
}