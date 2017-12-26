<?php

namespace marvin255\bxmailer\handler;

use PHPMailer\PHPMailer\PHPMailer as PhpMailerLib;
use marvin255\bxmailer\HandlerInterface;
use marvin255\bxmailer\MessageInterface;
use marvin255\bxmailer\Exception;

/**
 * Класс для отправки писем с помощью phpmailer.
 */
class PhpMailer implements HandlerInterface
{
    /**
     * Флаг, который указывает, что нужно использовать smtp.
     *
     * @var bool
     */
    public $is_smtp = false;
    /**
     * Логин для smtp.
     *
     * @var string
     */
    public $smtp_login = '';
    /**
     * Пароль для smtp.
     *
     * @var string
     */
    public $smtp_password = '';
    /**
     * Хост для smtp.
     *
     * @var string
     */
    public $smtp_host = '';
    /**
     * Порт для smtp.
     *
     * @var string
     */
    public $smtp_port = '';
    /**
     * Флаг, который указывает, что нужно использовать авторизацию для smtp.
     *
     * @var bool
     */
    public $smtp_auth = false;
    /**
     * Тип шифрования.
     *
     * @var string
     */
    public $smtp_secure = '';
    /**
     * Кодировка письма.
     *
     * @var string
     */
    public $charset = 'UTF-8';
    /**
     * Время в секундах, которое скрипт ожидает ответа от smtp.
     *
     * @var int
     */
    public $smtp_timeout = 15;
    /**
     * Уровень дебага для smtp.
     *
     * @var int
     */
    public $smtp_debug = 0;

    /**
     * Объект phpMailer для отправки сообщений.
     *
     * @var \PHPMailer\PHPMailer\PHPMailer
     */
    protected $mailer = null;
    /**
     * Флаг, который указывает, что общие настройки установлены настройки.
     *
     * @var bool
     */
    protected $optionsSetted = false;

    /**
     * Конструктор.
     *
     * @param \PHPMailer\PHPMailer\PHPMailer $mailer
     */
    public function __construct(PhpMailerLib $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @inheritdoc
     */
    public function send(MessageInterface $message)
    {
        if (!$this->optionsSetted) {
            $this->setHandlerSettings($this->mailer);
        }

        $return = false;
        if (function_exists('mb_internal_encoding') && ((int) ini_get('mbstring.func_overload')) & 2) {
            $mbEncoding = mb_internal_encoding();
            mb_internal_encoding('ASCII');
        }

        try {
            $return = $this->setMessageSettings($this->mailer, $message)->send();
        } catch (\PHPMailer\PHPMailer\Exception $e) {
            $return = false;
        } finally {
            if (isset($mbEncoding)) {
                mb_internal_encoding($mbEncoding);
            }
        }

        if (!$return) {
            throw new Exception(
                "PhpMailer error: {$this->mailer->ErrorInfo}"
            );
        }

        return $return;
    }

    /**
     * Задает настройки нового сообщения для мэйлера.
     *
     * @param \PHPMailer\PHPMailer\PHPMailer       $mailer
     * @param \marvin255\bxmailer\MessageInterface $message
     *
     * @return \PHPMailer\PHPMailer\PHPMailer
     */
    protected function setMessageSettings(PhpMailerLib $mailer, MessageInterface $message)
    {
        $mailer = $this->resetMessageSettings($mailer);

        foreach ($message->getTo() as $address) {
            $mailer->addAddress($address);
        }
        foreach ($message->getCc() as $address) {
            $mailer->addCC($address);
        }
        foreach ($message->getBcc() as $address) {
            $mailer->addBCC($address);
        }
        foreach ($message->getAdditionalHeaders() as $key => $value) {
            $mailer->addCustomHeader("{$key}: {$value}");
        }
        if ($message->getReplyTo()) {
            $mailer->addReplyTo($message->getReplyTo());
        }
        if ($message->getFrom()) {
            $mailer->setFrom($message->getFrom());
        }

        $mailer->isHTML($message->isHtml());
        $mailer->Subject = $message->getSubject();
        $mailer->Body = $message->getMessage();

        return $mailer;
    }

    /**
     * Удаляет настройки предыдущего сообщения из объекта мэйлера.
     *
     * @param \PHPMailer\PHPMailer\PHPMailer $mailer
     *
     * @return \PHPMailer\PHPMailer\PHPMailer
     */
    protected function resetMessageSettings(PhpMailerLib $mailer)
    {
        $mailer->clearAddresses();
        $mailer->clearCCs();
        $mailer->clearBCCs();
        $mailer->clearReplyTos();
        $mailer->clearAllRecipients();
        $mailer->clearCustomHeaders();
        $mailer->From = '';
        $mailer->FromName = '';
        $mailer->Subject = '';
        $mailer->Body = '';

        return $mailer;
    }

    /**
     * Устанавливает общие настройки для отправки каждого письма.
     *
     * @param \PHPMailer\PHPMailer\PHPMailer $mailer
     *
     * @return \PHPMailer\PHPMailer\PHPMailer
     */
    protected function setHandlerSettings(PhpMailerLib $mailer)
    {
        $mailer->CharSet = $this->charset;
        if ((bool) $this->is_smtp) {
            $mailer->isSMTP();
            $mailer->Timeout = (int) $this->smtp_timeout;
            $mailer->Host = (string) $this->smtp_host;
            $mailer->Username = (string) $this->smtp_login;
            $mailer->Password = (string) $this->smtp_password;
            $mailer->Port = (int) $this->smtp_port;
            $mailer->SMTPSecure = (string) $this->smtp_secure;
            $mailer->SMTPAuth = (bool) $this->smtp_auth;
            if (!((bool) $this->smtp_auth)) {
                $mailer->SMTPAutoTLS = false;
            }
            if ((int) $this->smtp_debug > 0) {
                $mailer->SMTPDebug = (int) $this->smtp_debug;
            }
        }

        return $mailer;
    }
}
