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

if (!session_id())
    session_start();
$_SESSION['plugin_base_url'] = plugin_dir_url(__FILE__);
$_SESSION['plugin_base_dir'] = plugin_dir_path(__FILE__);

define('COMPOSER', 'true');
if(!COMPOSER) {
    try {
        spl_autoload_register(function ($class) {
            $dir = __DIR__ . DIRECTORY_SEPARATOR;   // $_SESSION['plugin_base_dir']
            $namespace = __NAMESPACE__;
            $className = $dir . $class . '.php';
            $className = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $className);
            if (file_exists($className)) // Защита от некорректного class_exists
                include $className;
        }, true);
    } catch (Exception $e) {
        error_log('Не удалось загрузить класс');
    }
} else {
    require plugin_dir_path(__FILE__) . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
}

use Backend\Connection;
use Backend\Options;
use Frontend\Datatables;
use Frontend\Details;

// Добавляем страницу настроек интеграции с Ititlium
// Изменяем профиль пользователя
// Инициализация фрронтальной части
//add_action('plugins_loaded', function () {
//add_action('after_setup_theme', function () {

//$datatables = null;
//add_action('wp_ajax_open_details', 'details');
add_action('admin_post_open_details', 'details');
add_action('admin_post_download_file', 'download_file');

if (is_admin()) {    // В консоли
    (new Connection())->init();

    include 'user-fields.php';  // Включаем дополнительные поля в профиль пользователя

    new Options();  // Пункт меню и страница настроек
} else {    // На фронте
    new Datatables();
    new Details('list_details');
    //add_action('admin_post_open_details', 'details');
    //$datatable = new Datatable('itilium_list');
    //$datatable->init();
}
//});

function details()
{
    $detailsPage = get_option('itilium_details');
    if (!$detailsPage) wp_die();

    if (isset($_GET['UID']))
        $_SESSION['UID'] = $_GET['UID'];

    $page = get_permalink($detailsPage);
    wp_redirect($page, 301);

    //$incident = Datatables::details();
    //wp_send_json_success($incident);
    //wp_die();
}

function download_file()
{
    if (!isset($_GET['UID'])) wp_die();

    $connection = new Connection();
    $fileContent = $connection->getFile($_GET['UID']);
    if (!$fileContent) {
        $html = sprintf('<div id="message-area">%s</div>\n', $connection->createBootstrapAlert());
        return $html;
    }
    wp_die();
    return null;
}

add_action('admin_enqueue_scripts', function () {
    if ($GLOBALS['pagenow'] === 'profile.php') {
        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script('jquery-ui-widget');
        wp_enqueue_script('jquery-ui-mouse');
        wp_enqueue_script('jquery-ui-accordion');
        wp_enqueue_script('jquery-ui-autocomplete');
        wp_enqueue_script('jquery-ui-selectmenu');
        wp_enqueue_script('jquery-ui-slider');

        wp_enqueue_script('itilium_test',
            $_SESSION['plugin_base_url'] . 'assets/js/itilium-test.js',
            array('jquery'),
            false,
            true);

        wp_enqueue_style('bootstrap_css', 'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css');
        wp_enqueue_script('popper_js', 'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js', array('jquery'));
        wp_enqueue_script('bootstrap_js', 'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js', array('jquery'));
    }
});

