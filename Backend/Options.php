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

        add_filter('display_post_states', [$this, 'add_itilium_states'], 20, 2);
    }

    // Добавление новых состояний для страниц
    public function add_itilium_states($states, $post)
    {
        // Страница со списком инцидентов 1С Итилиум
        $list = get_option('itilium_list');
        if (isset($list) && $post->ID == $list) {
            $states[] = 'Список инцидентов 1С Итилиум';
        }

        // Страница единичного инцидента 1С Итилиум
        $details = get_option('itilium_details');
        if (isset($details) && $post->ID == $details) {
            $states[] = 'Детали инцидента 1С Итилиум';
        }

        return $states;
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
            'itilium_details' => get_option('itilium_details'),
            'itilum_list_shortcode' => get_option('itilum_list_shortcode', '[itilum_list]'),
            'itilum_details_shortcode' => get_option('itilum_details_shortcode', '[itilum_details]')
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
        // Как хранится
        $settings = [
            ['option' => 'itilium_URL', 'sanitize' => 'URL_sanitize'],      // URL для связи с Итилиум
            ['option' => 'itilium_list', 'sanitize' => 'list_sanitize'],    // Страница для списка инцидентов
            ['option' => 'itilium_details', 'sanitize' => 'details_sanitize'],  // Страница для отображения единичного инцидента
        ];
        foreach ($settings as $setting) {
            register_setting(
                'itilium_group',    // Группа опций
                $setting['option'],
                [$this, $setting['sanitize']]   // Очистка ввода
            );
        }

        // Как отображается (секции / поля)
        $sections = [
            [
                'section' => 'itilium_URL_section_id',
                'title' => 'URL для получения данных',
                'callback' => 'print_itilium_URL_section_info',
                'fields' => [
                    ['field' => 'URL', 'title' => 'Itilium URL:', 'callback' => 'URL_callback'],
                ]
            ],
            [
                'section' => 'itilium_page_section_id',
                'title' => 'Страницы для отображения данных',
                'callback' => 'print_itilium_pages_section_info',
                'fields' => [
                    ['field' => 'list', 'title' => 'Страница отображения списка инцидентов:', 'callback' => 'list_callback'],
                    ['field' => 'details', 'title' => 'Страница отображения деталей инцидента:', 'callback' => 'details_callback'],
                ]
            ],
            [
                'section' => 'itilium_shortcodes_section_id',
                'title' => 'Короткие коды (shortcodes) для работы с 1С Итилиум',
                'callback' => 'print_itilium_shortcodes_section_info',
                'fields' => [
                    ['field' => 'list', 'title' => 'Код отображения списка инцидентов:', 'callback' => 'list_shortcode_callback'],
                    ['field' => 'details', 'title' => 'Код отображения инцидента:', 'callback' => 'details_shortcode_callback'],
                ]
            ]
        ];
        foreach ($sections as $section) {
            add_settings_section(
                $section['section'],
                $section['title'],
                [$this, $section['callback']],
                'itilium-setting-admin'
            );
            $fields = $section['fields'];
            foreach ($fields as $field) {
                add_settings_field(
                    $field['field'],
                    $field['title'],
                    [$this, $field['callback']],
                    'itilium-setting-admin',
                    $section['section']
                );
            }
        }
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
     * Очистка введенной страницы списка инцидентов, если это необходимо
     * @param string $input Страница для очистки (sanitize)
     */
    public function list_sanitize($input)
    {
        return $input;  // Возвращаем ID выбранной страницы без предобработки
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
        print 'Введите информацию для соединения с 1С Итилиум';
    }

    // Подзаголовок секции страниц
    public function print_itilium_pages_section_info()
    {
        print 'Выберите страницы сайта, которые будут использоваться для отображения информации из 1С Итилиум<br/>' .
            'Данные страницы должны содержать короткие коды, указанные в секции ниже';
    }

    // Подзаголовок секции шорткодов
    public function print_itilium_shortcodes_section_info()
    {
        print 'Скопируйте короткие коды (shortcodes) для вставки в страницы для работы с 1С Итилиум';
    }

    // Основные функции отображения (callbacks) соответствующих полей

    public function URL_callback()
    {
        printf(
            '<input type="url" id="itilium_URL" name="itilium_URL" value="%s" class="regular-text"/>',
            isset($this->options['itilium_URL']) ? esc_attr($this->options['itilium_URL']) : ''
        );
        print '<p class="description">Проверить соединение с 1С Итилиум вы сможете с помощью логина и пароля на странице профиля пользователя</p>';

    }

    public function list_callback()
    {
        wp_dropdown_pages(
            array(
                'name' => 'itilium_list',
                'echo' => 1,
                'show_option_none' => '&mdash; Выбрать &mdash;',
                'option_none_value' => '0',
                'selected' => get_option('itilium_list')
            )
        );
    }

    public function details_callback()
    {
        wp_dropdown_pages(
            array(
                'name' => 'itilium_details',
                'echo' => 1,
                'show_option_none' => '&mdash; Выбрать &mdash;',
                'option_none_value' => '0',
                'selected' => get_option('itilium_details')
            )
        );
    }

    public function list_shortcode_callback()
    {
        ?>
        <input type="text"
               id="itilum_list_shortcode"
               name="itilum_list_shortcode"
               value="<?php echo esc_attr($this->options['itilum_list_shortcode']); ?>"
               class="regular-text"
               readonly
        />
        <?php
    }

    public function details_shortcode_callback()
    {
        ?>
        <input type="text"
               id="itilum_details_shortcode"
               name="itilum_details_shortcode"
               value="<?php echo esc_attr($this->options['itilum_details_shortcode']); ?>"
               class="regular-text"
               readonly
        />
        <?php
    }
}
