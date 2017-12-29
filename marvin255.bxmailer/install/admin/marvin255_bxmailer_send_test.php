<?php

$path = $_SERVER['DOCUMENT_ROOT'] . '/local/modules/marvin255.bxmailer/admin/marvin255_bxmailer_send_test.php';
if (!file_exists($path)) {
    $path = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/marvin255.bxmailer/admin/marvin255_bxmailer_send_test.php';
}

require_once $path;
