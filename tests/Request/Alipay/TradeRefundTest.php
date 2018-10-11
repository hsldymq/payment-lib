<?php

namespace Archman\PaymentLib\Test\Request\Alipay;

use Archman\PaymentLib\Request\Alipay\TradeRefund;
use PHPUnit\Framework\TestCase;
use Archman\PaymentLib\Test\Config;
use Archman\PaymentLib\Test\Config\AlipayConfig;

class TradeRefundTest extends TestCase
{
    public function testMakingParameters()
    {
        $cases = Config::get('alipay', 'testCases', 'request', 'TradeRefund');
        foreach ($cases as $each) {
            $configData = Config::get('alipay', 'config', $each['appID']);
            $config = new AlipayConfig($configData);
            $config->setOpenAPIDefaultSignType($each['signType']);

            $request = (new TradeRefund($config))
                ->setOutTradeNo($each['fields']['out_trade_no'])
                ->setTimestamp(new \DateTime($each['fields']['timestamp']))
                ->setRefundAmount($each['fields']['refund_amount'])
                ->setRefundReason($each['fields']['refund_reason'])
                ->encrypt($each['encrypted']);

            $this->assertEquals($each['parameters'], $request->makeParameters());
        }
    }
}