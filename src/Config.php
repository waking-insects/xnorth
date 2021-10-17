<?php
namespace validate;
use validate\Request;

class Config{
    private static $config = [];
    private static $range = '_sys_';
    public static function load($file, $name = '', $range = '')
    {

        $range = $range ?: self::$range;

        if (!isset(self::$config[$range])) self::$config[$range] = [];

        if (is_file($file)) {
            $name = strtolower($name);
            $type = pathinfo($file, PATHINFO_EXTENSION);

            if ('php' == $type) {

                return self::set(include $file, $name, $range);
            }
            return self::parse($file, $type, $name, $range);
        }

        return self::$config[$range];
    }
    public static function set($name,$value=null,$range=''){
        if (is_array($name)) {

            if (!empty($value)) {

                self::$config[$range][$value] = isset(self::$config[$range][$value]) ?
                    array_merge(self::$config[$range][$value], $name) :
                    $name;
                return self::$config[$range][$value];
            }

            return self::$config[$range] = array_merge(
                self::$config[$range], array_change_key_case($name)
            );
        }
        return self::$config[$range];
    }
    public static function parse($config, $type = '', $name = '', $range = '')
    {
        $range = $range ?: self::$range;

        if (empty($type)) $type = pathinfo($config, "");

        $class = false !== strpos($type, '\\') ?
            $type :
            '\\validate\\driver\\' . ucwords($type);

        return self::set((new $class())->parse($config), $name, $range);
    }
    public static function get($name = null, $range = '')
    {
        $range = $range ?: self::$range;

        // 无参数时获取所有

        if (empty($name) && isset(self::$config[$range])) {
            return self::$config[$range];
        }


        // 非二级配置时直接返回
        if (!strpos($name, '.')) {
            $name = strtolower($name);
            return isset(self::$config[$range][$name]) ? self::$config[$range][$name] : null;
        }

        // 二维数组设置和获取支持
        $name    = explode('.', $name, 2);
        $name[0] = strtolower($name[0]);

        if (!isset(self::$config[$range][$name[0]])) {
            // 动态载入额外配置
            $module = Request::instance()->module();
            $file   = CONF_PATH . ($module ? $module . DS : '') . 'extra' . DS . $name[0] . CONF_EXT;
            is_file($file) && self::load($file, $name[0]);
        }

        return isset(self::$config[$range][$name[0]][$name[1]]) ?
            self::$config[$range][$name[0]][$name[1]] :
            null;
    }
}