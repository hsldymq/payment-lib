<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Request\WeChat\Enums;

/**
 * 资金账户类型
 */
class AccountTypeEnum
{
    // 基本账户
    const BASIC = 'Basic';

    // 运营账户
    const OPERATION = 'Operation';

    // 手续费账户
    const FEES = 'Fees';
}