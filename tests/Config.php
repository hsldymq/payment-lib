<?php

declare(strict_types=1);

namespace Archman\PaymentLib\Test;

class Config
{
    private static $config = null;

    public static function get()
    {
        if (!($path = func_get_args())) {
            return null;
        }

        $c = self::$config;
        do {
            $k = array_shift($path);
            if (!isset($c[$k])) {
                return null;
            }
            $v = $c = $c[$k];
        } while ($path);

        return $v;
    }

    public static function init()
    {
        static $init;

        if (!$init) {
            self::$config = require __DIR__.'/data/data.php';
            $init = true;
        }
    }
}