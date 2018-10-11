<?php

namespace Archman\PaymentLib\Test\Request\Alipay;

use Archman\PaymentLib\Request\Alipay\TradeCancel;
use PHPUnit\Framework\TestCase;
use Archman\PaymentLib\Test\Config;
use Archman\PaymentLib\Test\Config\AlipayConfig;

class TradeCancelTest extends TestCase
{
    public function testMakingParameters()
    {
        $cases = Config::get('alipay', 'testCases', 'request', 'TradeCancel');
        foreach ($cases as $each) {
            $configData = Config::get('alipay', 'config', $each['appID']);
            $config = new AlipayConfig($configData);
            $config->setOpenAPIDefaultSignType($each['signType']);

            $request = (new TradeCancel($config))
                ->setOutTradeNo($each['fields']['out_trade_no'])
                ->setTimestamp(new \DateTime($each['fields']['timestamp']))
                ->encrypt($each['encrypted']);

            $this->assertEquals($each['parameters'], $request->makeParameters());
        }
    }
}