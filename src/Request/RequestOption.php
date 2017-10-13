<?php
namespace Archman\PaymentLib\Request;

class RequestOption implements RequestOptionInterface
{
    private $rootCAFilePath = null;

    private $sslKeyFilePath = null;

    private $sslPassword = null;

    private $clientCertFilePath = null;

    private $clientCertPassword = null;

    public function getRootCAFilePath(): ?string
    {
        return $this->rootCAFilePath;
    }

    public function getSSLKeyFilePath(): ?string
    {
        return $this->sslKeyFilePath;
    }

    public function getSSLPassword(): ?string
    {
        return $this->sslPassword;
    }

    public function getClientCertFilePath(): ?string
    {
        return $this->clientCertFilePath;
    }

    public function getClientCertPassword(): ?string
    {
        return $this->clientCertPassword;
    }

    public function setRootCAFilePath(?string $path): self
    {
        $this->rootCAFilePath = $path;

        return $this;
    }

    public function setSSLKeyFilePath(?string $path): self
    {
        $this->sslKeyFilePath = $path;

        return $this;
    }

    public function setSSLPassword(?string $password): self
    {
        $this->sslPassword = $password;

        return $this;
    }

    public function setClientCertFilePath(?string $path): self
    {
        $this->clientCertFilePath = $path;

        return $this;
    }

    public function setClientCertPassword(?string $password): self
    {
        $this->clientCertPassword = $password;

        return $this;
    }
}