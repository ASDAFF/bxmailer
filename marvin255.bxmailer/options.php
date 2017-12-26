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

Loc::loadMessages($context->getServer()->getDocumentRoot() . '/bitrix/modules/main/options.php');
Loc::loadMessages(__FILE__);

$tabControl = new CAdminTabControl('tabControl', [
    [
        'DIV' => 'edit1',
        'TAB' => Loc::getMessage('MAIN_TAB_SET'),
        'TITLE' => Loc::getMessage('MAIN_TAB_TITLE_SET'),
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
            'smtp_debug',
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
                       value="<?php echo htmlentities(Option::get($module_id, 'charset')); ?>"
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
                       <?php if (Option::get($module_id, 'is_smtp')) {
        echo 'checked';
    } ?>
                       />
            </td>
        </tr>
        <tr>
            <td width="40%">
                <label><?php echo Loc::getMessage('MARVIN255_BXMAILER_PREFERENCIES_SMTP_DEBUG') ?>:</label>
            <td width="60%">
                <input type="text"
                       size="50"
                       name="smtp_debug"
                       value="<?php echo htmlentities(Option::get($module_id, 'smtp_debug')); ?>"
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
                       <?php if (Option::get($module_id, 'smtp_auth')) {
        echo 'checked';
    } ?>
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
                <label><?php echo Loc::getMessage('MARVIN255_BXMAILER_PREFERENCIES_SMTP_SECURE') ?>:</label>
            <td width="60%">
                <select name="smtp_secure">
                    <option value="">Нет</option>
                    <option value="ssl"<?php if (Option::get($module_id, 'smtp_secure') === 'ssl') {
        echo ' selected';
    } ?>>ssl</option>
                    <option value="tls"<?php if (Option::get($module_id, 'smtp_secure') === 'tls') {
        echo ' selected';
    } ?>>tls</option>
                </select>
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
