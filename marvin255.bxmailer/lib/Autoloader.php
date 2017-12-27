<?php

namespace marvin255\bxmailer;

/**
 * Автозагрузчик для классов модуля.
 */
class Autoloader
{
    /**
     * Соответствие между пространством имен и папкой в файловой системе.
     *
     * @var array
     */
    protected static $psr = null;
    /**
     * Флаг о том, что автозагрузчик зарегистрирован.
     *
     * @var bool
     */
    protected static $splRegister = null;

    /**
     * @param string $path
     *
     * @return bool
     *
     * @throws \marvin255\bxmailer\Exception
     */
    public static function register($prefix = __NAMESPACE__, $path = __DIR__)
    {
        $prefix = trim($prefix, ' \\');
        if ($prefix === '') {
            throw new Exception('Empty prefix for autoloader');
        }

        $path = rtrim(trim($path), '/');
        if ($path === '') {
            throw new Exception('Empty path for autoloader');
        }

        self::$psr[$prefix] = $path;

        if (!self::$splRegister) {
            self::$splRegister = spl_autoload_register([__CLASS__, 'load'], true, true);
        }

        return self::$splRegister;
    }

    /**
     * @param string $class
     */
    public static function load($class)
    {
        foreach (self::$psr as $prefix => $path) {
            $len = strlen($prefix);
            if (strncmp($prefix, $class, $len) !== 0) {
                continue;
            }
            $relative_class = substr($class, $len);
            $file = $path . '/' . str_replace('\\', '/', $relative_class) . '.php';
            if (file_exists($file)) {
                require $file;
                break;
            }
        }
    }
}
