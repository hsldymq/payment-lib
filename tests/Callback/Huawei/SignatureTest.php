<?php
use \PHPUnit\Framework\TestCase;
use \Archman\PaymentLib\Test\Config\HuaweiConfig;
use Archman\PaymentLib\Test\Config;

class SignatureTest extends TestCase
{
    public function testCallbackSignatureValidation()
    {
        $config = new HuaweiConfig(Config::get('huawei', 'config'));
        $testCases = Config::get('huawei', 'testCases', 'callback');
        $validator = new \Archman\PaymentLib\SignatureHelper\Huawei\Validator($config);

        foreach ($testCases as $case) {
            $this->assertTrue($validator->validate($case['signature'], $case['signType'], $case['data']));
        }
    }
}