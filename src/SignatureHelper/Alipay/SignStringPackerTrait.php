<?php

declare(strict_types=1);

namespace Archman\PaymentLib\SignatureHelper\Alipay;

Trait SignStringPackerTrait
{
    /**
     * 生成请求的代签名字符串.
     *
     * 这是在服务器向支付宝发起接口调用时,生成请求签名用的.
     *
     * @see https://doc.open.alipay.com/docs/doc.htm?docType=1&articleId=106118 详情见这里.
     *
     * @param array $data
     * @param array $exclude
     *
     * @return string
     */
    protected function packRequestSignString(array $data, array $exclude): string
    {
        ksort($data);
        $kv = [];
        foreach ($data as $k => $v) {
            if (!$this->isEmpty($v) &&
                !$this->isSignField($k) &&
                !$this->startWithAt($v) &&
                !in_array($k, $exclude)
            ) {
                $kv[] = "{$k}={$v}";
            }
        }

        return implode('&', $kv);
    }

    /**
     * 生成同步回调验签待签名字符串.
     *
     * 这是在服务器向支付宝发起接口调用时,对响应数据进行签名验证用的.
     *
     * @see https://doc.open.alipay.com/docs/doc.htm?docType=1&articleId=106120#s0 详情见这里
     *
     * @param array $data 这个数据应该是response体的数据,调用者不应该直接把整个数据结构丢过来
     * @param array $exclude
     *
     * @return string
     */
    protected function packVerifiedSignStringSync(array $data, array $exclude): string
    {
        foreach ($exclude as $k) {
            unset($data[$k]);
        }

        return json_encode($data);
    }

    /**
     * 生成异步回调验签待签名字符串.
     *
     * 这是在支付宝异步回调接口时,对其请求数据进行签名验证用的.
     *
     * @see https://doc.open.alipay.com/docs/doc.htm?docType=1&articleId=106120#s1 详情见这里
     *
     * @param array $data
     * @param array $exclude
     *
     * @return string
     */
    protected function packVerifiedSignStringAsync(array $data, array $exclude): string
    {
        ksort($data);
        $kv = [];
        foreach ($data as $k => $v) {
            if (!$this->isEmpty($v) &&
                !$this->isSignField($k) &&
                !$this->isSignTypeField($k) &&
                !$this->startWithAt($v) &&
                !in_array($k, $exclude)
            ) {
                $kv[] = "{$k}={$v}";
            }
        }

        return implode('&', $kv);
    }

    private function isEmpty($value): bool
    {
        return !isset($value) || $value === null || (is_string($value) && trim($value) === '');
    }

    private function startWithAt($value): bool
    {
        return is_string($value) && $value[0] === '@';
    }

    private function isSignField(string $field_name): bool
    {
        return $field_name === 'sign';
    }

    private function isSignTypeField(string $field_name): bool
    {
        return $field_name === 'sign_type';
    }
}