<?php

namespace marvin255\bxmailer;

/**
 * Автозагрузчик для классов модуля.
 */
class Autoloader
{
    /**
     * @param string
     */
    protected static $prefix = null;
    /**
     * @param string
     */
    protected static $path = null;

    /**
     * @param string $path
     *
     * @return bool
     */
    public static function register($prefix = __NAMESPACE__, $path = __DIR__)
    {
        self::$prefix = $prefix;
        self::$path = $path;

        return spl_autoload_register([__CLASS__, 'load'], true, true);
    }

    /**
     * @param string $class
     */
    public static function load($class)
    {
        $len = strlen(self::$prefix);
        if (strncmp(self::$prefix, $class, $len) !== 0) {
            return;
        }
        $relative_class = substr($class, $len);
        $file = self::$path . '/' . str_replace('\\', '/', $relative_class) . '.php';
        if (file_exists($file)) {
            require $file;
        }
    }
}
