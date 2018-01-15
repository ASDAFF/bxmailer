<?php

namespace marvin255\bxmailer;

use Bitrix\Main\Localization\Loc;

/**
 * Класс, который содержит в себе функции для обработчиков событий, которые
 * использует модуль.
 */
class EventHandler
{
    /**
     * Регистрирует типы и названия событий модуля для журнала событий.
     *
     * @return array
     */
    public static function onEventLogGetAuditTypes()
    {
        Loc::loadMessages(__FILE__);

        return [
            'bxmailer_initialize_error' => '[bxmailer_initialize_error] ' . Loc::getMessage('bxmailer_initialize_error'),
            'bxmailer_send_error' => '[bxmailer_send_error] ' . Loc::getMessage('bxmailer_send_error'),
        ];
    }
}
