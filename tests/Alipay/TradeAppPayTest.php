<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Test\Alipay;

use Archman\PaymentLib\Test\Alipay\Config\ConfigLoader;
use PHPUnit\Framework\TestCase;
use Archman\PaymentLib\Test\Config;
use Archman\PaymentLib\Alipay\TradeAppPay;

class TradeAppPayTest extends TestCase
{
    public function testMakingParameters()
    {
        $cases = Config::get('alipay', 'requestDataCases', 'TradeAppPay');
        foreach ($cases as $each) {
            $config = ConfigLoader::loadConfig($each['configName'], $each['aesEnabled'], $each['certEnabled']);

            $request = (new TradeAppPay($config))
                ->setTotalAmount($each['fields']['amount'])
                ->setNotifyURL($each['fields']['notify_url'])
                ->setSubject($each['fields']['subject'])
                ->setOutTradeNo($each['fields']['out_trade_no'])
                ->setTimestamp(new \DateTime($each['fields']['timestamp']))
                ->setTimeoutExpress($each['fields']['timeout_express'] ?? null);

            $this->assertEquals($each['parameters'], $request->makeParameters());
        }
    }
}