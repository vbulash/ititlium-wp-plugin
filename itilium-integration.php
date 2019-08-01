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

define("BASE_PATH", plugin_dir_path(__FILE__));
require BASE_PATH . 'vendor/autoload.php';

use Backend\Connection;
use Backend\Options;

// Добавляем страницу настроек интеграции с Ititlium
add_action('plugins_loaded', function () {
    if (!is_admin()) return;

    (new Connection())->init();

    include 'user-fields.php';  // Включаем дополнительные поля в профиль пользователя

    new Options();  // Пункт меню и страница настроек
});

add_action('admin_enqueue_scripts', function () {
    if (!is_admin()) return;

    //if(!is_page('profile')) return; // Только для профиля пользователя

    if($GLOBALS['pagenow'] === 'profile.php') {
        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script('jquery-ui-widget');
        wp_enqueue_script('jquery-ui-mouse');
        wp_enqueue_script('jquery-ui-accordion');
        wp_enqueue_script('jquery-ui-autocomplete');
        wp_enqueue_script('jquery-ui-selectmenu');
        wp_enqueue_script('jquery-ui-slider');

        wp_enqueue_script('itilium_test',
            plugin_dir_url(__FILE__) . 'assets/js/itilium-test.js',
            array('jquery'),
            false,
            true);

        wp_enqueue_style('bootstrap_css', 'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css');
        wp_enqueue_script('popper_js', 'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js', array('jquery'));
        wp_enqueue_script('bootstrap_js', 'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js', array('jquery'));
    }
});

