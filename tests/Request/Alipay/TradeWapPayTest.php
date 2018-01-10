<?php
namespace Archman\PaymentLib\Test\Request\Alipay;

use PHPUnit\Framework\TestCase;
use Archman\PaymentLib\Test\Config;
use Archman\PaymentLib\Test\Config\AlipayConfig;
use Archman\PaymentLib\Request\Alipay\TradeWapPay;

class TradeWapPayTest extends TestCase
{
    public function testMakingParameters()
    {
        $cases = Config::get('alipay', 'testCases', 'request', 'TradeWapPay');
        foreach ($cases as $each) {
            $configData = Config::get('alipay', 'config', $each['appID']);
            $config = new AlipayConfig($configData);

            $request = (new TradeWapPay($config))
                ->setReturnURL($each['fields']['return_url'] ?? null)
                ->setNotifyURL($each['fields']['notify_url'] ?? null)
                ->setTimestamp(new \DateTime($each['fields']['timestamp']))
                ->setBody($each['fields']['body'])
                ->setSubject($each['fields']['subject'])
                ->setTotalAmount($each['fields']['amount'])
                ->setOutTradeNo($each['fields']['out_trade_no'])
                ->setTimeoutExpress($each['fields']['timeout_express'] ?? null)
                ->setGoodsType($each['fields']['goods_type'] ?? null)
                ->setPassbackParams($each['fields']['passback_params'] ?? null)
                ->setQuitUrl($each['fields']['quit_url'] ?? null);

            $this->assertEquals($each['parameters'], $request->makeParameters());
        }
    }
}