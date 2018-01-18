<?php

namespace marvin255\bxmailer;

/**
 * Класс для отправки сообщений.
 *
 * Служит контейнером для обработчика отправки почты. Логирует исключения.
 *
 * Так, как нет никакой возможности передать через какой-либо
 * встроенный механизм, то реализует Singleton, что, в принципе,
 * не особо нужно было бы.
 */
class Mailer
{
    /**
     * Объект для реализации singleton.
     *
     * @var \marvin255\bxmailer\Mailer
     */
    private static $instance = null;

    /**
     * Возвращает объект singleton, если он уже создан, либо создает новый
     * и возвращает новый.
     *
     * @param bool $refresh Флаг, который обозначает, что нужно создать новый объект
     *
     * @return \marvin255\bxmailer\Mailer
     */
    public static function getInstance($refresh = false)
    {
        if (self::$instance === null || $refresh) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Реализация singleton. Запрещает создание новых объектов.
     */
    private function __construct()
    {
    }

    /**
     * Отправляет письмо с теми данными, которые описаны в объекте сообщения.
     *
     * @param \marvin255\bxmailer\MessageInterface $message
     *
     * @return bool
     *
     * @throws \marvin255\bxmailer\Exception
     */
    public function send(MessageInterface $message)
    {
        $return = false;

        try {
            $return = $this->getHandler()->send($message);
        } catch (\Exception $e) {
            throw new Exception($e->getMessage(), null, $e);
        }

        return $return;
    }

    /**
     * Обработчик, который будет непосредственно заниматься отправкой сообщений.
     *
     * @var \marvin255\bxmailer\HandlerInterface
     */
    protected $handler = null;

    /**
     * Устанавливает обработчик сообщений.
     *
     * @param \marvin255\bxmailer\HandlerInterface $handler
     *
     * @return \marvin255\bxmailer\Mailer
     */
    public function setHandler(HandlerInterface $handler)
    {
        $this->handler = $handler;

        return $this;
    }

    /**
     * Возвращает текущий обработчик сообщений.
     *
     * @return \marvin255\bxmailer\HandlerInterface|null
     */
    public function getHandler()
    {
        return $this->handler;
    }
}
