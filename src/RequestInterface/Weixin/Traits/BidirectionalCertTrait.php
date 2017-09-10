<?php
namespace Utils\PaymentVendor\RequestInterface\Weixin\Traits;

use Utils\PaymentVendor\ConfigManager\WeixinConfig;

/**
 * @property WeixinConfig $config
 */
trait BidirectionalCertTrait
{
    public function prepareCert(&$root_ca_file, &$ssl_key_path, &$ssl_password, &$client_cert_path, &$client_cert_password)
    {
        $root_ca_file = $this->config->getRootCAFile();
        $ssl_key_path = $this->config->getSSLKeyFile();
        $client_cert_path = $this->config->getSSLCertFile();
    }
}