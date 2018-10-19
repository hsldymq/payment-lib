<?php

namespace Archman\PaymentLib\Test\Request\MyCard;

use Archman\PaymentLib\Request\MyCard\AuthGlobal;
use Archman\PaymentLib\Test\Config;
use Archman\PaymentLib\Test\Config\MyCardConfig;
use PHPUnit\Framework\TestCase;

class AuthGlobalTest extends TestCase
{
    public function testMakeParameters()
    {
        $cases = Config::get('mycard', 'testCases', 'request', 'AuthGlobal');
        foreach ($cases as $each) {
            $configData = Config::get('mycard', 'config', $each['FacServiceID']);
            $config = new MyCardConfig($configData);

            $request = (new AuthGlobal($config))
                ->setFacTradeSeq($each['fields']['FacTradeSeq'])
                ->setTradeType($each['fields']['TradeType'])
                ->setCustomerID($each['fields']['CustomerId'])
                ->setPaymentType($each['fields']['PaymentType'])
                ->setItemCode($each['fields']['ItemCode'])
                ->setProductName($each['fields']['ProductName'])
                ->setAmount($each['fields']['Amount'])
                ->setCurrency($each['fields']['Currency'])
                ->setSandBoxMode($each['fields']['SandBoxMode'])
                ->setFacReturnURL($each['fields']['FacReturnURL']);

            $this->assertEquals($each['parameters'], $request->makeParameters());
        }
    }
}