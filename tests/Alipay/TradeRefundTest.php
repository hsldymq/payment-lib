<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Test\Alipay;

use Archman\PaymentLib\Alipay\TradeRefund;
use Archman\PaymentLib\Test\Alipay\Config\OpenAPIConfig;
use PHPUnit\Framework\TestCase;
use Archman\PaymentLib\Test\Config;

class TradeRefundTest extends TestCase
{
    public function testMakingParameters()
    {
        $cases = Config::get('alipay', 'testCases', 'request', 'TradeRefund');
        foreach ($cases as $each) {
            $configData = Config::get('alipay', 'config', $each['appID']);
            $config = new OpenAPIConfig($configData, $each['signType']);
            $config->enableAESEncrypt($each['encrypted'] ?? false);

            $request = (new TradeRefund($config))
                ->setOutTradeNo($each['fields']['out_trade_no'])
                ->setTimestamp(new \DateTime($each['fields']['timestamp']))
                ->setRefundAmount($each['fields']['refund_amount'])
                ->setRefundReason($each['fields']['refund_reason']);

            $this->assertEquals($each['parameters'], $request->makeParameters());
        }
    }
}