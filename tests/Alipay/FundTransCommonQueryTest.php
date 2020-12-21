<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Test\Alipay;

use Archman\PaymentLib\Alipay\FundTransCommonQuery;
use Archman\PaymentLib\Test\Alipay\Config\OpenAPIConfig;
use PHPUnit\Framework\TestCase;
use Archman\PaymentLib\Test\Config;

class FundTransCommonQueryTest extends TestCase
{
    public function testMakingOrderQueryParameters()
    {
        $cases = Config::get('alipay', 'testCases', 'request', 'FundTransCommonQuery');
        foreach ($cases as $each) {
            $configData = Config::get('alipay', 'config', $each['configName']);
            $config = new OpenAPIConfig($configData, $each['signType'], $each['certEnabled'] ?? false);
            $config->enableAESEncrypt($each['encrypted'] ?? false);

            $request = (new FundTransCommonQuery($config))
                ->setTimestamp(new \DateTime($each['fields']['timestamp']))
                ->setOutBizNo($each['fields']['out_biz_no'])
                ->setProductCode($each['fields']['product_code'])
                ->setBizScene($each['fields']['biz_scene']);

            $this->assertEquals($each['parameters'], $request->makeParameters());
        }
    }
}