<?php
namespace Archman\PaymentLib\Test\Request\Alipay;

use PHPUnit\Framework\TestCase;
use Archman\PaymentLib\Test\Config;
use Archman\PaymentLib\Test\Config\AlipayConfig;
use Archman\PaymentLib\Request\Alipay\FundTransToAccountTransfer;
use Archman\PaymentLib\Request\Alipay\FundTransOrderQuery;

class FundTransOrderQueryTest extends TestCase
{
    public function testMakingOrderQueryParameters()
    {
        $cases = Config::get('alipay', 'testCases', 'request', 'FundTransOrderQuery');
        foreach ($cases as $each) {
            $configData = Config::get('alipay', 'config', $each['appID']);
            $config = new AlipayConfig($configData);
            $config->setOpenAPIDefaultSignType($each['signType']);

            $request = (new FundTransOrderQuery($config))
                ->encrypt($each['encrypted'])
                ->setTimestamp(new \DateTime($each['fields']['timestamp']))
                ->setOrderID($each['fields']['order_id'] ?? null)
                ->setOutBizNo($each['fields']['out_biz_no'] ?? null);

            $this->assertEquals($each['parameters'], $request->makeParameters());
        }
    }
}