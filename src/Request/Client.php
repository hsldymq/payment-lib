<?php

namespace Archman\PaymentLib\Request;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Client as GuzzleClient;

class Client extends BaseClient
{
    protected static function doSend(RequestInterface $request, RequestOptionInterface $option): ResponseInterface
    {
        $ssl = [];
        if ($path = $option->getSSLKeyFilePath()) {
            $ssl[0] = $path;
            if ($password = $option->getSSLKeyPassword()) {
                $ssl[1] = $password;
            }
        }

        $cert = [];
        if ($path = $option->getSSLCertFilePath()) {
            $cert[0] = $path;
            if ($password = $option->getSSLCertPassword()) {
                $cert[1] = $password;
            }
        }

        $verify = $option->getRootCAFilePath() ?? false;        // null不验证证书
        $verify = $verify === '' ? true : $verify;              // 空字符串使用系统默认提供的证书验证

        $config = ['verify' => $verify];
        $ssl && $config['ssl_key'] = $ssl;
        $cert && $config['cert'] = $cert;

        return (new GuzzleClient($config))->send($request);
    }
}