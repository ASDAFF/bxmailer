<?php

use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;

defined('ADMIN_MODULE_NAME') or define('ADMIN_MODULE_NAME', 'marvin255.bxmailer');
$module_id = ADMIN_MODULE_NAME;

if (!$USER->isAdmin()) {
    $APPLICATION->authForm(Loc::getMessage('ACCESS_DENIED'));
}

$app = Application::getInstance();
$context = $app->getContext();
$request = $context->getRequest();
CJSCore::Init(['jquery']);

Loc::loadMessages($context->getServer()->getDocumentRoot() . '/bitrix/modules/main/options.php');
Loc::loadMessages(__FILE__);

$tabControl = new CAdminTabControl('tabControl', [
    [
        'DIV' => 'edit1',
        'TAB' => Loc::getMessage('MAIN_TAB_SET'),
        'TITLE' => Loc::getMessage('MAIN_TAB_TITLE_SET'),
    ],
    [
        'DIV' => 'edit2',
        'TAB' => Loc::getMessage('MARVIN255_BXMAILER_TAB_TEST'),
        'TITLE' => Loc::getMessage('MARVIN255_BXMAILER_TAB_TEST'),
    ],
]);

$isConfigComplete = false;
if ((!empty($save) || !empty($restore)) && $request->isPost() && check_bitrix_sessid()) {
    if (!empty($restore)) {
        Option::delete($module_id);
    } else {
        $fields = [
            'is_smtp',
            'smtp_login',
            'smtp_password',
            'smtp_host',
            'smtp_secure',
            'smtp_port',
            'charset',
            'smtp_auth',
        ];
        foreach ($fields as $field) {
            if ($request->getPost($field) !== null) {
                Option::set(
                    $module_id,
                    $field,
                    $request->getPost($field)
                );
            }
        }
    }
    $isConfigComplete = true;
}
?>

<?php
    if (!defined('MARVIN255_BXMAILER_IS_CUSTOM_MAIL_SET')) {
        echo CAdminMessage::ShowMessage([
            'MESSAGE' => Loc::getMessage('MARVIN255_BXMAILER_MODULE_IS_UNPLUGGED'),
            'HTML' => true,
            'TYPE' => 'ERROR',
        ]);
    }
?>

<script>
    (function ($, $document) {

        $document.on('click', '.js-test-sender', function () {
            var $this = $(this);
            var endpointUrl = $this.attr('data-endpoint');
            var $result = $this.closest('table').find('.js-test-sender-result');
            var $sendData = $this.closest('form').serialize();

            if (!$this.prop('disabled')) {
                $this.closest('table').find('input').prop('disabled', true);
                $.ajax({
                    url: endpointUrl,
                    type: 'POST',
                    cache: false,
                    dataType: 'json',
                    data: $sendData
                }).done(function (response) {
                    $result.empty().attr(
                        'style',
                        'color: ' + (response.status ? 'green' : 'red')
                    );
                    var $message = $('<div />');
                    if (response.status) {
                        $message.html('<b>Сообщение отправлено</b>');
                    } else {
                        $message.text(response.error);
                    }
                    $message.appendTo($result);
                    if (response.printed_data) {
                        $('<p />').text(response.printed_data).appendTo($result);
                    }
                }).fail(function() {
                    $result.attr('style', 'color: red')
                        .empty()
                        .text('Во время ajax запроса произошла ошибка.');
                }).always(function() {
                    $this.closest('table').find('input').prop('disabled', false);
                });
            }

            return false;
        });

    })(jQuery, jQuery(document));
</script>

<?php
$tabControl->begin();
?>

