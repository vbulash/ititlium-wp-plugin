<?php
/**
 * Создан в PhpStorm.
 * Автор: vbulash
 * Дата и время: 2019-07-23 06:45
 */

// Оригинальный код здесь - https://www.cssigniter.com/how-to-add-a-custom-user-field-in-wordpress/

/**
 * Back end registration
 */

function itilium_profile_parts($itilium_user, $itilium_password)
{
    $itilium_URL = get_option('itilium_URL');
    ?>
    <h3><?php esc_html_e('Данные коннекта Итилиум', 'itilium'); ?></h3>

    <table class="form-table">
        <tr>
            <th>
                <input type="hidden" id="ajax_handler" value="<?php echo esc_url(admin_url('admin-ajax.php')); ?>"/>
                <input type="hidden" name="itilium_test_action" id="itilium_test_action" value="itilium_test"/>
                <label for="itilium_URL"><?php esc_html_e('URL Итилиум', 'itilium'); ?>
            </th>
            <td>
                <input type="text"
                       id="itilium_URL"
                       name="itilium_URL"
                       value="<?php echo esc_attr($itilium_URL); ?>"
                       class="regular-text"
                       readonly
                />
                <p class="description">URL 1С Итилиум вводится в настройках и распространяется на всех пользователей
                    данного сайта</p>
            </td>
        </tr>
        <tr>
            <th><label for="itilium_user"><?php esc_html_e('Логин Итилиум', 'itilium'); ?></th>
            <td>
                <input type="text"
                       id="itilium_user"
                       name="itilium_user"
                       value="<?php echo esc_attr($itilium_user); ?>"
                       class="regular-text"/>
            </td>
        </tr>
        <tr>
            <th><label for="itilium_password"><?php esc_html_e('Пароль Итилиум', 'itilium'); ?></th>
            <td>
                <input type="password"
                       id="itilium_password"
                       name="itilium_password"
                       value="<?php echo esc_attr($itilium_password); ?>"
                       class="regular-text"/>
            </td>
        </tr>
        <tr>
            <th>&nbsp;</th>
            <td>
                <input type="button"
                       id="itilium_connect"
                       name="itilium_connect"
                       value="<?php esc_html_e('Тест коннекта к Итилиуму', 'itilium'); ?>"
                       class="button button-secondary"
                />
            </td>
        </tr>
    </table>
    <div id="itilium_test_preloader" class="spinner" style="display: none"></div>
    <div id="message_area"></div>
    <?php
}

add_action('user_new_form', function ($operation) {
    // Только при добавлении нового пользователя
    if ('add-new-user' !== $operation) {
        // $operation may also be 'add-existing-user'
        return;
    }

    $itilium_user = !empty($_POST['itilium_user']) ? $_POST['itilium_user'] : '';
    $itilium_password = !empty($_POST['itilium_password']) ? $_POST['itilium_password'] : '';
    itilium_profile_parts($itilium_user, $itilium_password);
});

// Только контроль ввода, без коннекта к Itilium
add_action('user_profile_update_errors', function ($errors, $update, $user) {
    return true;

}, 10, 3);

/**
 * Back end display
 */

add_action('show_user_profile', 'itl_show_extra_profile_fields');
add_action('edit_user_profile', 'itl_show_extra_profile_fields');
function itl_show_extra_profile_fields($user)
{
    $itilium_user = get_the_author_meta('itilium_user', $user->ID);
    $itilium_password = get_the_author_meta('itilium_password', $user->ID);
    itilium_profile_parts($itilium_user, $itilium_password);
}

add_action('personal_options_update', 'itl_update_profile_fields');
add_action('edit_user_profile_update', 'itl_update_profile_fields');
function itl_update_profile_fields($user_id)
{
    if (!current_user_can('edit_user', $user_id)) {
        return false;
    }

    if (!empty($_POST['itilium_user'])) {
        update_user_meta($user_id, 'itilium_user', $_POST['itilium_user']);
        update_user_meta($user_id, 'itilium_password', $_POST['itilium_password']);
    }
}