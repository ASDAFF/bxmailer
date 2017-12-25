<?php

/**
 * Мок для объекта CEventLog для записи логов в битриксе.
 */
class CEventLog
{
    /**
     * Данные, с которыми в последний раз была запущена функция add.
     *
     * @var array|null
     */
    public static $add = null;

    /**
     * Запись в лог. Запоминает данные, с которыми была вызвана в
     * соответствующую статическую переменную.
     *
     * @param array $data
     */
    public static function add(array $data)
    {
        self::$add = $data;
    }
}
