<?php

namespace marvin255\bxmailer\handler;

use PHPMailer\PHPMailer\PHPMailer as PhpMailerLib;
use marvin255\bxmailer\HandlerInterface;
use marvin255\bxmailer\MessageInterface;
use marvin255\bxmailer\OptionsInterface;
use marvin255\bxmailer\Exception;

/**
 * Класс для отправки писем с помощью phpmailer.
 */
class PhpMailer implements HandlerInterface
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
     * Флаг, который указывает, что объект мэйлера настроен.
     *
     * @var bool
     */
    protected $isSetHandlerSettings = false;

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
        if (!$this->isSetHandlerSettings) {
            $this->isSetHandlerSettings = true;
            $this->setHandlerSettings($this->mailer, $this->options);
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
            if ($options->getInt('smtp_debug', 0) > 0) {
                $mailer->SMTPDebug = $options->getInt('smtp_debug', 0);
            }
        }

        return $mailer;
    }
}
