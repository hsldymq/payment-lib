<?php
namespace Archman\PaymentLib\Request\WeChat;
use Archman\PaymentLib\ConfigManager\WeChatConfigInterface;
use Archman\PaymentLib\Exception\InvalidParameterException;
use Archman\PaymentLib\Request\ParameterHelper;
use Archman\PaymentLib\Request\RequestableInterface;
use Archman\PaymentLib\Request\WeChat\Traits\NonceStrTrait;
use Archman\PaymentLib\RequestInterface\WeChat\Traits\RequestPreparationTrait;
use Archman\PaymentLib\SignatureHelper\WeChat\Generator;

/**
 * 拉取订单评价数据.
 * @link https://pay.weixin.qq.com/wiki/doc/api/micropay.php?chapter=9_17&index=11
 */
class BatchQueryBillComment implements RequestableInterface
{
    use NonceStrTrait;
    use RequestPreparationTrait;

    private const FIXED_SIGN_TYPE = 'HMAC-SHA256';

    private $config;

    private $uri = 'https://api.mch.weixin.qq.com/billcommentsp/batchquerycomment';

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
        ParameterHelper::checkRequired($this->params, ['begin_time', 'end_time', 'offset']);

        $parameters = ParameterHelper::packValidParameters($this->params);
        $parameters['appid'] = $this->config->getAppID();
        $parameters['mch_id'] = $this->config->getMerchantID();
        $parameters['nonce_str'] = $this->getNonceStr();
        $parameters['sign_type'] = self::FIXED_SIGN_TYPE;
        $parameters['sign'] = (new Generator($this->config))->makeSign($parameters, self::FIXED_SIGN_TYPE);

        return $parameters;
    }

    public function setBeginTime(\DateTime $dt): self
    {
        $this->params['begin_time'] = $dt->format('YmdHis');

        return $this;
    }

    public function setEndTime(\DateTime $dt): self
    {
        $this->params['end_time'] = $dt->format('YmdHis');

        return $this;
    }

    public function setOffset(int $offset): self
    {
        $this->params['offset'] = $offset;

        return $this;
    }

    public function setLimit(int $limit): self
    {
        if ($limit > 200 || $limit < 1) {
            throw new InvalidParameterException("Invalid Limit Number({$limit}).");
        }

        $this->params['limit'] = $limit;

        return $this;
    }
}