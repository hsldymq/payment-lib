<?php
namespace Utils\PaymentVendor\RequestInterface;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface RequestableInterface
{
    /**
     * @return RequestInterface
     */
    public function prepareRequest(): RequestInterface;

    /**
     * @param string $root_ca_file
     * @param string $ssl_key_path
     * @param string $ssl_password
     * @param string $client_cert_path
     * @param string $client_cert_password
     * @return void
     */
    public function prepareCert(&$root_ca_file, &$ssl_key_path, &$ssl_password, &$client_cert_path, &$client_cert_password);

    /**
     * @param ResponseInterface $response
     * @return array
     */
    public function handleResponse(ResponseInterface $response): array;
}
