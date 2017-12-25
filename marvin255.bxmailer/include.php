<?php

use Bitrix\Main\Event;
use Bitrix\Main\Config\Option;
use marvin255\bxmailer\Exception;
use marvin255\bxmailer\Mailer;
use marvin255\bxmailer\PhpMailer;

//получаем от битрикса исходные данные
$moduleId = 'marvin255.bxmailer';
$options = Option::getForModule($moduleId);
$mailer = Mailer::getInstance()->setOptions($options);

//запускаем событие, чтобы дать возможность другому модулю прописать свой обработчик
$event = new Event($moduleId, 'createHandler', ['mailer' => $mailer]);
$event->send();
foreach ($event->getResults() as $eventResult) {
    if ($eventResult->getType() === EventResult::ERROR) {
        throw new Exception(
            'Get error while create handler: '
            . implode(',', $eventResult->getErrorMessages())
        );
    }
}

//если обработчик не задан, то устанавливаем по умолчанию обертку над phpMailer
if (!$mailer->getHandler()) {
    $mailer->setHandler(new PhpMailer);
}

//определяем в модуле кастомную функцию для отправки писем
if (!function_exists('custom_mail')) {
    function custom_mail($to, $subject, $message, $additional_headers, $additional_parameters)
    {
        return Mailer::getInstance()->send(
            $to,
            $subject,
            $message,
            $additional_headers,
            $additional_parameters
        );
    }
    $mailer->isRan();
}
