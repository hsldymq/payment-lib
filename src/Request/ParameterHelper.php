<?php
namespace Archman\PaymentLib\Request;

use Api\Exception\Logic\MakePaymentVendorParametersFailedException;

class ParameterHelper
{
    /**
     * 检查必要参数.缺少必要参数就抛错.
     * @param array $parameters
     * @param array $required_list 必选列表
     * @param array $optional 多选一列表
     * @throws MakePaymentVendorParametersFailedException
     */
    public static function checkRequired(array $parameters, array $required_list, array $optional = [])
    {
        foreach ($required_list as $param_name) {
            if (self::isInvalid($parameters[$param_name])) {
                throw new MakePaymentVendorParametersFailedException(['message' => "Parameter {$param_name} Is Required"]);
            }
        }

        $result = array_reduce($optional, function ($prev, $curr) use ($parameters) {
            return boolval($prev) || !self::isInvalid($parameters[$curr]);
        }, null);
        if ($result === false) {
            throw new MakePaymentVendorParametersFailedException([
                'message' => 'One Of These Parameters (' . implode(',', $optional) . ') Is Required'
            ]);
        }
    }

    /**
     * 生成有效的请求参数.
     *
     * @param array $parameters
     * @return array
     */
    public static function packValidParameters(array $parameters): array
    {
        $biz_content = [];

        foreach ($parameters as $field => $value) {
            $value = self::filterInvalid($value);

            if (!is_null($value)) {
                $biz_content[$field] = $value;
            }
        }

        return $biz_content;
    }

    public static function checkAmount(int $amount)
    {
        if ($amount <= 0) {
            throw new MakePaymentVendorParametersFailedException(['message' => 'Parameter amount Should Not Less Than 1']);
        }
    }

    /**
     * 将以分为单位的金额换算为元,保留小数点后两位.
     * @param int $amount
     * @return string
     */
    public static function transUnitCentToYuan(int $amount): string
    {
        return sprintf('%.2f', $amount / 100);
    }

    /**
     * 过滤掉无效数据,被过滤的值以null返回
     *  1. 如果参数为null,空字符串被视为无效.
     *  2. 如果参数为数组,那么按照以上规则递归进行过滤
     * @param $param
     * @return mixed
     */
    private static function filterInvalid($param)
    {
        if (self::isInvalid($param)) {
            return null;
        } else if (is_array($param)) {
            $filter_array = [];
            foreach ($param as $key => $value) {
                $value = self::filterInvalid($value);
                if (!is_null($value)) {
                    $filter_array[$key] = $value;
                }
            }

            return empty($filter_array) ? null : $filter_array;
        }

        return $param;
    }

    private static function isInvalid($content): bool
    {
        return $content === null ||
            (is_string($content) && trim($content) === '') ||
            (is_array($content) && empty($content));
    }
}