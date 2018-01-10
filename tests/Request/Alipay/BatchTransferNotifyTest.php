<?php
namespace Archman\PaymentLib\Test\Request\Alipay;

use PHPUnit\Framework\TestCase;
use Archman\PaymentLib\Test\Config;
use Archman\PaymentLib\Test\Config\AlipayConfig;
use Archman\PaymentLib\Request\Alipay\BatchTransNotify;

class BatchTransferNotifyTest extends TestCase
{
    public function testMakingURL()
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
                ->addDetailData(...$each['fields']['detail_data'])
                ->setPayDate(new \DateTime($each['fields']['pay_date']))
                ->setBuyerAccountName($each['fields']['buyer_account_name'] ?? null);
            foreach ($each['fields']['extend_param'] ?? [] as $params) {
                $request->addExtendParam(...$params);
            }

            $url = $request->makeTransferUrl();

            $this->assertEquals($each['url'], $url);
        }
    }
}