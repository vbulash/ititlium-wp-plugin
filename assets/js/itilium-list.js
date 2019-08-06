jQuery(function ($) {
    /*
    $('#itilium_list').dataTable({
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/1.10.19/i18n/Russian.json"
        },
        "data": window.recordset,
        "lengthMenu": [[25, 50, 100, -1], [25, 50, 100, "Все"] ]
    });
     */

    $("#informer_close").click(function(){
        $("#informer").alert("close");
    });
});
