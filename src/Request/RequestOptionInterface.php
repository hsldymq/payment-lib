<?php
namespace Archman\PaymentLib\Request;

interface RequestOptionInterface
{
    public function getRootCAFilePath(): ?string;

    public function getSSLKeyFilePath(): ?string;
    public function getSSLKeyPassword(): ?string;

    public function getSSLCertFilePath(): ?string;
    public function getSSLCertPassword(): ?string;
}