<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Test\Alipay;

use Archman\PaymentLib\Alipay\TradeQuery;
use Archman\PaymentLib\Test\Alipay\Config\OpenAPIConfig;
use Archman\PaymentLib\Test\Config;
use PHPUnit\Framework\TestCase;

class TradeQueryTest extends TestCase
{
    public function testMakingParameters()
    {
        $cases = Config::get('alipay', 'testCases', 'request', 'TradeQuery');
        foreach ($cases as $each) {
            $configData = Config::get('alipay', 'config', $each['appID']);
            $config = new OpenAPIConfig($configData, $each['signType']);
            $config->enableAESEncrypt($each['encrypted']);

            $request = (new TradeQuery($config))
                ->setTimestamp(new \DateTime($each['fields']['timestamp']))
                ->setOutTradeNo($each['fields']['out_trade_no'] ?? null)
                ->setTradeNo($each['fields']['trade_no'] ?? null);

            $this->assertEquals($each['parameters'], $request->makeParameters());
        }
    }
}