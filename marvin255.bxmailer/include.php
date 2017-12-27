<?php

use Bitrix\Main\Event;
use marvin255\bxmailer\Autoloader;
use marvin255\bxmailer\Mailer;
use marvin255\bxmailer\handler\PhpMailer as PHPMailerHandler;
use marvin255\bxmailer\message\Bxmail;
use marvin255\bxmailer\options\Bitrix as BitrixOptions;
use PHPMailer\PHPMailer\PHPMailer;

//используем свой автолоадер, который соответствует psr
require_once __DIR__ . '/lib/Autoloader.php';
Autoloader::register('\\marvin255\\bxmailer', __DIR__ . '/lib');
Autoloader::register('\\PHPMailer\\PHPMailer', __DIR__ . '/phpmailer');

//если кастомная отправка уже определена, то ничего не делаем
if (!function_exists('custom_mail')) {
    define('MARVIN255_BXMAILER_IS_CUSTOM_MAIL_SET', true);

    //запускаем событие, чтобы дать возможность другому модулю прописать свой обработчик
    $mailer = Mailer::getInstance();
    $event = new Event('marvin255.bxmailer', 'createHandler', ['mailer' => $mailer]);
    $event->send();

    //определяем в модуле кастомную функцию для отправки писем
    function custom_mail($to, $subject, $message, $additional_headers, $additional_parameters)
    {
        $mailer = Mailer::getInstance();

        //если обработчик не задан, то устанавливаем по умолчанию обертку над phpMailer
        if (!$mailer->getHandler()) {
            $mailer->setHandler(new PHPMailerHandler(
                new PHPMailer(true),
                new BitrixOptions('marvin255.bxmailer')
            ));
        }

        $message = new Bxmail(
            $to,
            $subject,
            $message,
            $additional_headers,
            $additional_parameters
        );

        return $mailer->send($message);
    }
}
