<?php

namespace Archman\PaymentLib\Test\Request\WeChat;

use Archman\PaymentLib\Request\WeChat\BatchQueryBillComment;
use PHPUnit\Framework\TestCase;
use Archman\PaymentLib\Test\Config;
use Archman\PaymentLib\Test\Config\WeChatConfig;

class BatchQueryBillCommentTest extends TestCase
{
    public function testMakingParameters()
    {
        $cases = Config::get('wechat', 'testCases', 'request', 'BatchQueryBillComment');
        foreach ($cases as $each) {
            $configData = Config::get('wechat', 'config', $each['appID']);
            $config = new WeChatConfig($configData);

            $request = (new BatchQueryBillComment($config))
                ->setBeginTime($each['fields']['begin_time'])
                ->setEndTime($each['fields']['end_time'])
                ->setOffset($each['fields']['offset'])
                ->setLimit($each['fields']['limit'] ?? null)
                ->setNonceStr($each['fields']['nonce_str']);

            $this->assertEquals($each['parameters'], $request->makeParameters());
        }
    }
}