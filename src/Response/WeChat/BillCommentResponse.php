<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Response\WeChat;

use Archman\PaymentLib\Response\BaseResponse;

class BillCommentResponse extends BaseResponse implements \Iterator
{
    /** @var int */
    private int $offset;

    private int $position = 0;

    /**
     * @var array [
     *      [
     *          'datetime' => '评论时间',
     *          'orderID' => '支付订单号',
     *          'rating' => '评论星级',
     *          'comment' => '评论内容',
     *      ],
     *      ...
     * ]
     */
    private array $comments = [];

    public function __construct(string $comments)
    {
        parent::__construct([]);

        $rows = explode("\n", $comments);
        foreach ($rows as $index => $eachRow) {
            $eachRow = ltrim($eachRow, '`');

            if ($index === 0) {
                $this->offset = intval($eachRow);
                continue;
            }
            if (!$eachRow) {
                continue;
            }

            $fields = explode(',`', $eachRow);
            $comment['time'] = array_shift($fields);
            $comment['orderID'] = array_shift($fields);
            $comment['rating'] = array_shift($fields);
            if (count($comment) > 1) {
                $fields[0] = implode(',`', $fields);
            }
            $comment['comment'] = array_shift($fields);

            $this->comments[] = $comment;
        }
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
        return isset($this->comments[$this->position]);
    }

    public function current()
    {
        return $this->comments[$this->position];
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