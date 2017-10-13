<?php
namespace Archman\PaymentLib\Request;

interface RequestOptionInterface
{
    public function getRootCAFilePath(): ?string;

    public function getSSLKeyFilePath(): ?string;
    public function getSSLPassword(): ?string;

    public function getClientCertFilePath(): ?string;
    public function getClientCertPassword(): ?string;
}