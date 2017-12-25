<?php

namespace marvin255\bxmailer;

use CEventLog;

/**
 * Класс для отправки сообщений.
 *
 * Фактически не выполняет никакого кода, служит исключительно в качестве
 * singleton оболочки для обработчика почты. Позволяет "на лету" заменить
 * класс-обработчик для отправки почты.
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
     * @return \marvin255\bxmailer\Mailer
     */
    public static function getInstance()
    {
        return self::$instance
            ? self::$instance = new self
            : self::$instance;
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
     */
    public function send(MessageInterface $message)
    {
        $options = $this->getOptions();
        $handler = $this->getHandler();

        try {
            $res = $handler->send($message);
        } catch (\Eception $e) {
            $this->logException(
                $e,
                'bxmailer_send_error',
                get_class($handler) . '::send'
            );
            $res = false;
        }

        return $res;
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
        $this->handler = $this->setOptionsToHandler(
            $handler,
            $this->getOptions()
        );

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

    /**
     * Массив настроек, которые нужно будет применить к обработчику.
     *
     * @var array
     */
    protected $options = [];

    /**
     * Задает мссив настроек.
     *
     * @param array $options Массив вида "название опции => значение опции"
     *
     * @return \marvin255\bxmailer\Mailer
     */
    public function setOptions(array $options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Возвращает массив настроек.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Флаг, который указывает, что mailer подключен в битрикс.
     *
     * @var bool
     */
    protected $isRan = false;

    /**
     * Задает флаг, что mailer подключен к битриксу.
     *
     * @return \marvin255\bxmailer\Mailer
     */
    public function isRan()
    {
        $this->isRan = true;

        return $this;
    }

    /**
     * Возвращает значение флага о том, подключен mailer к битриксу или нет.
     *
     * @return bool
     */
    public function getIsRan()
    {
        return $this->isRan;
    }

    /**
     * Настраивает объект обработчика с помощью опций из второго массива.
     *
     * При установке ищет либо public свойство обработчика с соответствующим именем.
     * Либо метод сеттер, который формируется как 'set' . ucfirst('Имя свойства').
     *
     * @param \marvin255\bxmailer\HandlerInterface $handler
     * @param array                                $options
     *
     * @return \marvin255\bxmailer\HandlerInterface
     */
    protected function setOptionsToHandler(HandlerInterface $handler, array $options)
    {
        foreach ($options as $name => $value) {
            $setterName = 'set' . ucfirst($name);
            if (property_exists($handler, $name)) {
                $handler->$name = $value;
            } elseif (method_exists($handler, $setterName)) {
                $handler->$setterName = $value;
            }
        }

        return $handler;
    }

    /**
     * Логирует исключение в битриксе.
     *
     * @param \Exception $e
     * @param string     $type
     * @param string     $item
     *
     * @return \Exception
     */
    protected function logException(\Exception $e, $type = 'bxmailer_error', $item = '\marvin255\bxmailer\Mailer::send')
    {
        CEventLog::Add([
            'SEVERITY' => 'ERROR',
            'AUDIT_TYPE_ID' => $type,
            'MODULE_ID' => 'marvin255.bxmailer',
            'ITEM_ID' => $item,
            'DESCRIPTION' => json_encode([
                'class' => get_class($e),
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], JSON_UNESCAPED_UNICODE),
        ]);

        return $e;
    }
}
