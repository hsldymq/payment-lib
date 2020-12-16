<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Apple\Enum;

/**
 * @see https://developer.apple.com/documentation/appstoreservernotifications/notification_type
 */
class NotificationTypeEnum
{
    const CANCEL = 'CANCEL';

    const DID_CHANGE_RENEWAL_PREF = 'DID_CHANGE_RENEWAL_PREF';

    const DID_CHANGE_RENEWAL_STATUS = 'DID_CHANGE_RENEWAL_STATUS';

    const DID_FAIL_TO_RENEW = 'DID_FAIL_TO_RENEW';

    const DID_RECOVER = 'DID_RECOVER';

    const DID_RENEW = 'DID_RENEW';

    const INITIAL_BUY = 'INITIAL_BUY';

    const INTERACTIVE_RENEWAL = 'INTERACTIVE_RENEWAL';

    const PRICE_INCREASE_CONSENT = 'PRICE_INCREASE_CONSENT';

    const REFUND = 'REFUND';

    /**
     * @deprecated in production in March 2021, rely on DID_RECOVER instead
     */
    const RENEWAL = 'RENEWAL';

    const REVOKE = 'REVOKE';
}
