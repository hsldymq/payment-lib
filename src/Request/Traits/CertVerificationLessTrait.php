<?php
namespace Utils\PaymentVendor\RequestInterface\Traits;

trait CertVerificationLessTrait
{
    public function prepareCert(&$root_ca_file, &$ssl_key_file, &$ssl_password, &$client_cert_file, &$client_cert_password)
    {
        $root_ca_file = false;
    }
}