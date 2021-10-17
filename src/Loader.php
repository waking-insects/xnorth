<?php

namespace validate;

use validate\Config;
use validate\exception\ClassNotFoundException;
use validate\Validate;
use validate\Request;

class Loader
{
    protected static $instance = [];

    public static function validate($name = '', $layer = 'Validate', $appendSuffix = false, $common = 'common')
    {
        $name = $name ?: Config::get('default_validate');

        if (empty($name)) {
            return new Validate;
        }

        $uid = $name . $layer;

        if (isset(self::$instance[$uid])) {
            return self::$instance[$uid];
        }
        list($module, $class) = self::getModuleAndClass($name, $layer, $appendSuffix);
        ///var_dump(\validate\Config::get());
        Config::load(__DIR__ . '/config_validate.php');
        $dir = Config::get('validate');
        $name = $dir . '\\' . $class;
        $validate = new $name;
        self::$instance[$uid] = $validate;
        return $validate;
    }

    protected static function getModuleAndClass($name, $layer, $appendSuffix)
    {
        $module = Request::instance()->module();
        if ($appendSuffix) {
            $class = $name;
        } else {
            $class = $name . $layer;
        }
        return [$module, $class];
    }
}