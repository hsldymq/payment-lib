<?php

namespace Archman\PaymentLib\Test\Request\MyCard;

use Archman\PaymentLib\Request\MyCard\AuthGlobal;
use Archman\PaymentLib\Test\Config\MyCardConfig;
use PHPUnit\Framework\TestCase;

class AuthGlobalTest extends TestCase
{
    public function testMakeParameters()
    {
        $config = new MyCardConfig();

        $request1 = new AuthGlobal($config);
        $request1->setAmount(100)
            ->setFacTradeSeq('FacTradeSeq')
            ->setTradeType('1')
            ->setServerID('ServerId')
            ->setCustomerID('CustomerId')
            ->setPaymentType('asdf')
            ->setItemCode('ItemCode')
            ->setProductName('ProductName')
            ->setSandBoxMode(false)
            ->setCurrency('RMB')
            ->setFacReturnURL('http://www.pay.com');
        $parameters = $request1->makeParameters();
        $this->assertEquals([
            'FacServiceId' => 'test_service_id',
            'FacTradeSeq' => 'FacTradeSeq',
            'TradeType' => '1',
            'ServerId' => 'ServerId',
            'CustomerId' => 'CustomerId',
            'PaymentType' => 'asdf',
            'ItemCode' => 'ItemCode',
            'ProductName' => 'ProductName',
            'Amount' => '1.00',
            'Currency' => 'RMB',
            'SandBoxMode' => 'false',
            'FacReturnURL' => 'http://www.pay.com',
            'Hash' => '0d277abba6832d980cef06d71fb86f7d2ca95deb02ec197a766b7239059c8806',
        ], $parameters);



        $request2 = new AuthGlobal($config);
        $request2->setAmount(200)
            ->setFacTradeSeq('FacTradeSeq')
            ->setTradeType('1')
            ->setServerID('ServerId')
            ->setCustomerID('CustomerId')
            ->setProductName('ProductName')
            ->setSandBoxMode(true)
            ->setCurrency('RMB')
            ->setFacReturnURL('http://www.pay.com/return');
        $parameters = $request2->makeParameters();
        $this->assertEquals([
            'FacServiceId' => 'test_service_id',
            'FacTradeSeq' => 'FacTradeSeq',
            'TradeType' => '1',
            'ServerId' => 'ServerId',
            'CustomerId' => 'CustomerId',
            'ProductName' => 'ProductName',
            'Amount' => '2.00',
            'Currency' => 'RMB',
            'SandBoxMode' => 'true',
            'FacReturnURL' => 'http://www.pay.com/return',
            'Hash' => '5ace51546b6791fe4a2d44059e81f038a4d411c5f8b8ca380fcb4d9fc5cb60d9',
        ], $parameters);
    }
}