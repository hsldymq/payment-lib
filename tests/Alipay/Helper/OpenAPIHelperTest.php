<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Test\Alipay\Helper;

use Archman\PaymentLib\Alipay\Helper\OpenAPIHelper;
use PHPUnit\Framework\TestCase;

class OpenAPIHelperTest extends TestCase
{
    public function testGetResponseContent()
    {
        $cases = [
            [
                'body' => '{"response1":{"f1":"v1","f2":"v2"},"sign":"testSign"}',
                'fieldName' => 'response1',
                'expect' => '{"f1":"v1","f2":"v2"}',
            ],
            [
                'body' => '{   "response2"  :    {"f1":"v1","f2":"v2"}  ,   "sign"  :  "testSign"}',
                'fieldName' => 'response2',
                'expect' => '{"f1":"v1","f2":"v2"}',
            ],
            [
                'body' => '{"sign":"testSign" , "response3"  :  {"f1":"v1","f2":"v2"}   }',
                'fieldName' => 'response3',
                'expect' => '{"f1":"v1","f2":"v2"}',
            ],
            [
                'body' => '{  "addition": 123  , "response4":{"f1":"v1","f2":"v2"},  "sign":"testSign"}',
                'fieldName' => 'response4',
                'expect' => '{"f1":"v1","f2":"v2"}',
            ],
            [
                'body' => '{  "addition": 123  , "response5": "asdfakkasf3124/324=="  ,  "sign":"testSign"}',
                'fieldName' => 'response5',
                'expect' => '"asdfakkasf3124/324=="',
            ],
            [
                'body' => ' { "response5"  :   "asdfakkasf3124/bc=="    }  ',
                'fieldName' => 'response5',
                'expect' => '"asdfakkasf3124/bc=="',
            ],
            [
                'body' => '{"alipay_trade_query_response":{"code":"10000","msg":"Success","buyer_logon_id":"139******90","buyer_pay_amount":"0.00","buyer_user_id":"xxx","invoice_amount":"0.00","out_trade_no":"yyy","point_amount":"0.00","receipt_amount":"0.00","send_pay_date":"2020-11-20 16:09:28","total_amount":"388.00","trade_no":"xxx","trade_status":"TRADE_SUCCESS"},"sign":"XLZVz81Xp74ZgWDaG3RjXHY9acxsT1xnYm87sTstbg6A/usCeuQtUUlkJJynPGMnBO5b3kaPAHCHo+LApKuOs1Rc6cCqL5Ee+mf/1NQHEvaX2FUsLttnwkBX2YOzNq+HbdibqWxh29YMvmXW3zEbXAHO5mshsERFn94rcHLB6dkXxmo+dT6cAy4lZEa/5ZA1cDHZxHaMrkZ4AKYrQpX9kVfV7bKVw2uxlRoihm/tPPa/4GYQVJgr1lAmgirQQ28/9dNbC5GZcyfbnLaRiwgnWs0E91K6C2Xp1Y9SMus4QUi41m2zYyyIIFda3FDrB+IUw5Nq6Wse+UCl1WjT8lV3Pw=="}',
                'fieldName' => 'alipay_trade_query_response',
                'expect' => '{"code":"10000","msg":"Success","buyer_logon_id":"139******90","buyer_pay_amount":"0.00","buyer_user_id":"xxx","invoice_amount":"0.00","out_trade_no":"yyy","point_amount":"0.00","receipt_amount":"0.00","send_pay_date":"2020-11-20 16:09:28","total_amount":"388.00","trade_no":"xxx","trade_status":"TRADE_SUCCESS"}',
            ],
            [
                'body' => '{"abc": 123, "ddd": "kkk" , "alipay_trade_query_response"   :   {"code":"10000","msg":"Success","buyer_logon_id":"139******90","buyer_pay_amount":"0.00","buyer_user_id":"xxx","invoice_amount":"0.00","out_trade_no":"yyy","point_amount":"0.00","receipt_amount":"0.00","send_pay_date":"2020-11-20 16:09:28","total_amount":"388.00","trade_no":"xxx","trade_status":"TRADE_SUCCESS"}    ,"sign":"XLZVz81Xp74ZgWDaG3RjXHY9acxsT1xnYm87sTstbg6A/usCeuQtUUlkJJynPGMnBO5b3kaPAHCHo+LApKuOs1Rc6cCqL5Ee+mf/1NQHEvaX2FUsLttnwkBX2YOzNq+HbdibqWxh29YMvmXW3zEbXAHO5mshsERFn94rcHLB6dkXxmo+dT6cAy4lZEa/5ZA1cDHZxHaMrkZ4AKYrQpX9kVfV7bKVw2uxlRoihm/tPPa/4GYQVJgr1lAmgirQQ28/9dNbC5GZcyfbnLaRiwgnWs0E91K6C2Xp1Y9SMus4QUi41m2zYyyIIFda3FDrB+IUw5Nq6Wse+UCl1WjT8lV3Pw=="}',
                'fieldName' => 'alipay_trade_query_response',
                'expect' => '{"code":"10000","msg":"Success","buyer_logon_id":"139******90","buyer_pay_amount":"0.00","buyer_user_id":"xxx","invoice_amount":"0.00","out_trade_no":"yyy","point_amount":"0.00","receipt_amount":"0.00","send_pay_date":"2020-11-20 16:09:28","total_amount":"388.00","trade_no":"xxx","trade_status":"TRADE_SUCCESS"}',
            ],
        ];


        foreach ($cases as $each) {
            $content = OpenAPIHelper::getResponseContent($each['body'], $each['fieldName']);
            $this->assertEquals($each['expect'], $content);
        }
    }
}