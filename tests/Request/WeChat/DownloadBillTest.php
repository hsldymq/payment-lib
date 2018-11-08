<?php

namespace Archman\PaymentLib\Test\Request\WeChat;

use Archman\PaymentLib\Request\WeChat\DownloadBill;
use PHPUnit\Framework\TestCase;
use Archman\PaymentLib\Test\Config;
use Archman\PaymentLib\Test\Config\WeChatConfig;

class DownloadBillTest extends TestCase
{
    public function testMakingParameters()
    {
        $cases = Config::get('wechat', 'testCases', 'request', 'DownloadBill');
        foreach ($cases as $each) {
            $configData = Config::get('wechat', 'config', $each['appID']);
            $config = new WeChatConfig($configData);

            $request = (new DownloadBill($config))
                ->setBillDate($each['fields']['bill_date'])
                ->setBillType($each['fields']['bill_type'])
                ->setTarType($each['fields']['tar_type'] ?? null)
                ->setNonceStr($each['fields']['nonce_str']);

            $this->assertEquals($each['parameters'], $request->makeParameters());
        }
    }
}