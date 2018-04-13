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
        $option->getSSLKeyFilePath() && $ssl[0] = $option->getSSLKeyFilePath();
        $ssl && $option->getSSLKeyPassword() && $ssl[1] = $option->getSSLKeyPassword();

        $cert = [];
        $option->getSSLCertFilePath() && $cert[0] = $option->getSSLCertFilePath();
        $cert && $option->getSSLCertPassword() && $cert[1] = $option->getSSLCertPassword();

        $verify = $option->getRootCAFilePath() ?? false;        // null不验证证书
        $verify = $verify === '' ? true : $verify;              // 空字符串使用系统默认提供的证书验证

        $config = ['verify' => $verify];
        $ssl && $config['ssl_key'] = $ssl;
        $cert && $config['cert'] = $cert;

        return (new GuzzleClient($config))->send($request);
    }
}