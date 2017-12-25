<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\Config\Option;

Loc::loadMessages(__FILE__);

class marvin255_bxmailer extends CModule
{
    public function __construct()
    {
        $arModuleVersion = [];

        include __DIR__ . '/version.php';

        if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        }

        $this->MODULE_ID = 'marvin255.bxmailer';
        $this->MODULE_NAME = Loc::getMessage('BX_MAILER_MODULE_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('BX_MAILER_MODULE_DESCRIPTION');
        $this->MODULE_GROUP_RIGHTS = 'N';
        $this->PARTNER_NAME = Loc::getMessage('BX_MAILER_MODULE_PARTNER_NAME');
    }

    /**
     * @inheritdoc
     */
    public function doInstall()
    {
        ModuleManager::registerModule($this->MODULE_ID);
        $this->installFiles();
        $this->installDb();
    }

    /**
     * @inheritdoc
     */
    public function doUninstall()
    {
        $this->unInstallDb();
        $this->unInstallFiles();
        ModuleManager::unregisterModule($this->MODULE_ID);
    }

    /**
     * Устанавливает данные модуля в базу данных сайта.
     *
     * Устанавливает агента, который будет обрабатывать загрузку.
     */
    public function installDb()
    {
    }

    /**
     * Удаляет данные модуля из базы данных сайта.
     *
     * Удаляет агента, который будет обрабатывать загрузку.
     */
    public function unInstallDb()
    {
        CAgent::RemoveModuleAgents($this->MODULE_ID);
        Option::delete($this->MODULE_ID);
    }

    /**
     * Копирует файлы модуля в битрикс
     */
    public function installFiles()
    {
    }

    /**
     * Удаляет файлы модуля из битрикса.
     */
    public function unInstallFiles()
    {
    }

    /**
     * Возвращает путь к папке с модулем
     *
     * @return string
     */
    public function getInstallatorPath()
    {
        return str_replace('\\', '/', __DIR__);
    }
}
