<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Application;
use Bitrix\Main\EventManager;

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
        $eventManager = EventManager::getInstance();
        foreach ($this->getEventsList() as $event) {
            $res = $eventManager->registerEventHandlerCompatible(
                $event['FROM_MODULE_ID'],
                $event['EVENT_TYPE'],
                $this->MODULE_ID,
                $event['TO_CLASS'],
                $event['TO_METHOD'],
                $event['SORT']
            );
        }
    }

    /**
     * Удаляет данные модуля из базы данных сайта.
     *
     * Удаляет агента, который будет обрабатывать загрузку.
     */
    public function unInstallDb()
    {
        $eventManager = EventManager::getInstance();
        foreach ($this->getEventsList() as $event) {
            $eventManager->unRegisterEventHandler(
                $event['FROM_MODULE_ID'],
                $event['EVENT_TYPE'],
                $this->MODULE_ID,
                $event['TO_CLASS'],
                $event['TO_METHOD']
            );
        }

        CAgent::RemoveModuleAgents($this->MODULE_ID);

        Option::delete($this->MODULE_ID);
    }

    /**
     * Копирует файлы модуля в битрикс
     */
    public function installFiles()
    {
        CopyDirFiles(
            $this->getInstallatorPath() . '/admin',
            Application::getDocumentRoot() . '/bitrix/admin'
        );
    }

    /**
     * Удаляет файлы модуля из битрикса.
     */
    public function unInstallFiles()
    {
        CopyDirFiles(
            $this->getInstallatorPath() . '/admin',
            Application::getDocumentRoot() . '/bitrix/admin'
        );
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

    /**
     * Возвращает список событий, которые должны быть установлены для данного модуля.
     *
     * @return array
     */
    protected function getEventsList()
    {
        return [];
    }
}
