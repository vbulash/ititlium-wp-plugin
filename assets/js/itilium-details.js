jQuery(function ($) {
    /*
    $('.cell').click(function (event) {
        //alert(event.target.href);
        event.preventDefault();
        var parts = event.target.href.split('?');
        var _URL = parts[0];
        var params = parts[1].split('&');
        // Action
        var paramAction = params[0].split('=');
        var _action = paramAction[1];
        // UID
        var paramUID = params[1].split('=');
        var _UID = paramUID[1];
        $.post({
            url: _URL,
            data: {
                action: _action,
                UID:
                _UID
            },
            error: function (response) {    // Возврат форматированного (Bootstrap 4) сообщения об ошибке
                if(!response) return;

                alert(response.data);
                $("#message_area").html(response.data);
            }
        })
        ;

    });
     */
});