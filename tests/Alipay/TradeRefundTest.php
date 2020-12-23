<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Test\Alipay;

use Archman\PaymentLib\Alipay\TradeRefund;
use Archman\PaymentLib\Test\Alipay\Config\ConfigLoader;
use PHPUnit\Framework\TestCase;
use Archman\PaymentLib\Test\Config;

class TradeRefundTest extends TestCase
{
    public function testMakingParameters()
    {
        $cases = Config::get('alipay', 'requestDataCases', 'TradeRefund');
        foreach ($cases as $each) {
            $config = ConfigLoader::loadConfig($each['configName'], $each['aesEnabled'], $each['certEnabled']);

            $request = (new TradeRefund($config))
                ->setOutTradeNo($each['fields']['out_trade_no'])
                ->setTimestamp(new \DateTime($each['fields']['timestamp']))
                ->setRefundAmount($each['fields']['refund_amount'])
                ->setRefundReason($each['fields']['refund_reason']);

            $this->assertEquals($each['parameters'], $request->makeParameters());
        }
    }
}