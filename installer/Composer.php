<?php

namespace marvin255\bxmailer\installer;

use Composer\Script\Event;
use Composer\Factory;
use Composer\Util\Filesystem;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Класс-установщик, который необходим для того, чтобы скопировать файлы модуля
 * из папки composer внутрь структуры битрикса. Это требуется для того, чтобы
 * модуль был внутри структуры битрикса и обрабатывался как именно как модуль
 * битрикса. Весь код библиотеки не нужен, следует переносить только папку с классами модуля.
 */
class Composer
{
    /**
     * Название вендора.
     *
     * @var string
     */
    protected static $vendor = 'marvin255';
    /**
     * Название модуля.
     *
     * @var string
     */
    protected static $module = 'bxmailer';

    /**
     * Возвращает массив вида "путь до папки бибилиотеки" => "путь для установки".
     * Для копироания данных в битрикс. Эти данные будут скопированы и при установке,
     * и при обновлении.
     *
     * @return array
     */
    protected static function getInstallingFoldersPathes(Event $event)
    {
        $libFolder = self::getLibraryFolder($event);
        $modulesFolder = self::getModulesFolder($event);

        return [
            $libFolder => $modulesFolder . '/' . self::$vendor . '.' . self::$module,
        ];
    }

    /**
     * Возвращает массив вида "путь до папки бибилиотеки" => "путь для установки".
     * Для копироания данных в битрикс. Эти данные будут скопированы только при обновлении.
     *
     * @return array
     */
    protected static function getUpdatingFoldersPathes(Event $event)
    {
        $libFolder = self::getLibraryFolder($event);
        $bitrixFolder = self::getBitrixFolder($event);

        return [];
    }

    /**
     * Устанавливает модуль в структуру битрикса.
     *
     * **Внимание** перед установкой или обновлением удаляет страрую весрию.
     *
     * @param \Composer\Script\Event $event
     */
    public static function injectModule(Event $event)
    {
        $installingFoldersPathes = self::getInstallingFoldersPathes($event);
        $updatingFoldersPathes = self::getUpdatingFoldersPathes($event);
        $fileSystem = new Filesystem();

        foreach ($installingFoldersPathes as $from => $to) {
            if ($to && is_dir($to)) {
                $fileSystem->removeDirectory($to);
            }
            if (!$to || !$from || !is_dir($from)) {
                continue;
            }
            self::copy($from, $to, $fileSystem);
        }

        foreach ($updatingFoldersPathes as $from => $to) {
            if (!is_dir($to) || !is_dir($from)) {
                continue;
            }
            $fileSystem->removeDirectory($to);
            self::copy($from, $to, $fileSystem);
        }
    }

    /**
     * Возвращает полный путь до папки битрикса.
     *
     * @param \Composer\Script\Event $event
     *
     * @return string
     */
    protected static function getBitrixFolder(Event $event)
    {
        $projectRootPath = rtrim(dirname(Factory::getComposerFile()), '/');

        $extras = $event->getComposer()->getPackage()->getExtra();
        if (!empty($extras['install-bitrix'])) {
            $bitrixModulesFolder = $extras['install-bitrix'];
        } else {
            $bitrixModulesFolder = 'web/bitrix';
        }

        return (string) realpath($projectRootPath . '/' . trim($bitrixModulesFolder, '/'));
    }

    /**
     * Возвращает полный путь до папки модулей.
     *
     * @param \Composer\Script\Event $event
     *
     * @return string
     */
    protected static function getModulesFolder(Event $event)
    {
        $projectRootPath = rtrim(dirname(Factory::getComposerFile()), '/');

        $extras = $event->getComposer()->getPackage()->getExtra();
        if (!empty($extras['install-bitrix-modules'])) {
            $bitrixFolder = $extras['install-bitrix-modules'];
        } else {
            $bitrixFolder = 'web/local/modules';
        }

        return (string) realpath($projectRootPath . '/' . trim($bitrixFolder, '/'));
    }

    /**
     * Возвращает путь до папки, в которую установлена бибилиотека.
     *
     * @param \Composer\Script\Event $event
     *
     * @return string
     */
    protected static function getLibraryFolder(Event $event)
    {
        $srcFolder = false;
        $composer = $event->getComposer();
        $repositoryManager = $composer->getRepositoryManager();
        $installationManager = $composer->getInstallationManager();
        $localRepository = $repositoryManager->getLocalRepository();
        $packages = $localRepository->getPackages();
        foreach ($packages as $package) {
            if ($package->getName() === self::$vendor . '/' . self::$module) {
                $srcFolder = realpath(rtrim($installationManager->getInstallPath($package), '/') . '/' . self::$vendor . '.' . self::$module);
                break;
            }
        }

        return (string) $srcFolder;
    }

    /**
     * Копирует содержимое одной папки в другую.
     *
     * @param string $source
     * @param string $target
     *
     * @return bool
     */
    protected static function copy($source, $target, $fileSystem)
    {
        if (!is_dir($source)) {
            return copy($source, $target);
        }
        $it = new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS);
        $ri = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::SELF_FIRST);
        $fileSystem->ensureDirectoryExists($target);

        $result = true;
        foreach ($ri as $file) {
            $targetPath = $target . DIRECTORY_SEPARATOR . $ri->getSubPathName();
            if ($file->isDir()) {
                $fileSystem->ensureDirectoryExists($targetPath);
            } else {
                $result = $result && copy($file->getPathname(), $targetPath);
            }
        }

        return $result;
    }
}
