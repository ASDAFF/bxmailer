<?php

namespace marvin255\bxmailer\handler;

use marvin255\bxmailer\HandlerInterface;
use marvin255\bxmailer\MessageInterface;

/**
 * Класс для отправки писем с помощью phpmailer.
 */
class PhpMailer implements HandlerInterface
{
    /**
     * @inheritdoc
     */
    public function send(MessageInterface $message)
    {
    }
}
