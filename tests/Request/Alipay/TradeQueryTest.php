<?php
namespace Archman\PaymentLib\Test\Request\Alipay;

use Archman\PaymentLib\Request\Alipay\TradeQuery;
use Archman\PaymentLib\Test\Config;
use PHPUnit\Framework\TestCase;
use Archman\PaymentLib\Test\Config\AlipayConfig;

class TradeQueryTest extends TestCase
{
    public function testMakingParameters()
    {
        $cases = Config::get('alipay', 'testCases', 'request', 'TradeQuery');
        foreach ($cases as $each) {
            $configData = Config::get('alipay', 'config', $each['appID']);
            $config = new AlipayConfig($configData);
            $config->setOpenAPIDefaultSignType($each['signType']);

            $request = (new TradeQuery($config))
                ->encrypt($each['encrypted'])
                ->setTimestamp(new \DateTime($each['fields']['timestamp']))
                ->setOutTradeNo($each['fields']['out_trade_no'] ?? null)
                ->setTradeNo($each['fields']['trade_no'] ?? null);

            $this->assertEquals($each['parameters'], $request->makeParameters());
        }
    }
}