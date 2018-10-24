<?php

namespace Archman\PaymentLib\Test\Request\WeChat;

use PHPUnit\Framework\TestCase;
use Archman\PaymentLib\Test\Config;
use Archman\PaymentLib\Test\Config\WeChatConfig;
use Archman\PaymentLib\Request\WeChat\AuthCodeToOpenID;

class AuthCodeToOpenIDTest extends TestCase
{
    public function testMakingParameters()
    {
        $cases = Config::get('wechat', 'testCases', 'request', 'AuthCodeToOpenID');
        foreach ($cases as $each) {
            $configData = Config::get('wechat', 'config', $each['appID']);
            $config = new WeChatConfig($configData);

            $request = (new AuthCodeToOpenID($config))
                ->setAuthCode($each['fields']['auth_code'])
                ->setNonceStr($each['fields']['noncestr']);

            $this->assertEquals($each['parameters'], $request->makeParameters());
        }
    }
}