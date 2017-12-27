<?php

namespace marvin255\bxmailer\options;

use marvin255\bxmailer\OptionsInterface;
use marvin255\bxmailer\Exception;
use Bitrix\Main\Config\Option;

/**
 * Класс, который получает настройки из настроек модуля в битриксе.
 */
class Bitrix implements OptionsInterface
{
    /**
     * Название модуля, для которого нужного загрузить опции.
     *
     * @var string
     */
    protected $moduleId = null;
    /**
     * Массив с опциями вида "Название опции => значение".
     *
     * @var array
     */
    protected $options = null;

    /**
     * Конструктор.
     *
     * @param string $moduleId
     *
     * @throws \marvin255\bxmailer\Exception
     */
    public function __construct($moduleId)
    {
        if (trim($moduleId) === '') {
            throw new Exception('moduleId can\'t be empty');
        }
        $this->moduleId = $moduleId;
    }

    /**
     * @inheritdoc
     */
    public function get($name, $default = null)
    {
        $options = $this->getOptions();

        return isset($options[$name]) ? $options[$name] : $default;
    }

    /**
     * @inheritdoc
     */
    public function getInt($name, $default = 0)
    {
        return (int) $this->get($name, $default);
    }

    /**
     * @inheritdoc
     */
    public function getBool($name, $default = false)
    {
        return (bool) $this->get($name, $default);
    }

    /**
     * Возвращает массив со всеми опциями.
     *
     * @return array
     */
    protected function getOptions()
    {
        if ($this->options === null) {
            $this->options = $this->loadOptions($this->moduleId);
        }

        return $this->options;
    }

    /**
     * Возвращает массив всех опций для модуля.
     *
     * @param string $moduleId
     *
     * @return array
     */
    protected function loadOptions($moduleId)
    {
        return Option::getForModule($moduleId);
    }
}
