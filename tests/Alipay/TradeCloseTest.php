<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Test\Alipay;

use Archman\PaymentLib\Alipay\TradeClose;
use Archman\PaymentLib\Test\Alipay\Config\ConfigLoader;
use PHPUnit\Framework\TestCase;
use Archman\PaymentLib\Test\Config;

class TradeCloseTest extends TestCase
{
    public function testMakingParameters()
    {
        $cases = Config::get('alipay', 'requestDataCases', 'TradeClose');
        foreach ($cases as $each) {
            $config = ConfigLoader::loadConfig($each['configName'], $each['aesEnabled'], $each['certEnabled']);

            $request = (new TradeClose($config))
                ->setOutTradeNo($each['fields']['out_trade_no'])
                ->setTimestamp(new \DateTime($each['fields']['timestamp']));

            $this->assertEquals($each['parameters'], $request->makeParameters());
        }
    }
}