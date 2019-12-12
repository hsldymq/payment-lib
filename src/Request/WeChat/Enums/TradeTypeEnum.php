<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Request\WeChat\Enums;

/**
 * 交易类型
 */
class TradeTypeEnum
{
    const APP = 'APP';

    const NATIVE = 'NATIVE';

    const JS_API = 'JSAPI';

    const MICRO_PAY = 'MICROPAY';
}