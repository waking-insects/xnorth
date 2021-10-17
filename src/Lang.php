<?php
namespace validate;
class Lang
{
    // JSON.parse(json);
    private static $lang = [];
    private static $range = "zh-cn";

    public static function range($range = '')
    {
        if ($range) {
            self::$range = $range;
        }
        return self::$range;
    }

    public static function load($file, $range = '')
    {
        $range = $range ?: self::$range;
        $file = is_string($file) ? [$file] : $file;
        if (!isset(self::$lang[$range])) {
            self::$lang[$range] = [];
        }
        $lang = [];
        foreach ($file as $value) {
            if (is_file($value)) {
                $_lang = include_once $value;

                if (is_array($_lang)) {
                    $lang = array_change_key_case($_lang) + $lang;
                }
            }
        }
        if (!empty($lang)) {
            self::$lang[$range] = $lang + self::$lang[$range];
        }

        return self::$lang[$range];
    }

    public static function get($name = null, $vars = [], $range = '')
    {
        $range = $range ?: self::$range;
        // 空参数返回所有定义

        if (empty($name)) {
            return self::$lang[$range];
        }
        $key = strtolower($name);
        $value = isset(self::$lang[$range][$key]) ? self::$lang[$range][$key] : $name;

        // 变量解析
        if (!empty($vars) && is_array($vars)) {
            /**
             * Notes:
             * 为了检测的方便，数字索引的判断仅仅是参数数组的第一个元素的key为数字0
             * 数字索引采用的是系统的 sprintf 函数替换，用法请参考 sprintf 函数
             */
            if (key($vars) === 0) {
                // 数字索引解析
                array_unshift($vars, $value);
                $value = call_user_func_array('sprintf', $vars);
            } else {
                // 关联索引解析
                $replace = array_keys($vars);
                foreach ($replace as &$v) {
                    $v = "{:{$v}}";
                }
                $value = str_replace($replace, $vars, $value);
            }

        }

        return $value;
    }

    public static function has($name, $range = '')
    {
        $range = $range ?: self::$range;

        return isset(self::$lang[$range][strtolower($name)]);
    }
}