<?php
/**
 * Создан в PhpStorm.
 * Автор: vbulash
 * Дата и время: 2019-07-22 23:14
 */

/*
Plugin Name: Интегрвция с 1С Итилиум
Plugin URI: http://страница_с_описанием_плагина_и_его_обновлений
Description: Интеграция Wordpress и 1С Итилиум
Version: 1.0
Author: Валерий Булаш
Author URI: http://web-artisan.pro
*/

/*
define("BASE_PATH", plugin_dir_path(__FILE__));

// include the Composer autoload file
require BASE_PATH . 'vendor/autoload.php';

use Backend\Settings\OptionsMenu;

// Добавляем страницу настроек интеграции с Ititlium
add_action('admin_init', function () {
    // Меню работает, но сама страница нет
    // TODO: Опция назначается напрямую только для отладки, нужно вводить на странице настроек
    add_option('itilium_URL', 'http://1c.sys-admin.su/Itilium/hs/mobiledata/');
    //
    include 'user-fields.php';  // Включаем дополнительные поля в профиль пользователя
});

new OptionsMenu();
*/