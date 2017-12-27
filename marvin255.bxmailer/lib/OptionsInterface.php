<?php

namespace marvin255\bxmailer;

/**
 * Интерфейс для объекта, который предоставляет доступ к настройкам из битрикса.
 */
interface OptionsInterface
{
    /**
     * Возвращает настройку по имени.
     *
     * @param string $name
     * @param string $default
     *
     * @return mixed
     */
    public function get($name, $default = null);

    /**
     * Возвращает настройку по имени, преобразует значение к int.
     *
     * @param string $name
     * @param string $default
     *
     * @return int
     */
    public function getInt($name, $default = 0);

    /**
     * Возвращает настройку по имени, преобразует значение к bool.
     *
     * @param string $name
     * @param string $default
     *
     * @return bool
     */
    public function getBool($name, $default = false);
}
