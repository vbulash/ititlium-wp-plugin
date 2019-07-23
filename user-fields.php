<?php
/**
 * Создан в PhpStorm.
 * Автор: vbulash
 * Дата и время: 2019-07-23 06:45
 */

// Оригинальный код здесь - https://www.cssigniter.com/how-to-add-a-custom-user-field-in-wordpress/

/**
 * Front end registration
 */

add_action( 'register_form', 'crf_registration_form' );
function crf_registration_form() {

    $year = ! empty( $_POST['year_of_birth'] ) ? intval( $_POST['year_of_birth'] ) : '';

    ?>
    <p>
        <label for="year_of_birth"><?php esc_html_e( 'Year of birth', 'crf' ) ?><br/>
            <input type="number"
                   min="1900"
                   max="2017"
                   step="1"
                   id="year_of_birth"
                   name="year_of_birth"
                   value="<?php echo esc_attr( $year ); ?>"
                   class="input"
            />
        </label>
    </p>
    <?php
}

add_filter( 'registration_errors', 'crf_registration_errors', 10, 3 );
function crf_registration_errors( $errors, $sanitized_user_login, $user_email ) {

    if ( empty( $_POST['year_of_birth'] ) ) {
        $errors->add( 'year_of_birth_error', __( '<strong>ERROR</strong>: Please enter your year of birth.', 'crf' ) );
    }

    if ( ! empty( $_POST['year_of_birth'] ) && intval( $_POST['year_of_birth'] ) < 1900 ) {
        $errors->add( 'year_of_birth_error', __( '<strong>ERROR</strong>: You must be born after 1900.', 'crf' ) );
    }

    return $errors;
}

add_action( 'user_register', 'crf_user_register' );
function crf_user_register( $user_id ) {
    if ( ! empty( $_POST['year_of_birth'] ) ) {
        update_user_meta( $user_id, 'year_of_birth', intval( $_POST['year_of_birth'] ) );
    }
}

/**
 * Back end registration
 */

add_action( 'user_new_form', 'crf_admin_registration_form' );
function crf_admin_registration_form( $operation ) {
    if ( 'add-new-user' !== $operation ) {
        // $operation may also be 'add-existing-user'
        return;
    }

    $year = ! empty( $_POST['year_of_birth'] ) ? intval( $_POST['year_of_birth'] ) : '';

    ?>
    <h3><?php esc_html_e( 'Personal Information', 'crf' ); ?></h3>

    <table class="form-table">
        <tr>
            <th><label for="year_of_birth"><?php esc_html_e( 'Year of birth', 'crf' ); ?></label> <span class="description"><?php esc_html_e( '(required)', 'crf' ); ?></span></th>
            <td>
                <input type="number"
                       min="1900"
                       max="2017"
                       step="1"
                       id="year_of_birth"
                       name="year_of_birth"
                       value="<?php echo esc_attr( $year ); ?>"
                       class="regular-text"
                />
            </td>
        </tr>
    </table>
    <?php
}

add_action( 'user_profile_update_errors', 'crf_user_profile_update_errors', 10, 3 );
function crf_user_profile_update_errors( $errors, $update, $user ) {
    if ( empty( $_POST['year_of_birth'] ) ) {
        $errors->add( 'year_of_birth_error', __( '<strong>ERROR</strong>: Please enter your year of birth.', 'crf' ) );
    }

    if ( ! empty( $_POST['year_of_birth'] ) && intval( $_POST['year_of_birth'] ) < 1900 ) {
        $errors->add( 'year_of_birth_error', __( '<strong>ERROR</strong>: You must be born after 1900.', 'crf' ) );
    }
}

add_action( 'edit_user_created_user', 'crf_user_register' );


/**
 * Back end display
 */

add_action( 'show_user_profile', 'crf_show_extra_profile_fields' );
add_action( 'edit_user_profile', 'crf_show_extra_profile_fields' );

function crf_show_extra_profile_fields( $user ) {
    $year = get_the_author_meta( 'year_of_birth', $user->ID );
    ?>
    <h3><?php esc_html_e( 'Personal Information', 'crf' ); ?></h3>

    <table class="form-table">
        <tr>
            <th><label for="year_of_birth"><?php esc_html_e( 'Year of birth', 'crf' ); ?></label></th>
            <td>
                <input type="number"
                       min="1900"
                       max="2017"
                       step="1"
                       id="year_of_birth"
                       name="year_of_birth"
                       value="<?php echo esc_attr( $year ); ?>"
                       class="regular-text"
                />
            </td>
        </tr>
    </table>
    <?php
}

add_action( 'personal_options_update', 'crf_update_profile_fields' );
add_action( 'edit_user_profile_update', 'crf_update_profile_fields' );

function crf_update_profile_fields( $user_id ) {
    if ( ! current_user_can( 'edit_user', $user_id ) ) {
        return false;
    }

    if ( ! empty( $_POST['year_of_birth'] ) && intval( $_POST['year_of_birth'] ) >= 1900 ) {
        update_user_meta( $user_id, 'year_of_birth', intval( $_POST['year_of_birth'] ) );
    }
}