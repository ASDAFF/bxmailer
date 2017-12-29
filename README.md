# Bxmailer

[![Latest Stable Version](https://poser.pugx.org/marvin255/bxmailer/v/stable.png)](https://packagist.org/packages/marvin255/bxmailer)
[![Total Downloads](https://poser.pugx.org/marvin255/bxmailer/downloads.png)](https://packagist.org/packages/marvin255/bxmailer)
[![License](https://poser.pugx.org/marvin255/bxmailer/license.svg)](https://packagist.org/packages/marvin255/bxmailer)
[![Build Status](https://travis-ci.org/marvin255/bxmailer.svg?branch=master)](https://travis-ci.org/marvin255/bxmailer)

Отправка почтовых сообщений через SMTP (phpMailer) для 1С-Битрикс "Управление сайтом".



## Оглавление

* [Установка](#Установка).
* [Настройка](#Настройка).
* [Замена транспорта](#Замена-транспорта).
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
    "scripts": [
        {
            "post-install-cmd": "\\marvin255\\bxmailer\\installer\\Composer::injectModule",
            "post-update-cmd": "\\marvin255\\bxmailer\\installer\\Composer::injectModule",
        }
    ]
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

Smtp настраивается через настройки модуля в административной части. Там же можно протестировать отправку сообщений без необходимости подмены стандартной отправки.



## Замена транспорта

В основе модуля лежит библиотека [phpMailer](https://github.com/PHPMailer/PHPMailer). Соответственно отправка сообщений осуществляется с помощью phpMailer. Для того, чтобы заменить phpMailer на любой другой транспорт можно использовать событие:

```php
AddEventHandler('marvin255.bxmailer', 'createHandler', 'createHandlerHandler');
function createHandlerHandler($mailer)
{
    $mailer->setHandler(new MyAwesomeHandler);
}
```

Для того, чтобы все заработало, класс `MyAwesomeHandler` должен реализовывать интерфейс [`\marvin255\bxmailer\HandlerInterface`](https://github.com/marvin255/bxmailer/blob/master/marvin255.bxmailer/lib/HandlerInterface.php).



## Благодарности

В основе модуля лежит библиотека [phpMailer](https://github.com/PHPMailer/PHPMailer). Автор модуля выражает огромную признательность сообществу phpMailer.
