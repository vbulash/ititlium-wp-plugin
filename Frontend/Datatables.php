<?php
/**
 * Создан в PhpStorm.
 * Автор: vbulash
 * Дата и время: 2019-08-01 01:11
 */

namespace Frontend;

//define('DATATABLES_NET', 'false');   // true - таблицы через Datatables.net, false - таблица HTML

use Backend\Connection;

// Wrapper для Datatables.net
class Datatables
{
    private $recordset_script = null;

    public function __construct()
    {
        add_action('wp_enqueue_scripts', [$this, 'initBootstrapScripts']);
        add_action('wp_enqueue_scripts', [$this, 'initDatatableScripts']);
        add_action('wp_enqueue_scripts', [$this, 'initRecordsetScript']);

        add_shortcode('itilum_list', [$this, 'render']);
    }

    public function initBootstrapScripts()
    {
        // Работаем только на странице со списком инцидентов
        if (get_the_ID() != get_option('itilium_list')) return;

        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script('jquery-ui-widget');
        wp_enqueue_script('jquery-ui-mouse');
        wp_enqueue_script('jquery-ui-accordion');
        wp_enqueue_script('jquery-ui-autocomplete');
        wp_enqueue_script('jquery-ui-selectmenu');
        wp_enqueue_script('jquery-ui-slider');


        wp_enqueue_style('bootstrap_css', 'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css');
        wp_enqueue_script('popper_js', 'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js', array('jquery'));
        wp_enqueue_script('bootstrap_js', 'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js', array('jquery'));
    }

