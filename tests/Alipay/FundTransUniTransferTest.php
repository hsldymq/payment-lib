<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Test\Alipay;

use Archman\PaymentLib\Alipay\FundTransUniTransfer;
use Archman\PaymentLib\Test\Alipay\Config\OpenAPIConfig;
use PHPUnit\Framework\TestCase;
use Archman\PaymentLib\Test\Config;

class FundTransUniTransferTest extends TestCase
{
    public function testMakingOrderQueryParameters()
    {
        $cases = Config::get('alipay', 'testCases', 'request', 'FundTransUniTransfer');
        foreach ($cases as $each) {
            $configData = Config::get('alipay', 'config', $each['configName']);
            $config = new OpenAPIConfig($configData, $each['signType'], $each['certEnabled'] ?? false);
            $config->enableAESEncrypt($each['encrypted'] ?? false);

            $request = (new FundTransUniTransfer($config))
                ->setTimestamp(new \DateTime($each['fields']['timestamp']))
                ->setOutBizNo($each['fields']['out_biz_no'])
                ->setProductCode($each['fields']['product_code'])
                ->setTransAmount($each['fields']['trans_amount'])
                ->setBizScene($each['fields']['biz_scene'])
                ->setPayeeInfo($each['fields']['payee_info']);

            $this->assertEquals($each['parameters'], $request->makeParameters());
        }
    }
}