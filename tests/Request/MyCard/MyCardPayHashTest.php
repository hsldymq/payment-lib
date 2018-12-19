<?php

namespace Archman\PaymentLib\Test\Request\MyCard;

use Archman\PaymentLib\SignatureHelper\MyCard\Generator;
use Archman\PaymentLib\SignatureHelper\MyCard\Validator;
use Archman\PaymentLib\Test\Config;
use Archman\PaymentLib\Test\Config\MyCardConfig;
use PHPUnit\Framework\TestCase;

class MyCardPayHashTest extends TestCase
{
    public function testValidateHash()
    {
        $cases = Config::get('mycard', 'testCases', 'request', 'MyCardPayHash');
        foreach ($cases as $each) {
            $configData = Config::get('mycard', 'config', $each['FacServiceID']);
            $config = new MyCardConfig($configData);

            if ($each['type'] === 'validating') {
                $result = (new Validator($config))->validatePayResultHash($each['fields']['hash'], $each['data']);
                $this->assertTrue($result);
            } else {
                $hash = (new Generator($config))->makeHash($each['data']);
                $this->assertEquals($each['fields']['hash'], $hash);
            }

        }
    }
}