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

//запускаем событие, чтобы дать возможность другому модулю прописать свой обработчик
try {
    $mailer = Mailer::getInstance();
    $event = new Event('marvin255.bxmailer', 'createHandler', ['mailer' => $mailer]);
    $event->send();
    //если обработчик не задан, то устанавливаем по умолчанию обертку над phpMailer
    if (!$mailer->getHandler()) {
        $mailer->setHandler(new PHPMailerHandler(
            new PHPMailer(true),
            new BitrixOptions('marvin255.bxmailer')
        ));
    }
} catch (Exception $e) {
    CEventLog::add([
        'SEVERITY' => 'ERROR',
        'AUDIT_TYPE_ID' => 'bxmailer_initialize_error',
        'MODULE_ID' => 'marvin255.bxmailer',
        'ITEM_ID' => '',
        'DESCRIPTION' => json_encode([
            'class' => get_class($e),
            'message' => $e->getMessage(),
            'code' => $e->getCode(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ], JSON_UNESCAPED_UNICODE),
    ]);
    define('MARVIN255_BXMAILER_NO_INJECT', true);
}

//если кастомная отправка уже определена, то ничего не делаем
if (!function_exists('custom_mail') && !defined('MARVIN255_BXMAILER_NO_INJECT')) {
    define('MARVIN255_BXMAILER_IS_CUSTOM_MAIL_SET', true);
    //определяем кастомную функцию для отправки писем
    function custom_mail($to, $subject, $message, $additional_headers, $additional_parameters)
    {
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
