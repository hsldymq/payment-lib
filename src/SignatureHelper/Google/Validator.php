<?php
namespace Archman\PaymentLib\SignatureHelper\Google;

use Archman\PaymentLib\ConfigManager\GoogleConfigInterface;

class Validator
{
    private $config;

    public function __construct(GoogleConfigInterface $config)
    {
        $this->config = $config;
    }

    public function validate(string $data, string $signature): bool
    {
        $resource = openssl_get_publickey($this->config->getLicenseKey());
        if (!$resource) {
            throw new \Exception("Unable To Get Public Key");
        }

        $isCorrect = openssl_verify($data, base64_decode($signature), $resource) === 1;
        openssl_free_key($resource);

        return $isCorrect;
    }
}