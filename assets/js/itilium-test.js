// document.ready inside
jQuery(function ($) {
    //использование jQuery как $
});

function test_connection() {
    jQuery.ajax({
        type: "POST",
        url: jQuery('#ajax_handler').val(),
        data: {
            action: jQuery('#itilium_test_action').val(),
            URL: jQuery('#itilium_URL').val(),
            login: jQuery('#itilium_user').val(),
            password: jQuery('#itilium_password').val()
        }
    }).done(function (msg) {
        alert("Готово: " + msg);
    });
}