<?php
namespace Archman\PaymentLib\RequestInterface\WeChat;

use Archman\PaymentLib\ConfigManager\WeChatConfigInterface;
use Archman\PaymentLib\SignatureHelper\WeChat\Generator;

/**
 * 下载对账单.
 * @link https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=9_6
 */
class DownloadBill
{
    private const SIGN_TYPE = 'HMAC-SHA256';

    private $config;

    private $params = [
        'begin_time' => null,
        'end_time' => null,
        'offset' => null,
        'limit' => null,
    ];

    public function __construct(WeChatConfigInterface $config)
    {
        $this->config = $config;
    }

    public function makeParameters(): array
    {
        $params = [
            'app' => $this->config->getAppID(),
            'mch_id' => $this->config->getMerchantID(),
            'nonce_str' => md5(time()),
            'sign_type' => self::SIGN_TYPE,
            'begin_time' => $this->params['begin_time'],
            'end_time' => $this->params['end_time'],
            'offset' => $this->params['offset'],
        ];
        $this->params['limit'] && $params['limit'] = $this->params['limit'];
        $params['sign'] = (new Generator($this->config))->makeSign($params, self::SIGN_TYPE);

        return $params;
    }

    public function setBeginTime(\DateTime $datetime): self
    {
        $this->params['begin_time'] = $datetime->format('YmdHis');

        return $this;
    }

    public function setEndTime(\DateTime $datetime): self
    {
        $this->params['end_time'] = $datetime->format('YmdHis');

        return $this;
    }

    public function setOffset(int $offset): self
    {
        $this->params['offset'] = $offset;

        return $this;
    }

    public function setLimit(int $limit): self
    {
        $this->params['limit'] = $limit;

        return $this;
    }
}