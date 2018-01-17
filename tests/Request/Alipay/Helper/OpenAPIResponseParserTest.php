<?php
namespace Archman\PaymentLib\Test\Request\Alipay\Helper;

use Archman\PaymentLib\Request\Alipay\Helper\OpenAPIResponseParser;
use PHPUnit\Framework\TestCase;

class OpenAPIResponseParserTest extends TestCase
{
    private $contentCases = [
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
    ];

    public function testGetResponseContent()
    {
        foreach ($this->contentCases as $each) {
            $content = OpenAPIResponseParser::getResponseContent($each['body'], $each['fieldName']);
            $this->assertEquals($each['expect'], $content);
        }
    }
}