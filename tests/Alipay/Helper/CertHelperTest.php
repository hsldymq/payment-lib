<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Test\Alipay\Helper;

use Archman\PaymentLib\Alipay\Helper\CertHelper;
use PHPUnit\Framework\TestCase;

class CertHelperTest extends TestCase
{
    public function testGetIssuerDN()
    {
        $expect = "CN=Ant Financial Certification Authority Class 1 R1,OU=Certification Authority,O=Ant Financial,C=CN";

        $dn = CertHelper::getIssuerDN([
            "C" => "CN",
            "O" => "Ant Financial",
            "OU" => "Certification Authority",
            "CN" => "Ant Financial Certification Authority Class 1 R1",
        ]);
        $this->assertEquals($expect, $dn);

        $dn = CertHelper::getIssuerDN([
            "CN" => "Ant Financial Certification Authority Class 1 R1",
            "OU" => "Certification Authority",
            "O" => "Ant Financial",
            "C" => "CN",
        ]);
        $this->assertEquals($expect, $dn);
    }
}