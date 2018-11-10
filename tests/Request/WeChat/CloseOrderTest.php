<?php

namespace Archman\PaymentLib\Test\Request\WeChat;

use Archman\PaymentLib\Request\WeChat\CloseOrder;
use PHPUnit\Framework\TestCase;
use Archman\PaymentLib\Test\Config;
use Archman\PaymentLib\Test\Config\WeChatConfig;

class CloseOrderTest extends TestCase
{
    public function testMakingTransferParameters()
    {
        $cases = Config::get('wechat', 'testCases', 'request', 'CloseOrder');
        foreach ($cases as $each) {
            $configData = Config::get('wechat', 'config', $each['appID']);
            $config = new WeChatConfig($configData);

            $request = (new CloseOrder($config))
                ->setOutTradeNo($each['fields']['out_trade_no'])
                ->setNonceStr($each['fields']['nonce_str']);

            $this->assertEquals($each['parameters'], $request->makeParameters());
        }
    }
}