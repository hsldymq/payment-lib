<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Response\WeChat;

use Archman\PaymentLib\Exception\InternalErrorException;
use Archman\PaymentLib\Response\BaseResponse;

class BillResponse extends BaseResponse implements \Iterator
{
    private int $offset;

    private int $position = 0;

    /**
     * @var array 根据账单类型的不同而不同 [
     *      [
     *          '交易时间' => 'xxx',
     *          '公众账号ID' => 'yyy',
     *          '商户号' => 'zzz',
     *          ...
     *      ],
     *      ...
     * ]
     */
    private array $bill = [];

    /**
     * @var array 合计 [
     *      '总交易订单数' => 'xxx',
     *      '应结订单总金额' => 'yyy',
     *      ...
     * ]
     */
    private array $summary = [];

    public function __construct(string $bill)
    {
        parent::__construct([]);

        // 去掉UTF-8 BOM
        if (strpos($bill, "\xEF\xBB\xBF") === 0) {
            $bill = substr($bill, 3);
        }

        // 这个接口微信给过来的数据开头带BOM表示,并且按照\r\n分割,文档里没有说明!
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
            $combined = array_combine($keys, $fields);
            if ($combined === false) {
                throw new InternalErrorException([
                    'titles' => $keys,
                    'values' => $fields,
                ], "combining bill titles with values");
            }
            $this->bill[] = $combined;
        }

        if (count($rows) > 0) {
            $keys = explode(',', array_shift($rows));
            $fields = explode(',`', ltrim(array_shift($rows), '`'));
            $combined = array_combine($keys, $fields);
            if ($combined === false) {
                throw new InternalErrorException([
                    'titles' => $keys,
                    'values' => $fields,
                ], "combining bill summary titles with values");
            }
            $this->summary = $combined;
        }
    }

    /**
     * 返回合计.
     *
     * @return array
     */
    public function summary(): array
    {
        return $this->summary;
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