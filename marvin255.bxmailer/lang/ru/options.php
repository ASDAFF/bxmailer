<?php

$MESS['MARVIN255_BXMAILER_TAB_TEST'] = 'Отправить тестовое сообщение';
$MESS['MARVIN255_BXMAILER_PREFERENCIES_SMTP_LOGIN'] = 'Логин для smtp';
$MESS['MARVIN255_BXMAILER_PREFERENCIES_SMTP_PASSWORD'] = 'Пароль для smtp';
$MESS['MARVIN255_BXMAILER_PREFERENCIES_SMTP_HOST'] = 'Хост для smtp';
$MESS['MARVIN255_BXMAILER_PREFERENCIES_SMTP_SECURE'] = 'Тип шифрования для smtp';
$MESS['MARVIN255_BXMAILER_PREFERENCIES_SMTP_PORT'] = 'Порт для smtp';
$MESS['MARVIN255_BXMAILER_PREFERENCIES_CHARSET'] = 'Кодировка';
$MESS['MARVIN255_BXMAILER_PREFERENCIES_USE_SMTP'] = 'Использовать smtp';
$MESS['MARVIN255_BXMAILER_PREFERENCIES_SMTP_AUTH'] = 'Использовать авторизацию для smtp';

$MESS['MARVIN255_BXMAILER_PRESENTED_BY_PHPMAILER'] = 'В основе модуля лежит библиотека <a href="https://github.com/PHPMailer/PHPMailer">phpMailer</a>.'
    . ' Автор модуля выражает огромную признательность сообществу phpMailer.';

$MESS['MARVIN255_BXMAILER_MODULE_IS_UNPLUGGED'] = '<b>Отправка писем через модуль не настроена</b><br>'
    . '<div style="font-weight: normal;">Убедитесь, что в вашем init.php прописано подключение модуля:<br>'
    . '\\Bitrix\\Main\\Loader::includeModule(\'marvin255.bxmailer\');<br>'
    . 'или проверьте журнал событий на наличие ошибок "<a href="http://stroitel-dev.ru/bitrix/admin/event_log.php?set_filter=Y&adm_filter_applied=0&find=bxmailer_initialize_error&find_type=audit_type_id">bxmailer_initialize_error</a>"'
    . '</div>';

$MESS['MARVIN255_BXMAILER_SMTP'] = '<table><tr>'
    . '<td style="padding-right: 40px;">'
    . '<b>Настройка smtp mail.ru:</b><br>'
    . 'Хост для smtp: smtp.mail.ru<br>'
    . 'Тип шифрования для smtp: ssl<br>'
    . 'Порт для smtp: 465<br>'
    . 'Использовать авторизацию для smtp: да'
    . '</td>'
    . '<td style="padding-right: 40px;">'
    . '<b>Настройка smtp yandex.ru:</b><br>'
    . 'Хост для smtp: smtp.yandex.ru<br>'
    . 'Тип шифрования для smtp: ssl<br>'
    . 'Порт для smtp: 465<br>'
    . 'Использовать авторизацию для smtp: да'
    . '</td>'
    . '<td>'
    . '<b>Настройка smtp gmail.com:</b><br>'
    . 'Хост для smtp: smtp.gmail.com<br>'
    . 'Тип шифрования для smtp: tls<br>'
    . 'Порт для smtp: 587<br>'
    . 'Использовать авторизацию для smtp: да'
    . '</td>'
    . '</tr></table>';

$MESS['REFERENCES_OPTIONS_RESTORED'] = 'Восстановлены настройки по умолчанию';
$MESS['REFERENCES_OPTIONS_SAVED'] = 'Настройки сохранены';
$MESS['REFERENCES_INVALID_VALUE'] = 'Введено неверное значение';
