<?php
namespace Archman\PaymentLib\Test\Reponse\Huawei\Async;

use \PHPUnit\Framework\TestCase;
use \Archman\PaymentLib\Test\Config\HuaweiConfig;
use Archman\PaymentLib\Test\Config;
use Archman\PaymentLib\SignatureHelper\Huawei\Validator;

class SignatureTest extends TestCase
{
    public function testCallbackSignatureValidation()
    {
        $config = new HuaweiConfig(Config::get('huawei', 'config'));
        $testCases = Config::get('huawei', 'testCases', 'callback');
        $validator = new Validator($config);

        foreach ($testCases as $case) {
            $this->assertTrue($validator->validate($case['signature'], $case['signType'], $case['data']));
        }
    }
}