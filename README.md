# Bxmailer

[![Build Status](https://travis-ci.org/marvin255/bxmailer.svg?branch=master)](https://travis-ci.org/marvin255/bxmailer)

Отправка почтовых сообщений через SMTP (phpMailer) для 1С-Битрикс "Управление сайтом".



## Оглавление

* [Установка](#Установка).
* [Настройка](#Настройка).
* [Замена транспорта](#Замена-транспорта).
* [Замена сообщения](#Замена-сообщения).
* [Обработка ошибок](#Обработка-ошибок).
* [Благодарности](#Благодарности).



## Установка

**С помощью [Composer](https://getcomposer.org/doc/00-intro.md)**

1. Добавьте в ваш composer.json в раздел `require`:

    ```javascript
    "require": {
        "marvin255/bxmailer": "*"
    }
    ```

2. Если требуется автоматическое обновление библиотеки через composer, то добавьте в раздел `scripts`:

    ```javascript
    "scripts": {
        "post-install-cmd": [
            "\\marvin255\\bxmailer\\installer\\Composer::injectModule"
        ],
        "post-update-cmd": [
            "\\marvin255\\bxmailer\\installer\\Composer::injectModule"
        ]
    }
    ```

3. Выполните в консоли внутри вашего проекта:

    ```
    composer update
    ```

4. Если пункт 2 не выполнен, то скопируйте папку `vendor/marvin255/bxfoundation/marvin255.bxmailer` в папку `local/modules` вашего проекта. А папку `vendor/phpmailer/phpmailer` в папку `local/modules/marvin255.bxmailer/phpmailer`.

5. Установите модуль в административном разделе 1С-Битрикс "Управление сайтом".

6. Добавьте строку `\Bitrix\Main\Loader::includeModule('marvin255.bxmailer');` в `init.php` вашего сайта.

**Обычная**

1. Скачайте архив с репозиторием.
2. Скопируйте папку `vendor/marvin255/bxfoundation/marvin255.bxmailer` в папку `local/modules` вашего проекта. А папку `vendor/phpmailer/phpmailer` в папку `local/modules/marvin255.bxmailer/phpmailer`.
3. Установите модуль в административном разделе 1С-Битрикс "Управление сайтом".
4. Добавьте строку `\Bitrix\Main\Loader::includeModule('marvin255.bxmailer');` в `init.php` вашего сайта.



## Настройка

Smtp настраивается через настройки модуля в административной части. Там же можно протестировать отправку сообщений без необходимости подмены стандартной отправки. Подмена стандартной отправки Битрикса происходит только после подключения модуля с помощью `\Bitrix\Main\Loader::includeModule('marvin255.bxmailer');`. Если требуется подключить модуль без подмены стандартной отправки, то можно использовать константу:

```php
define('MARVIN255_BXMAILER_NO_INJECT', true);
if (!\Bitrix\Main\Loader::includeModule('marvin255.bxmailer')) {
    throw new Exception("Can't find marvin255.bxmailer module");
}
```



## Замена транспорта

В основе модуля лежит библиотека [phpMailer](https://github.com/PHPMailer/PHPMailer). Соответственно отправка сообщений осуществляется с помощью phpMailer. Для того, чтобы заменить phpMailer на любой другой транспорт можно использовать событие:

```php
use Bitrix\Main\EventManager;
use Bitrix\Main\Event;

EventManager::getInstance()->addEventHandler('marvin255.bxmailer', 'createHandler', 'createHandlerHandler');
function createHandlerHandler(Event $event)
{
    $event->getParameter('mailer')->setHandler(new MyAwesomeHandler);
}
```

Для того, чтобы все заработало, класс `MyAwesomeHandler` должен реализовывать интерфейс [`\marvin255\bxmailer\HandlerInterface`](https://github.com/marvin255/bxmailer/blob/master/marvin255.bxmailer/lib/HandlerInterface.php).



## Замена сообщения

Данные письма для транспорта передаются через объект сообщения, который тоже можно заменить с помощью события. Вместе с событием так же передается объект оригинального сообщения, а также все исходные параметры письма.

```php
use Bitrix\Main\EventManager;
use Bitrix\Main\Event;

EventManager::getInstance()->addEventHandler('marvin255.bxmailer', 'createMessage', 'createMessageHandler');
function createMessageHandler(Event $event)
{
    //$event->getParameter('messageContainer');
    //$event->getParameter('to');
    //$event->getParameter('subject');
    //$event->getParameter('message');
    //$event->getParameter('additional_headers');
    //$event->getParameter('additional_parameters');
    $event->setParameter('messageContainer', new MyAwesomeMessage);
}
```

Для того, чтобы все заработало, класс `MyAwesomeMessage` должен реализовывать интерфейс [`\marvin255\bxmailer\MessageInterface`](https://github.com/marvin255/bxmailer/blob/master/marvin255.bxmailer/lib/MessageInterface.php).



## Обработка ошибок

Модуль перехватывает все исключения, чтобы не прерывать процесс инициализации сайта. Тем не менее, все исключения будут записаны в журнал ошибок.

Модуль логирует два типа ошибок:

* bxmailer_initialize_error - ошибки при инициализации модуля,
* bxmailer_send_error - ошибки при отправке сообщения.

Если требуется прервать работу модуля во время одного из событий, то следует выбросить исключение, унаследованное от `\marvin255\bxmailer\Exception`.

**Внимание** ошибки внутри событий, которые невозможно перехватить обработаны не будут.



## Благодарности

В основе модуля лежит библиотека [phpMailer](https://github.com/PHPMailer/PHPMailer). Автор модуля выражает огромную признательность сообществу phpMailer.
