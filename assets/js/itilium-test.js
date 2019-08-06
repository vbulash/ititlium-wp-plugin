function messageClasses(messageType) {
    // Классы сообщения
    let classes = 'alert alert-dismissible ';
    switch (messageType) {
        case 0: // Info
            classes = classes + 'alert-success';
            break;
        case 1: // Warning
            classes = classes + 'alert-warning';
            break;
        case 2: // Error
            classes = classes + 'alert-danger';
            break;
    }
    return classes;
}

jQuery(function ($) {
    // Тестирование соединения с 1С Итилиум по нажатию кнопки
    $('#itilium_connect').click(function () {

        $("#message_area").html('<div class="' + messageClasses(1) + '" id="informer">\n' +
            '<button type="button" id="informer_close" class="close" data-dismiss="alert">&times;</button>\n' +
            '<p>Выполняется проверка соединения...</p>\n' +
            '</div>');

        // Запуск нативного спиннера - https://make.wordpress.org/core/2015/04/23/spinners-and-dismissible-admin-notices-in-4-2/
        //$('#itilium_test_preloader').show();
        $.post({
            url: $('#ajax_handler').val(),
            data: {
                action: $('#itilium_test_action').val(),
                URL: $('#itilium_URL').val(),
                login: $('#itilium_user').val(),
                password: $('#itilium_password').val()
            },
            complete: function (response) {
                //$('#itilium_test_preloader').hide();    // Убрать спиннер - проверка завершена, осталось только показать результат

                if (!response) return;

                $("#message_area").html(response.responseText);
            }
    })
        ;
    });

    $("#informer_close").click(function () {
        $("#informer").alert("close");
    });
});
