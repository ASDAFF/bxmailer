<?php

namespace marvin255\bxmailer;

/**
 * Интерфейс для обработчика сообщений, который будет непосредственно
 * заниматься отправкой.
 */
interface HandlerInterface
{
    /**
     * Отправляет письмо с теми данными, которые описаны в объекте сообщения.
     *
     * @param \marvin255\bxmailer\MessageInterface $message
     *
     * @return bool
     */
    public function send(MessageInterface $message);
}
