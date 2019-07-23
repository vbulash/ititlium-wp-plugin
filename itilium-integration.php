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

// Добавляем страницу настроек интеграции с Ititlium
add_action('admin_init', function() {
    // Меню работает, но сама страница нет
    add_options_page(
        'Интеграция с 1С Итилиум',
        'Интеграция с 1С Итилиум',
        10,
        'itilium_settings',
        'render_itilium_options'
        /*
        function() {
            echo "<h1>Yes, settings page is showing!</h1>";
        }*/
        );

    // TODO: Опция назачается напрямую только для отладки, нужно вводить на странице настроек
    add_option('itilium_URL', 'http://1c.sys-admin.su/Itilium/hs/mobiledata/');
    //
    include 'user-fields.php';  // Включаем дополнительные поля в профиль пользователя
});

function render_itilium_options() {
    echo "<h1>Yes, settings page is showing!</h1>";
}