    // Скрипты Datatables.net + пользовательский JavaScript (itilium-list)
    public function initDatatableScripts()
    {
        // Работаем только на странице со списком инцидентов
        if (get_the_ID() != get_option('itilium_list')) return;

        // Набор взят для варианта Bootstrap 4 (http://cdn.datatables.net)
        $scripts = [
            // Release
            'jquery.dataTables' => 'https://cdn.datatables.net/1.10.18/js/jquery.dataTables.min.js',
            'dataTables.bootstrap4' => 'https://cdn.datatables.net/1.10.18/js/dataTables.bootstrap4.min.js',
            // Расширения - включать по мере необходимости
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
            // Расширения - включать по мере необходимости
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

    public function initRecordsetScript()
    {
        /*
        if (defined('DATATABLES_NET')) {
            wp_enqueue_script('itilium_records',
                $_SESSION['plugin_base_url'] . 'assets/data/records.js',
                null, false, true
            );

            wp_enqueue_script('itilium_columns',
                $_SESSION['plugin_base_url'] . 'assets/data/columns.js',
                null, false, true
            );

            wp_enqueue_script('itilium_list',
                $_SESSION['plugin_base_url'] . 'assets/js/itilium-list.js',
                array('jquery'),
                false,
                true);
        }
        */
        wp_enqueue_script('itilium_details',
            $_SESSION['plugin_base_url'] . 'assets/js/itilium-details.js',
            array('jquery'),
            false,
            true);
    }

    public function render()
    {
        // Работаем только на странице со списком инцидентов
        if (get_the_ID() != get_option('itilium_list')) return null;

        $html = '';

        $connection = new Connection();
        $sourceRS = json_decode($connection->getAll()); // -- массив исходных объектов-записей
        if (!$sourceRS) {
            $html .= sprintf('<div id="message-area">%s</div>\n', $connection->createBootstrapAlert());
            return $html;
        }

        $targetRS = [];
        foreach ($sourceRS as $properties) {
            $fields = [
                ['name' => 'UID', 'value' => $properties->UID, 'title' => '', 'hidden' => true],
                ['name' => 'number', 'value' => $properties->Number, 'title' => 'Номер', 'hidden' => true],
                ['name' => 'date', 'value' => $properties->Data, 'title' => 'Дата регистрации', 'hidden' => true],
                ['name' => 'number_date', 'value' => '<a href="' . esc_url(admin_url('admin-post.php')) .
                    '?action=open_details&UID=' . $properties->UID . '">' .
                    $properties->Number . ' от ' . $properties->Data .
                    '</a>', 'title' => 'Обращение'],
                ['name' => 'topic', 'value' => $properties->Topic, 'title' => 'Тема'],
                ['name' => 'description', 'value' => $properties->Description, 'title' => 'Описание'],
                ['name' => 'status', 'value' => $properties->Status->Name, 'title' => 'Состояние'],
                ['name' => 'service', 'value' => $properties->MembershipServices->Service->Name, 'title' => 'Услуга'],
                ['name' => 'service_pack', 'value' => $properties->MembershipServices->Name, 'title' => 'Состав услуги'],
                ['name' => 'category', 'value' => $properties->Category->Name, 'title' => 'Категория'],
                ['name' => 'closure_plan', 'value' => $properties->TheRegulatoryClosureDate, 'title' => 'Нормативная дата закрытия'],
                ['name' => 'closure_fact', 'value' => $properties->TheActualClosingDate, 'title' => 'Фактическая дата закрытия'],
                ['name' => 'files', 'value' => ($properties->ThereAreFiles ? '&times;' : ''), 'title' => 'Файлы', 'classes' => 'text-align: center;']
            ];
            $targetRS[] = $fields;
        }

        if (defined('DATATABLES_NET')) {
            $rsHtml =
                "window.recordset = \n" .
                "[\n";
            $row = [];
            foreach ($targetRS as $rows) {
                $rowHtml = "[";
                $cells = [];
                foreach ($rows as $property) {
                    $cells[] = sprintf("\n{\"%s\": \"%s\"}", $property['name'], $property['value']);
                }
                $rowHtml .= implode(',', $cells);
                $row[] = $rowHtml . "\n]\n";
            }
            $rsHtml .= implode(',', $row);
            $rsHtml .= "];";
            $this->recordset_script = $rsHtml;
            //add_action('wp_enqueue_scripts', [$this, 'initRecordsetScript']);


            $html .= <<<'EOH'
            <table id="itilium_list" class="display" style="width:100%">
                <thead>
                <tr>
                    <th>number</th>
                    <th>date</th>
                    <th>number_date</th>
                    <th>topic</th>
                    <th>description</th>
                    <th>status</th>
                    <th>service</th>
                    <th>service_pack</th>
                    <th>category</th>
                    <th>closure_plan</th>
                    <th>files</th>
                </tr>
                </thead>
                <tbody></tbody>
                <tfoot>
                <tr>
                    <th>number</th>
                    <th>date</th>
                    <th>number_date</th>
                    <th>topic</th>
                    <th>description</th>
                    <th>status</th>
                    <th>service</th>
                    <th>service_pack</th>
                    <th>category</th>
                    <th>closure_plan</th>
                    <th>files</th>
                </tr>
                </tfoot>
            </table>
EOH;
        } else {
            $html .=
                "<div id=\"message-area\"></div>\n" .
                "<table id=\data_table\" class=\"display\" style=\"width:100%\">\n" .
                "<thead>\n" .
                "<tr>\n";
            foreach ($fields as $field) {
                if (!isset($field['hidden']) || !$field['hidden']) {
                    $html .= sprintf(
                        "<td style=\"background-color: lightgrey;\"><strong>%s</strong></td>",
                        $field['title']);
                }
            }
            $html .=
                '</tr>' .
                '</thead>' .
                '<tbody>';
            foreach ($targetRS as $record) {
                $html .= '<tr>';
                foreach ($fields as $field) {
                    if (!isset($field['hidden']) || !$field['hidden']) {
                        $html .= sprintf('<td class="cell" style="%s">%s</td>',
                            isset($field['classes']) ? $field['classes'] : '',
                            $field['value']);
                    }
                }
                $html .= '</tr>';
            }
            $html .=
                '</tbody>' .
                '</table>';
        }
        return $html;
    }
}