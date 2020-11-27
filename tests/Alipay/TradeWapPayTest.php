<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Test\Alipay;

use Archman\PaymentLib\Alipay\TradeWapPay;
use Archman\PaymentLib\Test\Alipay\Config\OpenAPIConfig;
use PHPUnit\Framework\TestCase;
use Archman\PaymentLib\Test\Config;

class TradeWapPayTest extends TestCase
{
    public function testMakingParameters()
    {
        $cases = Config::get('alipay', 'testCases', 'request', 'TradeWapPay');
        foreach ($cases as $each) {
            $configData = Config::get('alipay', 'config', $each['appID']);
            $config = new OpenAPIConfig($configData, $each['signType']);

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
                ->setStoreID($each['fields']['store_id'] ?? null)
                ->setQuitURL($each['fields']['quit_url'] ?? null);

            $this->assertEquals($each['parameters'], $request->makeParameters());
            $this->assertEquals($each['html'], $request->makeFormHTML($each['htmlAutoSubmit'], $each['htmlFormID']));
        }
    }
}