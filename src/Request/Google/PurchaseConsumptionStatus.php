<?php
namespace Archman\PaymentLib\Request\Google;

use Archman\PaymentLib\ConfigManager\GoogleConfigInterface;

/**
 * 查询购买状态.
 * @link https://developers.google.com/android-publisher/api-ref/purchases/products
 * @link https://developers.google.com/android-publisher/api-ref/purchases/products/get
 */
class PurchaseConsumptionStatus
{
    private $config;

    private $params = [
        'product_id' => null,
        'token' => null,
    ];

    public function __construct(GoogleConfigInterface $config)
    {
        $this->config = $config;
    }

    public function setProductID(string $id): self
    {
        $this->params['product_id'] = $id;

        return $this;
    }

    public function setToken(string $token): self
    {
        $this->params['token'] = $token;

        return $this;
    }
}