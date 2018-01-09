<?php

namespace marvin255\bxmailer\message;

use marvin255\bxmailer\MessageInterface;

/**
 * Класс для сообщения из параметров, которые задаются с помощью массива.
 */
class ArrayBased implements MessageInterface
{
    /**
     * Массив с данными для вывода.
     *
     * @var string
     */
    protected $messageData = [];

    /**
     * Конструктор. Задает начальные данные.
     *
     * @param array $messageData
     */
    public function __construct(array $messageData)
    {
        $this->messageData = $messageData;
    }

    /**
     * @inheritdoc
     */
    public function getTo()
    {
        $res = isset($this->messageData['to'])
            ? $this->messageData['to']
            : null;

        return is_array($res) ? $res : [];
    }

    /**
     * @inheritdoc
     */
    public function getCc()
    {
        $res = isset($this->messageData['cc'])
            ? $this->messageData['cc']
            : null;

        return is_array($res) ? $res : [];
    }

    /**
     * @inheritdoc
     */
    public function getBcc()
    {
        $res = isset($this->messageData['bcc'])
            ? $this->messageData['bcc']
            : null;

        return is_array($res) ? $res : [];
    }

    /**
     * @inheritdoc
     */
    public function getFrom()
    {
        return isset($this->messageData['from'])
            ? $this->messageData['from']
            : '';
    }

    /**
     * @inheritdoc
     */
    public function getReplyTo()
    {
        return isset($this->messageData['replyTo'])
            ? $this->messageData['replyTo']
            : '';
    }

    /**
     * @inheritdoc
     */
    public function getSubject()
    {
        return isset($this->messageData['subject'])
            ? $this->messageData['subject']
            : '';
    }

    /**
     * @inheritdoc
     */
    public function getMessage()
    {
        return isset($this->messageData['message'])
            ? $this->messageData['message']
            : '';
    }

    /**
     * @inheritdoc
     */
    public function isHtml()
    {
        return isset($this->messageData['isHtml'])
            && (bool) $this->messageData['isHtml'];
    }

    /**
     * @inheritdoc
     */
    public function getAdditionalHeaders()
    {
        $res = isset($this->messageData['additionalHeaders'])
            ? $this->messageData['additionalHeaders']
            : null;

        return is_array($res) ? $res : [];
    }

    /**
     * @inheritdoc
     */
    public function getAttachments()
    {
        $res = isset($this->messageData['attachments'])
            ? $this->messageData['attachments']
            : null;

        return is_array($res) ? $res : [];
    }
}
