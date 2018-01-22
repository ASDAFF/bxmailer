<?php

namespace marvin255\bxmailer\message;

use marvin255\bxmailer\MessageInterface;
use marvin255\bxmailer\Exception;

/**
 * Класс для сообщения из параметров, которые приходят в bxmail.
 */
class Bxmail implements MessageInterface
{
    /**
     * Строка с адресатами сообщения.
     *
     * @var string
     */
    protected $to = null;
    /**
     * Строка с темой сообщения.
     *
     * @var string
     */
    protected $subject = null;
    /**
     * Строка с текстом сообщения.
     *
     * @var string
     */
    protected $message = null;
    /**
     * Строка с заголовками сообщения.
     *
     * @var string
     */
    protected $additional_headers = null;
    /**
     * Строка с дополнительными параметрами командной строки.
     *
     * @var string
     */
    protected $additional_parameters = null;
    /**
     * Массив с обработанными для вывода данными.
     *
     * @var string
     */
    protected $handled = [];

    /**
     * Конструктор. Записывает параметры, которые приходят в функцию bxmail
     * в объект сообщения.
     *
     * @param string $to
     * @param string $subject
     * @param string $message
     * @param string $additional_headers
     * @param string $additional_parameters
     */
    public function __construct($to, $subject, $message, $additional_headers = '', $additional_parameters = '')
    {
        $this->to = $to;
        $this->subject = $subject;
        $this->message = $message;
        $this->additional_headers = $additional_headers;
        $this->additional_parameters = $additional_parameters;
    }

    /**
     * @inheritdoc
     */
    public function getTo()
    {
        if (!isset($this->handled['to'])) {
            $this->handled['to'] = array_map(
                'trim',
                explode(',', $this->to)
            );
        }

        return $this->handled['to'];
    }

    /**
     * @inheritdoc
     */
    public function getCc()
    {
        if (!isset($this->handled['cc'])) {
            $this->handled['cc'] = [];
            if ($bcc = $this->searchHeader('CC')) {
                $this->handled['cc'] = array_map('trim', explode(',', $bcc));
            }
        }

        return $this->handled['cc'];
    }

    /**
     * @inheritdoc
     */
    public function getBcc()
    {
        if (!isset($this->handled['bcc'])) {
            $this->handled['bcc'] = [];
            if ($bcc = $this->searchHeader('BCC')) {
                $this->handled['bcc'] = array_map('trim', explode(',', $bcc));
            }
        }

        return $this->handled['bcc'];
    }

    /**
     * @inheritdoc
     */
    public function getFrom()
    {
        if (!isset($this->handled['from'])) {
            $from = $this->searchHeader('From');
            $this->handled['from'] = $from ?: '';
        }

        return $this->handled['from'];
    }

    /**
     * @inheritdoc
     */
    public function getReplyTo()
    {
        if (!isset($this->handled['replyTo'])) {
            $from = $this->searchHeader('Reply-To');
            $this->handled['replyTo'] = $from ?: '';
        }

        return $this->handled['replyTo'];
    }

    /**
     * @inheritdoc
     */
    public function getSubject()
    {
        if (!isset($this->handled['subject'])) {
            $this->handled['subject'] = $this->decodeMimeHeader($this->subject);
        }

        return $this->handled['subject'];
    }

    /**
     * @inheritdoc
     */
    public function getMessage()
    {
        if (!isset($this->handled['message'])) {
            $this->handled['message'] = '';
            $contentType = $this->searchHeader('Content-Type');
            //если в теле письма прописаны вложения, то пробуем получить только текст
            if (preg_match('/^\s*multipart\/mixed.*boundary="(.+)"\s*$/i', $contentType, $matches)) {
                $arParts = $this->parseMultipartMessage($this->message, $matches[1]);
                foreach ($arParts as $part) {
                    if (
                        !empty($part['headers']['Content-Transfer-Encoding'])
                        && $part['headers']['Content-Transfer-Encoding'] === 'base64'
                    ) {
                        continue;
                    }
                    $this->handled['message'] .= $part['content'];
                }
            } else {
                $this->handled['message'] = $this->message;
            }
        }

        return $this->handled['message'];
    }

    /**
     * @inheritdoc
     */
    public function isHtml()
    {
        if (!isset($this->handled['isHtml'])) {
            $contentType = $this->searchHeader('Content-Type');
            $this->handled['isHtml'] = $contentType
                && strpos($contentType, 'text/html') !== false;
        }

        return $this->handled['isHtml'];
    }

