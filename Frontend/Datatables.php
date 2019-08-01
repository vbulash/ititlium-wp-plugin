<?php
/**
 * Создан в PhpStorm.
 * Автор: vbulash
 * Дата и время: 2019-08-01 01:11
 */

namespace Frontend;

// Wrapper для Datatables.net
class Datatables
{
    public function __construct()
    {
        add_action('admin_enqueue_scripts', [$this, 'initScripts']);
    }

    public function initScripts()
    {
        if(!is_admin()) return;

        // TODO: Пристроить локализацию из //cdn.datatables.net/plug-ins/1.10.19/i18n/Russian.json
        // Набор взят для варианта Bootstrap 4 (http://cdn.datatables.net)
        $scripts = [
            // Release
            'jquery.dataTables' => 'https://cdn.datatables.net/1.10.18/js/jquery.dataTables.min.js',
            'dataTables.bootstrap4' => 'https://cdn.datatables.net/1.10.18/js/dataTables.bootstrap4.min.js',
            // Extensions
            //
            // AutoFill
            //'https://cdn.datatables.net/autofill/2.3.3/js/dataTables.autoFill.min.js',
            //'https://cdn.datatables.net/autofill/2.3.3/js/autoFill.bootstrap4.min.js',
            // Buttons
            //'https://cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js',
            //'https://cdn.datatables.net/buttons/1.5.6/js/buttons.bootstrap4.min.js',
            // Column visibility control
            //'https://cdn.datatables.net/buttons/1.5.6/js/buttons.colVis.min.js',
            // Flash export buttons
            //'https://cdn.datatables.net/buttons/1.5.6/js/buttons.flash.min.js',
            // HTML5 export buttons
            //'https://cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js',
            // Print button
            //'https://cdn.datatables.net/buttons/1.5.6/js/buttons.print.min.js',
            // ColReorder
            //'https://cdn.datatables.net/colreorder/1.5.0/js/dataTables.colReorder.min.js',
            // FixedColumns
            //'https://cdn.datatables.net/fixedcolumns/3.2.5/js/dataTables.fixedColumns.min.js',
            // FixedHeader
            //'https://cdn.datatables.net/fixedheader/3.1.4/js/dataTables.fixedHeader.min.js',
            // KeyTable
            //'https://cdn.datatables.net/keytable/2.5.0/js/dataTables.keyTable.min.js',
            // Responsive
            //'https://cdn.datatables.net/responsive/2.2.2/js/dataTables.responsive.min.js',
            //'https://cdn.datatables.net/responsive/2.2.2/js/responsive.bootstrap4.min.js',
            // RowGroup
            //'https://cdn.datatables.net/rowgroup/1.1.0/js/dataTables.rowGroup.min.js',
            // RowReorder
            //'https://cdn.datatables.net/rowreorder/1.2.4/js/dataTables.rowReorder.min.js',
            // Scroller
            //'https://cdn.datatables.net/scroller/2.0.0/js/dataTables.scroller.min.js',
            // Select
            //'https://cdn.datatables.net/select/1.3.0/js/dataTables.select.min.js',
        ];
        foreach ($scripts as $script_group => $javascript) {
            wp_enqueue_script($script_group, $javascript, array('bootstrap_js', 'jquery'), false, true);
        }

        $styles = [
            // Release
            'dataTables.bootstrap4' => 'https://cdn.datatables.net/1.10.18/css/dataTables.bootstrap4.min.css',
            // Extensions
            //
            // AutoFill
            //'https://cdn.datatables.net/autofill/2.3.3/css/autoFill.bootstrap4.min.css',
            // Buttons
            //'https://cdn.datatables.net/buttons/1.5.6/css/buttons.bootstrap4.min.css',
            // Column visibility control
            //'',
            // Flash export buttons
            //'',
            // HTML5 export buttons
            //'',
            // Print button
            //'',
            // ColReorder
            //'https://cdn.datatables.net/colreorder/1.5.0/css/colReorder.bootstrap4.min.css',
            // FixedColumns
            //'https://cdn.datatables.net/fixedcolumns/3.2.5/css/fixedColumns.bootstrap4.min.css',
            // FixedHeader
            //'https://cdn.datatables.net/fixedheader/3.1.4/css/fixedHeader.bootstrap4.min.css',
            // KeyTable
            //'https://cdn.datatables.net/keytable/2.5.0/css/keyTable.bootstrap4.min.css',
            // Responsive
            //'https://cdn.datatables.net/responsive/2.2.2/css/responsive.bootstrap4.min.css',
            // RowGroup
            //'https://cdn.datatables.net/rowgroup/1.1.0/css/rowGroup.bootstrap4.min.css',
            // RowReorder
            //'https://cdn.datatables.net/rowreorder/1.2.4/css/rowReorder.bootstrap4.min.css',
            // Scroller
            //'https://cdn.datatables.net/scroller/2.0.0/css/scroller.bootstrap4.min.css',
            // Select
            //'https://cdn.datatables.net/select/1.3.0/css/select.bootstrap4.min.css',
        ];
        foreach ($styles as $style_group => $style) {
            wp_enqueue_style($style_group, $style, array('bootstrap_css'));
        }
    }
}