<form method="post" action="<?php echo $APPLICATION->getCurPageParam('mid=' . urlencode($mid), ['mid']); ?>">
    <?php
        echo bitrix_sessid_post();
        $tabControl->beginNextTab();
    ?>
        <tr>
            <td width="40%">
                <label><?php echo Loc::getMessage('MARVIN255_BXMAILER_PREFERENCIES_CHARSET') ?>:</label>
            <td width="60%">
                <input type="text"
                       size="50"
                       name="charset"
                       value="<?php echo htmlentities(Option::get($module_id, 'charset', 'UTF-8')); ?>"
                       />
            </td>
        </tr>
        <tr>
            <td width="40%">
                <label><?php echo Loc::getMessage('MARVIN255_BXMAILER_PREFERENCIES_USE_SMTP') ?>:</label>
            <td width="60%">
                <input type="hidden" name="is_smtp" value="" />
                <input type="checkbox"
                       name="is_smtp"
                       value="1"
                       <?php echo Option::get($module_id, 'is_smtp') ? 'checked' : ''; ?>
                       />
            </td>
        </tr>
        <tr>
            <td width="40%">
                <label><?php echo Loc::getMessage('MARVIN255_BXMAILER_PREFERENCIES_SMTP_AUTH') ?>:</label>
            <td width="60%">
                <input type="hidden" name="smtp_auth" value="" />
                <input type="checkbox"
                       name="smtp_auth"
                       value="1"
                       <?php echo Option::get($module_id, 'smtp_auth') ? 'checked' : ''; ?>
                       />
            </td>
        </tr>
        <tr>
            <td width="40%">
                <label><?php echo Loc::getMessage('MARVIN255_BXMAILER_PREFERENCIES_SMTP_LOGIN') ?>:</label>
            <td width="60%">
                <input type="text"
                       size="50"
                       name="smtp_login"
                       value="<?php echo htmlentities(Option::get($module_id, 'smtp_login')); ?>"
                       />
            </td>
        </tr>
        <tr>
            <td width="40%">
                <label><?php echo Loc::getMessage('MARVIN255_BXMAILER_PREFERENCIES_SMTP_PASSWORD') ?>:</label>
            <td width="60%">
                <input type="text"
                       size="50"
                       name="smtp_password"
                       value="<?php echo htmlentities(Option::get($module_id, 'smtp_password')); ?>"
                       />
            </td>
        </tr>
        <tr>
            <td width="40%">
                <label><?php echo Loc::getMessage('MARVIN255_BXMAILER_PREFERENCIES_SMTP_HOST') ?>:</label>
            <td width="60%">
                <input type="text"
                       size="50"
                       name="smtp_host"
                       value="<?php echo htmlentities(Option::get($module_id, 'smtp_host')); ?>"
                       />
            </td>
        </tr>
        <tr>
            <td width="40%">
                <label><?php echo Loc::getMessage('MARVIN255_BXMAILER_PREFERENCIES_SMTP_PORT') ?>:</label>
            <td width="60%">
                <input type="text"
                       size="50"
                       name="smtp_port"
                       value="<?php echo htmlentities(Option::get($module_id, 'smtp_port')); ?>"
                       />
            </td>
        </tr>
        <tr>
            <td width="40%">
                <label><?php echo Loc::getMessage('MARVIN255_BXMAILER_PREFERENCIES_SMTP_SECURE') ?>:</label>
            <td width="60%">
                <select name="smtp_secure">
                    <option value="">Нет</option>
                    <option value="ssl"<?php echo Option::get($module_id, 'smtp_secure') === 'ssl' ? ' selected' : ''; ?>>
                        ssl
                    </option>
                    <option value="tls"<?php echo Option::get($module_id, 'smtp_secure') === 'tls' ? ' selected' : ''; ?>>
                        tls
                    </option>
                </select>
            </td>
        </tr>
    <?php
        $tabControl->beginNextTab();
    ?>
        <tr>
            <td width="40%">
                <label>
                    Email или список email'ов через запятую, на который отправить письмо
                </label>
            </td>
            <td width="60%">
                <input type="text" size="50" name="to" value="">
            </td>
        </tr>
        <tr>
            <td width="40%">
                <label>
                    Email отправителя
                </label>
            </td>
            <td width="60%">
                <input type="text" size="50" name="from" value="">
            </td>
        </tr>
        <tr>
            <td width="40%">
                <label>
                    Тема письма
                </label>
            </td>
            <td width="60%">
                <input type="text" size="50" name="subject" value="Тестовое сообщение из модуля marvin255.bxmailer">
            </td>
        </tr>
        <tr>
            <td width="40%">
                <label>
                    Html в сообщении
                </label>
            </td>
            <td width="60%">
                <input type="checkbox" name="isHtml" value="1">
            </td>
        </tr>
        <tr>
            <td width="40%">
                <label>
                    Сообщение
                </label>
            </td>
            <td width="60%">
                <input type="text" size="50" name="message" value="Тестовое сообщение из модуля marvin255.bxmailer">
            </td>
        </tr>
        <tr>
            <td width="40%">
            </td>
            <td width="60%">
                <input type="submit" class="js-test-sender" data-endpoint="/bitrix/admin/marvin255_bxmailer_send_test.php" value="Отправить">
            </td>
        </tr>
        <tr>
            <td width="40%" style="vertical-align: top;">
                <label>
                    Результат
                </label>
            </td>
            <td width="60%">
                <div class="js-test-sender-result">-</div>
            </td>
        </tr>
    <?php
        $tabControl->buttons();
    ?>
    <input type="submit"
           name="save"
           value="<?php echo Loc::getMessage('MAIN_SAVE'); ?>"
           title="<?php echo Loc::getMessage('MAIN_OPT_SAVE_TITLE'); ?>"
           class="adm-btn-save"
           />
    <input type="submit"
           name="restore"
           title="<?php echo Loc::getMessage('MAIN_HINT_RESTORE_DEFAULTS'); ?>"
           onclick="return confirm('<?php echo addslashes(GetMessage('MAIN_HINT_RESTORE_DEFAULTS_WARNING')); ?>')"
           value="<?php echo Loc::getMessage('MAIN_RESTORE_DEFAULTS'); ?>"
           />
    <?php
        $tabControl->end();
    ?>
</form>
<?php
    echo BeginNote();
    echo Loc::getMessage('MARVIN255_BXMAILER_SMTP');
    echo EndNote();
?>
<?php
    echo BeginNote();
    echo Loc::getMessage('MARVIN255_BXMAILER_PRESENTED_BY_PHPMAILER');
    echo EndNote();
?>
<?php
    if ($isConfigComplete) {
        LocalRedirect($APPLICATION->getCurPageParam('mid=' . urlencode($mid), ['mid']));
    }
?>
