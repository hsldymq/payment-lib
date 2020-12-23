<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Test\Alipay;

use Archman\PaymentLib\Test\Alipay\Config\ConfigLoader;
use PHPUnit\Framework\TestCase;
use Archman\PaymentLib\Test\Config;
use Archman\PaymentLib\Alipay\FundTransOrderQuery;

class FundTransOrderQueryTest extends TestCase
{
    public function testMakingOrderQueryParameters()
    {
        $cases = Config::get('alipay', 'requestDataCases', 'FundTransOrderQuery');
        foreach ($cases as $each) {
            $config = ConfigLoader::loadConfig($each['configName'], $each['aesEnabled'], $each['certEnabled']);

            $request = (new FundTransOrderQuery($config))
                ->setTimestamp(new \DateTime($each['fields']['timestamp']))
                ->setOrderID($each['fields']['order_id'] ?? null)
                ->setOutBizNo($each['fields']['out_biz_no'] ?? null);

            $this->assertEquals($each['parameters'], $request->makeParameters());
        }
    }
}