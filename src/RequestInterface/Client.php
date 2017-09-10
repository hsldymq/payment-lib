<?php
namespace Utils\PaymentVendor\RequestInterface;

class Client
{
    public static function sendRequest(RequestableInterface $interface): array
    {
        $request = $interface->prepareRequest();
        $config = self::prepareRequestConfig($interface);
        $client = new \GuzzleHttp\Client($config);
        $response = $client->send($request);

        return $interface->handleResponse($response);
    }

    private static function prepareRequestConfig(RequestableInterface $interface): array
    {
        $config = [];

        // 准备证书验证配置(根证书文件, SSL密钥文件, SSL密码, 客户端证书文件, 客户端证书密码)
        $root_ca_file = $ssl_key_file = $ssl_password = $client_cert_file = $client_cert_password = null;
        $interface->prepareCert($root_ca_file, $ssl_key_file, $ssl_password, $client_cert_file, $client_cert_password);
        if ($ssl_key_file && $ssl_password) {
            $config['ssl_key'] = [$ssl_key_file, $ssl_password];
        } else if ($ssl_key_file) {
            $config['ssl_key'] = $ssl_key_file;
        }

        if ($client_cert_file && $client_cert_password) {
            $config['cert'] = [$client_cert_file, $client_cert_password];
        } else if ($client_cert_file) {
            $config['cert'] = $client_cert_file;
        }

        !is_null($root_ca_file) && $config['verify'] = $root_ca_file;

        return $config;
    }
}