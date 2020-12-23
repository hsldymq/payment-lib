<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Test\Alipay;

use Archman\PaymentLib\Test\Alipay\Config\ConfigLoader;
use PHPUnit\Framework\TestCase;
use Archman\PaymentLib\Test\Config;
use Archman\PaymentLib\Alipay\TradePagePay;

class TradePagePayTest extends TestCase
{
    public function testMakingParameters()
    {
        $cases = Config::get('alipay', 'requestDataCases', 'TradePagePay');
        foreach ($cases as $each) {
            $config = ConfigLoader::loadConfig($each['configName'], $each['aesEnabled'], $each['certEnabled']);

            $request = (new TradePagePay($config))
                ->setTotalAmount($each['fields']['total_amount'])
                ->setReturnURL($each['fields']['return_url'] ?? null)
                ->setNotifyURL($each['fields']['notify_url'] ?? null)
                ->setSubject($each['fields']['subject'])
                ->setOutTradeNo($each['fields']['out_trade_no'])
                ->setTimestamp(new \DateTime($each['fields']['timestamp']))
                ->setBody($each['fields']['body'] ?? null)
                ->setTimeoutExpress($each['fields']['timeout_express'] ?? null);

            if (isset($each['html'])) {
                $this->assertEquals($each['html'], $request->makeFormHTML($each['htmlAutoSubmit'], $each['htmlFormID']));
            } else {
                $this->assertEquals($each['parameters'], $request->makeParameters());
            }
        }
    }
}