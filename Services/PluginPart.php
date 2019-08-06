<?php
/**
 * Создан в PhpStorm.
 * Автор: vbulash
 * Дата и время: 2019-08-03 15:51
 */

namespace Services;

use Backend\Connection;

/**
 * Визуальная часть плагина. Может реализовывать собственный шорткод
 * @package Services
 */
abstract class PluginPart
{
    private $shortcode = null;
    protected $recordset = null;
    private $connection = null;

    /**
     * @return Connection
     */
    public function getConnection(): Connection
    {
        return $this->connection;
    }

    /**
     * @param null $connection
     */
    public function setConnection($connection): void
    {
        $this->connection = $connection;
    }

    public function __construct($shortcode = null)
    {
        $this->shortcode = $shortcode;
    }

    public function init()
    {
        add_action('wp_enqueue_scripts', [$this, 'registerSystemScripts']);
        add_action('wp_enqueue_scripts', [$this, 'registerAppScripts']);

        if (isset($this->shortcode))
            add_shortcode($this->shortcode, [$this, 'render']);

        // TODO: добавиь поддержку admin_post
        // TODO: добавиь поддержку admin_ajax
    }

    /**
     * Формирование html-кода для шорткода
     * @return string Сгенерированный HTML-код
     */
    abstract public function render();

    /**
     * Регистрация системных скриптов js и стилей
     */
    abstract public function registerSystemScripts();

    /**
     * Регистрация прикладных скриптов js и стилей
     */
    abstract public function registerAppScripts();

    /**
     * Сформировать источник данных для последующего использования частью плагина
     * @return mixed Источник данных
     */
    abstract public function initRecordset();
}