<?php

namespace marvin255\bxmailer;

/**
 * Интерфейс для обработчика сообщений, для которого может быть включен
 * режим отладки.
 */
interface HandlerDebugInterface
{
    /**
     * Включает или отключает режим отладки для обработчика.
     *
     * @param bool $debugMode
     *
     * @return \marvin255\bxmailer\HandlerDebugInterface
     */
    public function setDebug($debugMode = true);

    /**
     * Возвращает статус режимаотладки.
     *
     * @return bool
     */
    public function getDebug();
}
