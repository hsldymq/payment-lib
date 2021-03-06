<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Test\Alipay;

use Archman\PaymentLib\Alipay\TradeFastPayRefundQuery;
use Archman\PaymentLib\Test\Alipay\Config\ConfigLoader;
use PHPUnit\Framework\TestCase;
use Archman\PaymentLib\Test\Config;

class TradeFastPayRefundQueryTest extends TestCase
{
    public function testMakingParameters()
    {
        $cases = Config::get('alipay', 'requestDataCases', 'TradeFastPayRefundQuery');
        foreach ($cases as $each) {
            $config = ConfigLoader::loadConfig($each['configName'], $each['aesEnabled'], $each['certEnabled']);

            $request = (new TradeFastPayRefundQuery($config))
                ->setTradeNo($each['fields']['trade_no'] ?? null)
                ->setOutTradeNo($each['fields']['out_trade_no'] ?? null)
                ->setOutRequestNo($each['fields']['out_request_no'])
                ->setTimestamp(new \DateTime($each['fields']['timestamp']));

            $this->assertEquals($each['parameters'], $request->makeParameters());
        }
    }
}