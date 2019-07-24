<?php
/**
 * Создан в PhpStorm.
 * Автор: vbulash
 * Дата и время: 2019-07-23 21:49
 */

namespace Backend\Settings;


class OptionsMenu {

    public function __construct() {
        add_action('admin_menu', [$this, 'pluginMenu']);
    }

    public function pluginMenu() {
        add_options_page(
            'Интеграция с 1С Итилиум',
            'Интеграция с 1С Итилиум',
            "manage_options",
            "itilium-menu",
            [$this, "pluginPage"]
        );
    }

    public function pluginPage() {
        if (!current_user_can("manage_options")) {
            wp_die(__("Sorry you cant access this page"));
        }

        OptionsPage::render();
    }
}