<?php

namespace Archman\PaymentLib\Test\Request\WeChat;

use Archman\PaymentLib\Request\WeChat\OrderQuery;
use PHPUnit\Framework\TestCase;
use Archman\PaymentLib\Test\Config;
use Archman\PaymentLib\Test\Config\WeChatConfig;

class OrderQueryTest extends TestCase
{
    public function testMakingParameters()
    {
        $cases = Config::get('wechat', 'testCases', 'request', 'OrderQuery');
        foreach ($cases as $each) {
            $configData = Config::get('wechat', 'config', $each['appID']);
            $config = new WeChatConfig($configData);

            $request = (new OrderQuery($config))
                ->setNonceStr($each['fields']['nonce_str'])
                ->setOutTradeNo($each['fields']['out_trade_no']);

            $this->assertEquals($each['parameters'], $request->makeParameters());
        }
    }
}