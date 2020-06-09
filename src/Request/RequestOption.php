<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Request;

class RequestOption implements RequestOptionInterface
{
    private $rootCAFilePath = null;

    private $sslKeyFilePath = null;

    private $sslKeyPassword = null;

    private $sslCertFilePath = null;

    private $sslCertPassword = null;

    public function getRootCAFilePath(): ?string
    {
        return $this->rootCAFilePath;
    }

    public function getSSLKeyFilePath(): ?string
    {
        return $this->sslKeyFilePath;
    }

    public function getSSLKeyPassword(): ?string
    {
        return $this->sslKeyPassword;
    }

    public function getSSLCertFilePath(): ?string
    {
        return $this->sslCertFilePath;
    }

    public function getSSLCertPassword(): ?string
    {
        return $this->sslCertPassword;
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

    public function setSSLKeyPassword(?string $password): self
    {
        $this->sslKeyPassword = $password;

        return $this;
    }

    public function setSSLCertFilePath(?string $path): self
    {
        $this->sslCertFilePath = $path;

        return $this;
    }

    public function setSSLCertPassword(?string $password): self
    {
        $this->sslCertPassword = $password;

        return $this;
    }
}