function messageClasses(messageType) {
    // Классы сообщения
    var classes = 'alert alert-dismissible ';
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
            '<p>Выполняется проверка соединения и обмен данными с 1С Итилиум...</p>\n' +
            '</div>');

        // Запуск нативного спиннера - https://make.wordpress.org/core/2015/04/23/spinners-and-dismissible-admin-notices-in-4-2/
        //$('#itilium_test_preloader').show();
        $.post(
            $('#ajax_handler').val(),
            {
                action: $('#itilium_test_action').val(),
                URL: $('#itilium_URL').val(),
                login: $('#itilium_user').val(),
                password: $('#itilium_password').val()
            },
            function (response) {
                //$('#itilium_test_preloader').hide();    // Убрать спиннер - проверка завершена, осталось только показать результат

                if (!response) return;
                var messageData = JSON.parse(response);
                $("#message_area").html('<div class="' + messageClasses(messageData.message_type) + '" id="informer">\n' +
                    '<button type="button" id="informer_close" class="close" data-dismiss="alert">&times;</button>\n' +
                    '<p class="alert-heading"><strong>' + messageData.message_header + '</strong></p>\n' +
                    '<p>' + messageData.message + '</p>\n' +
                    '</div>');
            }
        );
    });

    $("#informer_close").click(function(){
        $("#informer").alert("close");
    });
});
