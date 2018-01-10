<?php
namespace Archman\PaymentLib\Test\Request\Alipay;

use PHPUnit\Framework\TestCase;
use Archman\PaymentLib\Test\Config;
use Archman\PaymentLib\Test\Config\AlipayConfig;
use Archman\PaymentLib\Request\Alipay\TradeAppPay;

class TradeAppPayTest extends TestCase
{
    public function testMakingTransferParameters()
    {
        $cases = Config::get('alipay', 'testCases', 'request', 'TradeAppPay');
        foreach ($cases as $each) {
            $configData = Config::get('alipay', 'config', $each['appID']);
            $config = new AlipayConfig($configData);

            $request = (new TradeAppPay($config))
                ->setTotalAmount($each['fields']['amount'])
                ->setNotifyURL($each['fields']['notify_url'])
                ->setSubject($each['fields']['subject'])
                ->setOutTradeNo($each['fields']['out_trade_no'])
                ->setTimestamp(new DateTime($each['fields']['timestamp']))
                ->setTimeoutExpress($each['fields']['timeout_express'] ?? null);

            $this->assertEquals($each['parameters'], $request->makeParameters());
        }
    }
}