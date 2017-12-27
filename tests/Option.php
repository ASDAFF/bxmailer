<?php

namespace Bitrix\Main\Config;

/**
 * Мок для объекта Option для получения настроек модуля из битрикса.
 */
class Option
{
    /**
     * Массив настроек модулей, которые будет возвращать мок на соответствующие
     * запросы.
     *
     * @var array
     */
    public static $options = [];

    /**
     * Возвращает все настройки для указанного модуля.
     *
     * @param string $moduleId
     *
     * @return array
     */
    public static function getForModule($moduleId)
    {
        return isset(self::$options[$moduleId])
            ? self::$options[$moduleId]
            : [];
    }
}
