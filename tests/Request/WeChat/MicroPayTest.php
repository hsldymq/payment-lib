<?php

namespace Archman\PaymentLib\Test\Request\WeChat;

use Archman\PaymentLib\Request\WeChat\MicroPay;
use PHPUnit\Framework\TestCase;
use Archman\PaymentLib\Test\Config;
use Archman\PaymentLib\Test\Config\WeChatConfig;

class MicroPayTest extends TestCase
{
    public function testMakingParameters()
    {
        $cases = Config::get('wechat', 'testCases', 'request', 'MicroPay');
        foreach ($cases as $each) {
            $configData = Config::get('wechat', 'config', $each['appID']);
            $config = new WeChatConfig($configData);

            $request = (new MicroPay($config))
                ->setOutTradeNo($each['fields']['out_trade_no'])
                ->setBody($each['fields']['body'])
                ->setTotalFee($each['fields']['total_fee'])
                ->setSPBillCreateIP($each['fields']['spbill_create_ip'])
                ->setAuthCode($each['fields']['auth_code'])
                ->setNonceStr($each['fields']['nonce_str'])
                ->setDeviceInfo($each['fields']['device_info'] ?? null)
                ->setAttach($each['fields']['attach'] ?? null)
                ->setFeeType($each['fields']['fee_type'] ?? null)
                ->setGoodsTag($each['fields']['goods_tag'] ?? null)
                ->setLimitPay($each['fields']['limit_pay'] ?? null)
                ->setTimeStart($each['fields']['time_start'] ?? null)
                ->setTimeExpire($each['fields']['time_expire'] ?? null);
            if (isset($each['fields']['detail'])) {
                $request->setDetail(...$each['fields']['detail']);
            }
            if (isset($each['fields']['scene_info'])) {
                $request->setStoreInfo(...$each['fields']['scene_info']);
            }

            $this->assertEquals($each['parameters'], $request->makeParameters());
        }
    }
}