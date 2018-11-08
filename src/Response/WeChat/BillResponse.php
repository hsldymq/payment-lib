<?php

namespace Archman\PaymentLib\Response\WeChat;

use Archman\PaymentLib\Response\BaseResponse;

class BillResponse extends BaseResponse implements \Iterator
{
    /** @var int */
    private $offset;

    private $position = 0;

    /**
     * @var array 根据账单类型的不同而不同 [
     *      [
     *          '交易时间' => 'xxx,
     *          '公众账号ID' => 'yyy',
     *          '商户号' => 'zzz',
     *          ...
     *      ],
     *      ...
     * ]
     */
    private $bill = [];

    // 总交易订单数
    private $billCount = 0;

    // 总交易额
    private $tradeAmount = 0;

    // 总退款金额
    private $refundAmount = 0;

    // 总代金券或立减优惠退款金额
    private $prefRefundAmount = 0;

    // 手续费总金额
    private $chargeAmount = 0;

    public function __construct(string $bill)
    {
        parent::__construct([]);

        // 去掉UTF-8 BOM
        if (strpos($bill, "\xEF\xBB\xBF") === 0) {
            $bill = substr($bill, 3);
        }
        $rows = preg_split('/\r?\n/', $bill);
        $rowCount = count($rows);

        // 去掉无效行
        if (count($rows) > 0 && !$rows[$rowCount - 1]) {
            array_pop($rows);
        }

        $keys = explode(',', array_shift($rows));
        while (count($rows) > 2) {
            $eachRow = array_shift($rows);
            $eachRow = ltrim($eachRow, '`');
            $fields = explode(',`', $eachRow);
            $this->bill[] = array_combine($keys, $fields);
        }

        if (count($rows) > 0) {
            $fields = explode(',`', ltrim($rows[1], '`'));
            [$billCount, $tradeAmount, $refundAmount, $preferentialRefundAmount, $chargeAmount] = $fields;

            $this->billCount = intval($billCount);
            $this->tradeAmount = intval(round($tradeAmount * 100));
            $this->refundAmount = intval(round($refundAmount * 100));
            $this->prefRefundAmount = intval(round($preferentialRefundAmount * 100));
            $this->chargeAmount = intval(round($chargeAmount * 100));
        }
    }

    /**
     * 返回总交易订单数.
     *
     * @return int
     */
    public function getBillCount(): int
    {
        return $this->billCount;
    }

    /**
     * 返回总交易额(单位: 分).
     *
     * @return int
     */
    public function getTradeAmount(): int
    {
        return $this->tradeAmount;
    }

    /**
     * 返回总退款金额(单位: 分).
     *
     * @return int
     */
    public function getRefundAmount(): int
    {
        return $this->refundAmount;
    }

    /**
     * 返回总代金券或立减优惠退款金额(单位: 分).
     *
     * @return int
     */
    public function getPrefRefundAmount(): int
    {
        return $this->prefRefundAmount;
    }

    /**
     * 返回手续费总金额(单位: 分).
     *
     * @return int
     */
    public function getChargeAmount(): int
    {
        return $this->chargeAmount;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    public function rewind()
    {
        $this->position = 0;
    }

    public function valid()
    {
        return isset($this->bill[$this->position]);
    }

    public function current()
    {
        return $this->bill[$this->position];
    }

    public function key()
    {
        return $this->position;
    }

    public function next()
    {
        ++$this->position;
    }
}