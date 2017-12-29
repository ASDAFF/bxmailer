<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;
use marvin255\bxmailer\Mailer;
use marvin255\bxmailer\message\ArrayBased;

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog.php';

Loc::loadMessages(__FILE__);

define('MARVIN255_BXMAILER_NO_INJECT', true);
if (!Loader::includeModule('marvin255.bxmailer')) {
    throw new Exception('Can\'t find marvin255.bxmailer module');
}

global $USER;
if (!$USER->isAdmin()) {
    throw new Exception('Access denied');
}

$posted = Application::getInstance()
    ->getContext()
    ->getRequest()
    ->getPostList()
    ->toArray();

$res = [
    'status' => false,
    'error' => 'Не указаны входящие данные',
    'printed_data' => '',
];

if (!empty($posted['to']) && !empty($posted['subject']) && check_bitrix_sessid()) {
    $mailer = Mailer::getInstance();
    $message = new ArrayBased([
        'to' => [$posted['to']],
        'subject' => isset($posted['subject']) ? $posted['subject'] : '',
        'message' => isset($posted['message']) ? $posted['message'] : '',
        'isHtml' => !empty($posted['isHtml']),
        'from' => empty($posted['from']) ? '' : $posted['from'],
    ]);
    ob_start();
    ob_implicit_flush(false);
    $res['status'] = $mailer->send($message, true);
    $res['error'] = $mailer->getLastError();
    $res['printed_data'] = ob_get_clean();
}

echo json_encode($res);

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin_js.php';
