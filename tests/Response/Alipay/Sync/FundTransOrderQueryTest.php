<?php
namespace Archman\PaymentLib\Test\Signature\Alipay\Sync;

use Archman\PaymentLib\Request\Alipay\Helper\Encryption;
use Archman\PaymentLib\Request\Alipay\Helper\OpenAPIResponseParser;
use Archman\PaymentLib\SignatureHelper\Alipay\Validator;
use Archman\PaymentLib\Test\Config;
use PHPUnit\Framework\TestCase;
use Archman\PaymentLib\Test\Config\AlipayConfig;

class FundTransOrderQueryTest extends TestCase
{
    public function testSignatureValidation()
    {
        $cases = Config::get('alipay', 'testCases', 'syncResponse', 'FundTransOrderQuery');
        foreach ($cases as $each) {
            $configData = Config::get('alipay', 'config', $each['appID']);
            $config = new AlipayConfig($configData);

            $content = OpenAPIResponseParser::getResponseContent($each['body'], $each['fieldName']);
            $result = (new Validator($config))->validateOpenAPIResponseSign($each['signature'], $each['signType'], $content);

            $this->assertTrue($result);
        }
    }

    public function testDecryption()
    {
        $cases = Config::get('alipay', 'testCases', 'syncResponse', 'FundTransOrderQuery');
        foreach ($cases as $each) {
            if (!$each['encrypted']) {
                continue;
            }

            $configData = Config::get('alipay', 'config', $each['appID']);
            $config = new AlipayConfig($configData);

            $content = OpenAPIResponseParser::getResponseContent($each['body'], $each['fieldName']);
            $data = json_decode(Encryption::decrypt($content, $config->getOpenAPIEncryptionKey()), true);

            $this->assertArrayHasKey('code', $data);
        }
    }
}