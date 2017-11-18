<?php
use \PHPUnit\Framework\TestCase;
use \Archman\PaymentLib\Test\Config\AlipayConfig;
use Archman\PaymentLib\SignatureHelper\Alipay\Validator;
use Archman\PaymentLib\Test\Config;

class PaySignatureTest extends TestCase
{
    public function testPaySuccess()
    {
        $cases = Config::get('alipay', 'testCases', 'callback', 'pay');
        foreach ($cases as $each) {
            if ($each['type'] !== 'success') {
                continue;
            }

            $sign = $each['signature'];
            $signType = $each['signType'];
            $configData = Config::get('alipay', 'config', $each['appID']);
            $config = new AlipayConfig($configData);
            $validator = new Validator($config);

            $this->assertTrue($validator->validateSignAsync($sign, $signType, $each['data']));
        }
    }
}