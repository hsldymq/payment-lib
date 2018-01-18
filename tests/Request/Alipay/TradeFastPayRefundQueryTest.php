<?php
namespace Archman\PaymentLib\Test\Request\Alipay;

use Archman\PaymentLib\Request\Alipay\TradeFastPayRefundQuery;
use PHPUnit\Framework\TestCase;
use Archman\PaymentLib\Test\Config;
use Archman\PaymentLib\Test\Config\AlipayConfig;

class TradeFastPayRefundQueryTest extends TestCase
{
    public function testMakingParameters()
    {
        $cases = Config::get('alipay', 'testCases', 'request', 'TradeFastPayRefundQuery');
        foreach ($cases as $each) {
            $configData = Config::get('alipay', 'config', $each['appID']);
            $config = new AlipayConfig($configData);
            $config->setOpenAPIDefaultSignType($each['signType']);

            $request = (new TradeFastPayRefundQuery($config))
                ->encrypt($each['encrypted'])
                ->setTradeNo($each['fields']['trade_no'] ?? null)
                ->setOutTradeNo($each['fields']['out_trade_no'] ?? null)
                ->setOutRequestNo($each['fields']['out_request_no'])
                ->setTimestamp(new \DateTime($each['fields']['timestamp']));

            $this->assertEquals($each['parameters'], $request->makeParameters());
        }
    }
}