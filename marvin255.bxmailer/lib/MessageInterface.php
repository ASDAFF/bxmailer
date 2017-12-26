<?php

namespace marvin255\bxmailer;

/**
 * Интерфейс для сообщения, которое нужно будет отправить.
 */
interface MessageInterface
{
    /**
     * Возвращает массив адресатов.
     *
     * @return array
     */
    public function getTo();

    /**
     * Возвращает массив адресатов для копии письма.
     *
     * @return array
     */
    public function getCc();

    /**
     * Возвращает массив скрытых адресатов.
     *
     * @return array
     */
    public function getBcc();

    /**
     * Возвращает адрес отправителя.
     *
     * @return array
     */
    public function getFrom();

    /**
     * Возвращает адрес, на который нужно отправить ответ.
     *
     * @return array
     */
    public function getReplyTo();

    /**
     * Возвращает тему письма.
     *
     * @return string
     */
    public function getSubject();

    /**
     * Возвращает текст сообщения.
     *
     * @return string
     */
    public function getMessage();

    /**
     * Возвращает флаг используется ли html в содержимом письма.
     *
     * @return bool
     */
    public function isHtml();

    /**
     * Возвращает массив с дополнительными заголовками, которые нужно установить
     * для письма. В формате "Имя заголовка => Значение".
     *
     * @return array
     */
    public function getAdditionalHeaders();
}
