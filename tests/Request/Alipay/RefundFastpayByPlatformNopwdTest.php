<?php
namespace Archman\PaymentLib\Test\Request\Alipay;

use Archman\PaymentLib\Test\Config;
use Archman\PaymentLib\Test\Config\AlipayConfig;
use Archman\PaymentLib\Request\Alipay\RefundFastpayByPlatformNopwd;

class RefundFastpayByPlatformNopwdTest extends \PHPUnit\Framework\TestCase
{
    public function testMakingParameters()
    {
        $cases = Config::get('alipay', 'testCases', 'request', 'RefundFastpayByPlatformNopwd');
        foreach ($cases as $each) {
            $configData = Config::get('alipay', 'config', $each['appID']);
            $config = new AlipayConfig($configData);

            $request = (new RefundFastpayByPlatformNopwd($config))
                ->setNotifyURL($each['fields']['notify_url'])
                ->setUseFreezeAmount($each['fields']['use_freeze_amount'])
                ->setSerialNumber($each['fields']['serial_number'])
                ->setRefundDate(new DateTime($each['fields']['refund_date']));
            foreach ($each['fields']['detail_data'] as $d) {
                $request->addDetailData(...$d);
            }

            $this->assertEquals($each['parameters'], $request->makeParameters());
        }
    }
}