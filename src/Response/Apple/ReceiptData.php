<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Response\Apple;

use Archman\PaymentLib\Response\Apple\ReceiptDataField\LatestReceiptInfo;

class ReceiptData implements \ArrayAccess
{
    public string $environment;

    public int $status;

    public bool $isRetryable = false;

    public string $latestReceipt = '';

    /** @var LatestReceiptInfo[]  */
    public array $latestReceiptInfo = [];

    /** @var LatestReceiptInfo[]  */
    public array $pendingRenewalInfo = [];

    public $receipt;

    private array $rawData = [];

    public function offsetGet($offset)
    {
        // TODO: Implement offsetGet() method.
    }


}