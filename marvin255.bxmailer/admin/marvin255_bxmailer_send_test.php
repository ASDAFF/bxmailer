<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;
use marvin255\bxmailer\Mailer;
use marvin255\bxmailer\message\ArrayBased;
use marvin255\bxmailer\HandlerDebugInterface;

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

$root = Application::getInstance()
    ->getContext()
    ->getServer()
    ->getDocumentRoot();

$posted = Application::getInstance()
    ->getContext()
    ->getRequest()
    ->getPostList()
    ->toArray();

$res = [
    'status' => false,
    'error' => Loc::getMessage('MARVIN255_BXMAILER_PREFERENCIES_SEND_TEST_NO_DATA'),
    'printed_data' => '',
];

if (!empty($posted['to']) && !empty($posted['subject']) && check_bitrix_sessid()) {
    $mailer = Mailer::getInstance();
    if ($mailer->getHandler() instanceof HandlerDebugInterface) {
        $mailer->getHandler()->setDebug();
    }
    $message = new ArrayBased([
        'to' => array_map('trim', explode(',', $posted['to'])),
        'subject' => isset($posted['subject']) ? $posted['subject'] : '',
        'message' => isset($posted['message']) ? $posted['message'] : '',
        'isHtml' => !empty($posted['isHtml']),
        'from' => empty($posted['from']) ? '' : $posted['from'],
        'attachments' => empty($posted['attachment'])
            ? []
            : ['тестовый файл.' . pathinfo($posted['attachment'], PATHINFO_EXTENSION) => $root . $posted['attachment']],
    ]);
    ob_start();
    ob_implicit_flush(false);
    try {
        $res['status'] = $mailer->send($message, true);
        $res['error'] = '';
    } catch (\Exception $e) {
        $res['error'] = $e->getMessage();
    }
    $res['printed_data'] = ob_get_clean();
}

echo json_encode($res);

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin_js.php';
