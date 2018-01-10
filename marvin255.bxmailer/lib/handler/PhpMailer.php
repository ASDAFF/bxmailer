<?php

namespace marvin255\bxmailer\handler;

use PHPMailer\PHPMailer\PHPMailer as PhpMailerLib;
use marvin255\bxmailer\HandlerInterface;
use marvin255\bxmailer\HandlerDebugInterface;
use marvin255\bxmailer\MessageInterface;
use marvin255\bxmailer\OptionsInterface;
use marvin255\bxmailer\Exception;

/**
 * Класс для отправки писем с помощью phpmailer.
 */
class PhpMailer implements HandlerInterface, HandlerDebugInterface
{
    /**
     * Объект phpMailer для отправки сообщений.
     *
     * @var \PHPMailer\PHPMailer\PHPMailer
     */
    protected $mailer = null;
    /**
     * Объект для передачи настроек из модуля в phpMailer.
     *
     * @var \marvin255\bxmailer\OptionsInterface
     */
    protected $options = null;
    /**
     * Флаг, который указывает, что режим отладки включен.
     *
     * @var bool
     */
    protected $debug = false;

    /**
     * Конструктор.
     *
     * @param \PHPMailer\PHPMailer\PHPMailer       $mailer
     * @param \marvin255\bxmailer\OptionsInterface $options
     */
    public function __construct(PhpMailerLib $mailer, OptionsInterface $options)
    {
        $this->mailer = $mailer;
        $this->options = $options;
    }

    /**
     * @inheritdoc
     */
    public function send(MessageInterface $message)
    {
        $return = false;

        if (function_exists('mb_internal_encoding') && ((int) ini_get('mbstring.func_overload')) & 2) {
            $mbEncoding = mb_internal_encoding();
            mb_internal_encoding('ASCII');
        }

        try {
            $this->setHandlerSettings($this->mailer, $this->options);
            $this->setMessageSettings($this->mailer, $message);
            $return = $this->mailer->send();
        } catch (\PHPMailer\PHPMailer\Exception $e) {
            $return = false;
        } finally {
            if (isset($mbEncoding)) {
                mb_internal_encoding($mbEncoding);
            }
            if ($this->options->getBool('is_smtp', false)) {
                $this->mailer->smtpClose();
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
        foreach ($message->getAttachments() as $key => $value) {
            $mailer->addAttachment($value, $key);
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
        $mailer->clearAttachments();
        $mailer->From = '';
        $mailer->FromName = '';
        $mailer->Subject = '';
        $mailer->Body = '';

        return $mailer;
    }

    /**
     * Устанавливает общие настройки для отправки каждого письма.
     *
     * @param \PHPMailer\PHPMailer\PHPMailer       $mailer
     * @param \marvin255\bxmailer\OptionsInterface $options
     *
     * @return \PHPMailer\PHPMailer\PHPMailer
     */
    protected function setHandlerSettings(PhpMailerLib $mailer, OptionsInterface $options)
    {
        $mailer->CharSet = $options->get('charset', 'UTF-8');
        if ($options->getBool('is_smtp', false)) {
            $mailer->isSMTP();
            $mailer->Timeout = $options->getInt('smtp_timeout', 15);
            $mailer->Host = $options->get('smtp_host', '');
            $mailer->Username = $options->get('smtp_login', '');
            $mailer->Password = $options->get('smtp_password', '');
            $mailer->Port = $options->get('smtp_port', null);
            $mailer->SMTPSecure = $options->get('smtp_secure', null);
            $mailer->SMTPAuth = $options->getBool('smtp_auth', false);
            if (!$options->getBool('smtp_auth', false)) {
                $mailer->SMTPAutoTLS = false;
            }
            if ($this->getDebug()) {
                $this->mailer->SMTPDebug = 2;
                $this->mailer->Debugoutput = 'echo';
            } else {
                $this->mailer->SMTPDebug = 0;
            }
        } else {
            $mailer->isMail();
        }

        return $mailer;
    }

    /**
     * @inheritdoc
     */
    public function setDebug($debugMode = true)
    {
        $this->debug = (bool) $debugMode;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getDebug()
    {
        return $this->debug;
    }
}
