<?php
namespace Archman\PaymentLib\Test\Response\Alipay\Sync;

use Archman\PaymentLib\Request\Alipay\Helper\Encryption;
use Archman\PaymentLib\Request\Alipay\Helper\OpenAPIResponseParser;
use Archman\PaymentLib\Request\Alipay\TradeFastPayRefundQuery;
use Archman\PaymentLib\Request\DataConverter;
use Archman\PaymentLib\SignatureHelper\Alipay\Validator;
use Archman\PaymentLib\Test\Config;
use PHPUnit\Framework\TestCase;
use Archman\PaymentLib\Test\Config\AlipayConfig;

class TradeFastPayRefundQueryTest extends TestCase
{
    public function testSignatureValidation()
    {
        $cases = Config::get('alipay', 'testCases', 'syncResponse', 'TradeFastPayRefundQuery');
        foreach ($cases as $each) {
            $configData = Config::get('alipay', 'config', $each['appID']);
            $config = new AlipayConfig($configData);

            $data = DataConverter::jsonToArray($each['body']);
            ['signName' => $signName, 'responseName' => $responseName] = ResponseHelper::getResponseFieldName(TradeFastPayRefundQuery::class);

            $content = OpenAPIResponseParser::getResponseContent($each['body'], $responseName);
            $result = (new Validator($config))->validateOpenAPIResponseSign($data[$signName], $each['signType'], $content);

            $this->assertTrue($result);
        }
    }

    public function testDecryption()
    {
        $cases = Config::get('alipay', 'testCases', 'syncResponse', 'TradeFastPayRefundQuery');
        foreach ($cases as $each) {
            if (!$each['encrypted']) {
                continue;
            }

            $configData = Config::get('alipay', 'config', $each['appID']);
            $config = new AlipayConfig($configData);

            ['responseName' => $responseName] = ResponseHelper::getResponseFieldName(TradeFastPayRefundQuery::class);
            $content = OpenAPIResponseParser::getResponseContent($each['body'], $responseName);
            $data = json_decode(Encryption::decrypt($content, $config->getOpenAPIEncryptionKey()), true);

            $this->assertArrayHasKey('code', $data);
        }
    }
}