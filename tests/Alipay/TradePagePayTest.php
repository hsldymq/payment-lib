<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Test\Alipay;

use Archman\PaymentLib\Test\Alipay\Config\OpenAPIConfig;
use PHPUnit\Framework\TestCase;
use Archman\PaymentLib\Test\Config;
use Archman\PaymentLib\Alipay\TradePagePay;

class TradePagePayTest extends TestCase
{
    public function testMakingParameters()
    {
        $cases = Config::get('alipay', 'testCases', 'request', 'TradePagePay');
        foreach ($cases as $each) {
            $configData = Config::get('alipay', 'config', $each['appID']);
            $config = new OpenAPIConfig($configData, $each['signType']);
            $config->enableAESEncrypt($each['encrypted'] ?? false);

            $request = (new TradePagePay($config))
                ->setTotalAmount($each['fields']['amount'])
                ->setReturnURL($each['fields']['return_url'] ?? null)
                ->setNotifyURL($each['fields']['notify_url'] ?? null)
                ->setSubject($each['fields']['subject'])
                ->setOutTradeNo($each['fields']['out_trade_no'])
                ->setTimestamp(new \DateTime($each['fields']['timestamp']))
                ->setBody($each['fields']['body'] ?? null)
                ->setTimeoutExpress($each['fields']['timeout_express'] ?? null);

            $this->assertEquals($each['parameters'], $request->makeParameters());
        }
    }
}