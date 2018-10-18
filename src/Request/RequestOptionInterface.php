<?php

namespace Archman\PaymentLib\Request;

interface RequestOptionInterface
{
    /**
     * @return null|string 返回null:不进行证书验证, 返回空字符串:使用系统提供的证书验证, 返回非空字符串:证书文件绝对路径
     */
    public function getRootCAFilePath(): ?string;

    public function getSSLKeyFilePath(): ?string;
    public function getSSLKeyPassword(): ?string;

    public function getSSLCertFilePath(): ?string;
    public function getSSLCertPassword(): ?string;
}