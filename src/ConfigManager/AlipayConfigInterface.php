<?php
namespace Archman\PaymentLib\ConfigManager;

interface AlipayConfigInterface
{
    public function getAppID(): string;

    public function getPartnerID(): string;

    /**
     * 开放平台应用密钥默认使用的签名类型.
     * getOpenAPIPrivateKey不传参数得到密钥所使用的算法.
     * @return string
     */
    public function getOpenAPIDefaultSignType(): string;

    /**
     * 开放平台应用密钥.
     * 如果是RSA/RSA2算法,应返回PKCS格式.
     * @param null|string $signType 不传或传null返回getOpenAPIDefaultSignType方法值对应的密钥.
     * @return string
     */
    public function getOpenAPIPrivateKey(?string $signType = null): string;

    /**
     * 返回开放平台支付宝公钥.
     * 如果是RSA/RSA2算法,返回的公钥值应该为PKCS格式.
     * @param string $signType 签名类型(RSA, RSA2, ...).
     * @return string
     */
    public function getOpenAPIAlipayPublicKey(string $signType): string;

    /**
     * 返回开放平台AES密钥.
     * @link https://docs.open.alipay.com/common/104567
     * @return string 返回base64_encode后的值而非原始的二进制值,否则无法正确的加解密
     */
    public function getOpenAPIEncryptionKey(): string;

    /**
     * MAPI默认网关签名类型.
     * getMAPIPrivateKey不传参数得到密钥所使用的算法.
     * @return string
     */
    public function getMAPIDefaultSignType(): string;

    /**
     * MAPI网关密钥(MD5安全校验码, RSA密钥, DSA秘钥).
     * 如果是RSA/RSA2算法,应返回PKCS格式.
     * @param null|string $signType 不传或传null返回getMAPIDefaultSignType方法值对应的密钥.
     * @return string
     */
    public function getMAPIPrivateKey(?string $signType = null): string;

    /**
     * 返回MAPI支付宝公钥.
     * 如果是RSA/RSA2算法,返回的公钥值应该为PKCS格式.
     * @param string $signType 签名类型(RSA, DSA, MD5...).
     * @return string
     */
    public function getMAPIAlipayPublicKey(string $signType): string;
}