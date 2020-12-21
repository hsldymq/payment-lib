<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Test\Alipay;

use Archman\PaymentLib\Alipay\TradeCancel;
use Archman\PaymentLib\Test\Alipay\Config\OpenAPIConfig;
use PHPUnit\Framework\TestCase;
use Archman\PaymentLib\Test\Config;

class TradeCancelTest extends TestCase
{
    public function testMakingParameters()
    {
        $cases = Config::get('alipay', 'testCases', 'request', 'TradeCancel');
        foreach ($cases as $each) {
            $configData = Config::get('alipay', 'config', $each['configName']);
            $config = new OpenAPIConfig($configData, $each['signType']);
            $config->enableAESEncrypt($each['encrypted'] ?? false);

            $request = (new TradeCancel($config))
                ->setOutTradeNo($each['fields']['out_trade_no'])
                ->setTimestamp(new \DateTime($each['fields']['timestamp']));

            $this->assertEquals($each['parameters'], $request->makeParameters());
        }
    }
}