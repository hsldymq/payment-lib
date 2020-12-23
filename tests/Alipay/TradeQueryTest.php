<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Test\Alipay;

use Archman\PaymentLib\Alipay\TradeQuery;
use Archman\PaymentLib\Test\Alipay\Config\ConfigLoader;
use Archman\PaymentLib\Test\Config;
use PHPUnit\Framework\TestCase;

class TradeQueryTest extends TestCase
{
    public function testMakingParameters()
    {
        $cases = Config::get('alipay', 'requestDataCases', 'TradeQuery');
        foreach ($cases as $each) {
            $config = ConfigLoader::loadConfig($each['configName'], $each['aesEnabled'], $each['certEnabled']);

            $request = (new TradeQuery($config))
                ->setTimestamp(new \DateTime($each['fields']['timestamp']))
                ->setOutTradeNo($each['fields']['out_trade_no'] ?? null)
                ->setTradeNo($each['fields']['trade_no'] ?? null);

            $this->assertEquals($each['parameters'], $request->makeParameters());
        }
    }
}