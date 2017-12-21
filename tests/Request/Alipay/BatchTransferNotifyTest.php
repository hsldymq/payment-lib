<?php

use PHPUnit\Framework\TestCase;
use Archman\PaymentLib\Test\Config;
use Archman\PaymentLib\Test\Config\AlipayConfig;
use Archman\PaymentLib\Request\Alipay\BatchTransNotify;

class BatchTransferNotifyTest extends TestCase
{
    public function testSigning()
    {
        $cases = Config::get('alipay', 'testCases', 'request', 'BatchTransferNotify');
        foreach ($cases as $each) {
            $configData = Config::get('alipay', 'config', $each['appID']);
            $config = new AlipayConfig($configData);

            $request = (new BatchTransNotify($config))
                ->setAccountName($each['fields']['account_name'])
                ->setBatchNo($each['fields']['batch_no'])
                ->setEmail($each['fields']['email'])
                ->setNotifyURL($each['fields']['notify_url'])
                ->addDetailData(...$each['fields']['detail_data']);
            $url = $request->makeTransferUrl();

            $this->assertEquals($each['url'], $url);
        }
    }
}