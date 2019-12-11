<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Request\WeChat\Enums;

/**
 * 资金账户类型
 */
class BillTypeEnum
{
    // 当日所有订单
    const ALL = 'ALL';

    // 当日成功支付的订单
    const SUCCESS = 'SUCCESS';

    // 当日退款订单
    const REFUND = 'REFUND';

    // 当日充值退款订单
    const RECHARGE_REFUND = 'RECHARGE_REFUND';
}