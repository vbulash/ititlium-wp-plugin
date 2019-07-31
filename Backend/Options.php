<?php
/**
 * Создан в PhpStorm.
 * Автор: vbulash
 * Дата и время: 2019-07-30 13:20
 */

namespace Backend;


class Options
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct()
    {
        add_action('admin_menu', array($this, 'add_plugin_page'));
        add_action('admin_init', array($this, 'page_init'));
    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        add_options_page(
            'Интеграция с 1С Итилиум',
            'Интеграция с 1С Итилиум',
            'manage_options',
            'itilium-menu',
            [$this, 'create_admin_page']
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
        $this->options = [
            'itilium_URL' => get_option('itilium_URL'),
            'itilium_list' => get_option('itilium_list'),
            'itilium_details' => get_option('itilium_details')
        ];
        ?>
        <div class="wrap">
            <h1>Интеграция с 1С Итилиум</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('itilium_group');
                do_settings_sections('itilium-setting-admin');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    /**
     * Зарегистрировать и добавить настройки
     */
    public function page_init()
    {
        register_setting(
            'itilium_group',    // Группа опций
            'itilium_URL',      // URL для связи с Итилиум
            [$this, 'URL_sanitize']        // Очистка ввода
        );

        register_setting(
            'itilium_group',    // Группа опций
            'itilium_details',      // Страница для отображения единичного инцидента
            [$this, 'details_sanitize']        // Очистка ввода
        );

        // Секция URL
        //**************************************************************************
        add_settings_section(
            'itilium_URL_section_id', // ID
            'URL для получения данных', // Title
            [$this, 'print_itilium_URL_section_info'], // Callback
            'itilium-setting-admin' // Page
        );

        // Поле ввода URL в секции itilium_URL_section_id
        add_settings_field(
            'URL',
            'Itilium URL',
            [$this, 'URL_callback'],
            'itilium-setting-admin',
            'itilium_URL_section_id'
        );

        // Секция страниц
        //**************************************************************************
        add_settings_section(
            'itilium_page_section_id', // ID
            'Страницы для отображения данных', // Title
            [$this, 'print_itilium_pages_section_info'], // Callback
            'itilium-setting-admin' // Page
        );

        // Поле ввода страницы списка инцидентов в секции itilium_page_section_id
        add_settings_field(
            'details',
            'Страница отображения инцидента:',
            [$this, 'details_callback'],
            'itilium-setting-admin',
            'itilium_page_section_id'
        );
    }

    /**
     * Очистка введенного URL, если это необходимо
     * @param string $input URL для очистки (sanitize)
     */
    public function URL_sanitize($input)
    {
        $new_input = null;
        if (isset($input)) {
            $new_input = esc_url_raw($input);
            // Добавить финальный слэш
            $new_input = rtrim($new_input, "/") . '/';
        }

        return $new_input;
    }

    /**
     * Очистка введенной страницы детализации инцидента, если это необходимо
     * @param string $input Страница для очистки (sanitize)
     */
    public function details_sanitize($input)
    {
        return $input;  // Возвращаем ID выбранной страницы без предобработки
    }


    // Подзаголовок секции URL
    public function print_itilium_URL_section_info()
    {
        print 'Введите информацию для соединения с 1С Итилиум:';
    }

    // Подзаголовок секции страниц
    public function print_itilium_pages_section_info()
    {
        print 'Выберите страницы сайта, которые будут использоваться для отображения информации из 1С Итилиум:';
    }

    public function URL_callback()
    {
        printf(
            '<input type="url" id="itilium_URL" name="itilium_URL" value="%s" class="regular-text"/>',
            isset($this->options['itilium_URL']) ? esc_attr($this->options['itilium_URL']) : ''
        );
        print '<p class="description">Проверить соединение с 1С Итилиум вы сможете с помощью логина и пароля на странице профиля пользователя</p>';

    }

    public function details_callback()
    {
        wp_dropdown_pages(
            array(
                'name'              => 'itilium_details',
                'echo'              => 1,
                'show_option_none'  => '&mdash; Выбрать &mdash;',
                'option_none_value' => '0',
                'selected'          => get_option('itilium_details')
            )
        );
    }
}
