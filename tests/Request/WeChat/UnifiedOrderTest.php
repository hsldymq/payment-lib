<?php

namespace Archman\PaymentLib\Test\Request\WeChat;

use Archman\PaymentLib\Request\Client;
use Archman\PaymentLib\Request\WeChat\UnifiedOrder;
use PHPUnit\Framework\TestCase;
use Archman\PaymentLib\Test\Config;
use Archman\PaymentLib\Test\Config\WeChatConfig;

class UnifiedOrderTest extends TestCase
{
    public function testMakePrepayOrder()
    {
        $cases = Config::get('wechat', 'testCases', 'request', 'UnifiedOrder');
        foreach ($cases as $each) {
            $configData = Config::get('wechat', 'config', $each['appID']);
            $config = new WeChatConfig($configData);

            $outTradeNo = md5(strval(microtime(true) * 1000));
            $request = (new UnifiedOrder($config))
                ->setOutTradeNo($outTradeNo)
                ->setBody($each['fields']['body'])
                ->setTotalFee($each['fields']['total_fee'])
                ->setSPBillCreateIP($each['fields']['spbill_create_ip'])
                ->setNotifyUrl($each['fields']['notify_url'])
                ->setTradeType($each['fields']['trade_type'])
                ->setOpenID($each['fields']['openid'] ?? null)
                ->setSceneInfo($each['fields']['scene_info'] ?? null);

            $response = $request->send();

            $this->assertArrayHasKey('prepay_id', $response);
        }
    }
}