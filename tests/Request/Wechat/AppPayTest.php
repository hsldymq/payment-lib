<?php
namespace Archman\PaymentLib\Test\Request\WeChat;

use PHPUnit\Framework\TestCase;
use Archman\PaymentLib\Test\Config;
use Archman\PaymentLib\Test\Config\WeChatConfig;
use Archman\PaymentLib\Request\WeChat\AppPay;

class AppPayTest extends TestCase
{
    public function testMakingTransferParameters()
    {
        $cases = Config::get('wechat', 'testCases', 'request', 'AppPay');
        foreach ($cases as $each) {
            $configData = Config::get('wechat', 'config', $each['appID']);
            $config = new WeChatConfig($configData);

            $request = (new AppPay($config))
                ->setPrepayID($each['fields']['prepay_id'])
                ->setTimestamp(new DateTime($each['fields']['timestamp'], new DateTimeZone('+0800')))
                ->setNonceStr($each['fields']['noncestr']);

            $this->assertEquals($each['parameters'], $request->makeParameters());
        }
    }
}