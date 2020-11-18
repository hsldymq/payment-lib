<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Alipay\Traits;

/**
 * 考虑到该库无法紧跟支付宝开发平台接口的最新定义,如果接口定义中增加了字段,就需要为调用方提供一个途径设置最新定义的字段.
 *
 * @property array $params 公共请求参数
 * @property array $bizContent 请求参数
 */
trait OpenAPIExtendableTrait
{
    /**
     * 该方法用于扩展公共请求参数.
     *
     * @param string $key
     * @param mixed $value
     *
     * @return $this
     */
    public function setParam(string $key, $value): self
    {
        $this->params[$key] = $value;

        return $this;
    }

    /**
     * 该方法用于扩展biz_content中字段.
     *
     * @param string $key
     * @param mixed $value
     *
     * @return $this
     */
    public function setBizContentField(string $key, $value): self
    {
        $this->bizContent[$key] = $value;

        return $this;
    }
}