    /**
     * @inheritdoc
     */
    public function getAdditionalHeaders()
    {
        if (!isset($this->handled['additionalHeaders'])) {
            $this->handled['additionalHeaders'] = [];
            $disallowedHeaders = [
                'cc',
                'bcc',
                'from',
                'reply-to',
                'content-type',
                'content-transfer-encoding',
                'add-file',
            ];
            $allHeaders = $this->getAllHeaders();
            foreach ($allHeaders as $name => $value) {
                if (in_array(mb_strtolower($name), $disallowedHeaders)) {
                    continue;
                }
                $this->handled['additionalHeaders'][$name] = $value;
            }
        }

        return $this->handled['additionalHeaders'];
    }

    /**
     * {@inheritdoc}
     *
     * @throws \marvin255\bxmailer\Exception
     */
    public function getAttachments()
    {
        if (!isset($this->handled['attachments'])) {
            $attachments = $this->searchHeader('add-file');
            $this->handled['attachments'] = [];
            if ($attachments) {
                foreach (explode(';', $attachments) as $attachment) {
                    $arAttachment = explode('=>', $attachment);
                    if (!empty($arAttachment[0]) && !empty($arAttachment[1])) {
                        $this->handled['attachments'][$arAttachment[1]] = $arAttachment[0];
                    }
                }
            }

            $contentType = $this->searchHeader('Content-Type');
            //если в теле письма прописаны вложения, то преобразуем их обратно в файлы
            if (preg_match('/^\s*multipart\/mixed.*boundary="(.+)"\s*$/i', $contentType, $matches)) {
                $arParts = $this->parseMultipartMessage($this->message, $matches[1]);
                foreach ($arParts as $part) {
                    if (
                        empty($part['headers']['Content-Transfer-Encoding'])
                        || $part['headers']['Content-Transfer-Encoding'] !== 'base64'
                    ) {
                        continue;
                    }
                    if (preg_match('/.*\s*name="(.+)"\s*/i', $part['headers']['Content-Type'], $matches)) {
                        $name = $this->decodeMimeHeader($matches[1]);
                    } else {
                        $name = mt_rand();
                    }
                    $file = tempnam(sys_get_temp_dir() ?: '/tmp', 'bxmailer');
                    if (file_put_contents($file, base64_decode($part['content'])) === false) {
                        throw new Exception(
                            "Can't write attachment content to temp file: {$file}"
                        );
                    }
                    $this->handled['attachments'][$name] = $file;
                }
            }
        }

        return $this->handled['attachments'];
    }

    /**
     * Возвращает все заголовки письма, которые выставил битрикс.
     *
     * @return array
     */
    protected function getAllHeaders()
    {
        if (!isset($this->handled['allHeaders'])) {
            $this->handled['allHeaders'] = $this->parseHeaders($this->additional_headers);
        }

        return $this->handled['allHeaders'];
    }

    /**
     * Возвращает значение заголовка по его имени, если он указан.
     *
     * @param string $name
     *
     * @return string|null
     */
    protected function searchHeader($name)
    {
        $return = null;
        $name = mb_strtolower($name);
        foreach ($this->getAllHeaders() as $headerName => $headerValue) {
            if (mb_strtolower($headerName) !== $name) {
                continue;
            }
            $return = $headerValue;
            break;
        }

        return $return;
    }

    /**
     * Разбирает заголовки из строки в массив.
     *
     * @param string $headers
     *
     * @return array
     */
    protected function parseHeaders($headers)
    {
        $return = [];

        $explode = explode("\n", $headers);
        foreach ($explode as $strHeader) {
            if (preg_match('/^([^\:]+)\:(.*)$/', $strHeader, $matches)) {
                $key = trim($matches[1]);
                $value = trim($matches[2]);
                $return[$key] = $this->decodeMimeHeader($value);
            }
        }

        return $return;
    }

    /**
     * Декодирует mime заголовок.
     *
     * @param string $header
     *
     * @return string
     */
    protected function decodeMimeHeader($header)
    {
        if (preg_match('/^=\?.+\?=/', $header)) {
            $return = mb_decode_mimeheader(trim($header));
        } else {
            $return = $header;
        }

        return $return;
    }

    /**
     * Разбирает сообщение, в котором есть вложения.
     *
     * @param string $message
     * @param string $boundary
     *
     * @return string
     *
     * @throws \marvin255\bxmailer\Exception
     */
    protected function parseMultipartMessage($message, $boundary)
    {
        $boundary = "--{$boundary}";
        $return = [];
        $arParts = explode($boundary, $message);
        $arParts = array_diff(array_map('trim', $arParts), ['', '--']);
        foreach ($arParts as $part) {
            $arPart = explode("\n\n", $part, 2);
            if (!isset($arPart[0], $arPart[1])) {
                throw new Exception("Can't parse multipart/mixed format in message");
            }
            $return[] = [
                'headers' => $this->parseHeaders($arPart[0]),
                'content' => $arPart[1],
            ];
        }

        return $return;
    }
}
