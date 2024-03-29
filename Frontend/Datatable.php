<?php
/**
 * Создан в PhpStorm.
 * Автор: vbulash
 * Дата и время: 2019-08-04 12:04
 */

namespace Frontend;

use Backend\Connection;
use Services\PluginPart;

/**
 * Wrapper для Datatables.net
 */
class Datatable extends PluginPart
{
    protected $recordset = null;

    /**
     * Формирование html-кода для шорткода
     * @return string Сгенерированный HTML-код
     */
    public function render()
    {
        $this->recordset = $this->initRecordset();
        if(!$this->recordset) {
            $message = sprintf('<div id="message-area">%s</div>',
                $this->getConnection()->getMessageHelper()->generateBootstrapAlert());
            $this->getConnection()->getMessageHelper()->clear();
            return $message;
        }

        if (!isset($this->recordset))
            return '<strong>Нет данных для отображения обращений 1С Итилиум</strong>';

        if(!wp_get_current_user())
            return '<strong>Текущий пользователь не вошел пользователем на сайт</strong>';

        // Записать скрипты-источники - записей и столбцов
        $recordsHtml = $this->createJSRecordset($this->recordset);
        file_put_contents(
            $_SESSION['plugin_base_url'] . 'assets/js/data/records.js',
            $recordsHtml
        );

        $columnsHtml = $this->createJSColumns($this->recordset);
        file_put_contents(
            $_SESSION['plugin_base_url'] . 'assets/js/data/columns.js',
            $columnsHtml
        );

        // Сгенерировать HTML таблицы данных
        $html =
            '<div id="message-area"></div>\n' .
            '<table id="itilium_list">\n' .
            '\t<thead>\n' .
            '\t\t<tr>\n';

        $headerHtml = '';
        foreach ($this->recordset[0] as $column) {
            $visible = true;
            if (isset($column['visible']))
                if (!$column['visible'])
                    $visible = false;

            if ($visible)
                $headerHtml .= sprintf("\t\t\t<td><strong>%s</strong></td>\n", $column['title']);
        }
        $html .=
            $headerHtml .
            '\t\t</tr>\n' .
            '\t</thead>\n' .
            '\t<tbody></tbody>\n' .
            '</table>';

        return $html;
    }

    /**
     * Регистрация системных скриптов js и стилей
     */
    public function registerSystemScripts()
    {
        // Работаем только на странице со списком инцидентов
        if (get_the_ID() != get_option('itilium_list')) return;

        // jQuery
        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script('jquery-ui-widget');
        wp_enqueue_script('jquery-ui-mouse');
        wp_enqueue_script('jquery-ui-accordion');
        wp_enqueue_script('jquery-ui-autocomplete');
        wp_enqueue_script('jquery-ui-selectmenu');
        wp_enqueue_script('jquery-ui-slider');

        // Bootstrap 4
        wp_enqueue_style('bootstrap_css', 'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css');
        wp_enqueue_script('popper_js', 'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js', array('jquery'));
        wp_enqueue_script('bootstrap_js', 'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js', array('jquery'));

        // Datatables.net
        // Набор взят для варианта Bootstrap 4 (http://cdn.datatables.net)

        // JS-скрипты Datatables.net
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

        // Стили Datatables.net
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

    /**
     * Регистрация прикладных скриптов js и стилей
     */
    public function registerAppScripts()
    {
        wp_enqueue_script('itilium_records', $_SESSION['plugin_base_url'] . 'assets/js/data/records.js');
        wp_enqueue_script('itilium_columns', $_SESSION['plugin_base_url'] . 'assets/js/data/columns.js');
        wp_enqueue_script('itilium_list',
            $_SESSION['plugin_base_url'] . 'assets/js/itilium-list.js',
            array('jquery'),
            false,
            true);
    }

    /**
     * Сформировать источник данных для последующего использования частью плагина
     * @return mixed Источник данных
     */
    public function initRecordset()
    {
        // Получить исходные данные
        $this->setConnection(new Connection());
        $source = $this->getConnection()->getAll(); // -- массив исходных объектов-записей
        if (!$source) return false;

        $this->recordset = [];
        foreach ($source as $properties) {
            $fields = [
                ['name' => 'number', 'value' => $properties->Number, 'title' => 'Номер', 'visible' => false],
                ['name' => 'date', 'value' => $properties->Data, 'title' => 'Дата регистрации', 'visible' => false],
                ['name' => 'number_date',
                    'value' => sprintf("<a href='%s'>%s от %s</a>",
                        $properties->Number, $properties->Number, $properties->Data), 'title' => 'Обращение'],
                ['name' => 'topic', 'value' => $properties->Topic, 'title' => 'Тема'],
                ['name' => 'description', 'value' => $properties->Description, 'title' => 'Описание'],
                ['name' => 'status', 'value' => $properties->Status->Name, 'title' => 'Состояние'],
                ['name' => 'service', 'value' => $properties->MembershipServices->Service->Name, 'title' => 'Услуга'],
                ['name' => 'service_pack', 'value' => $properties->MembershipServices->Name, 'title' => 'Состав услуги'],
                ['name' => 'category', 'value' => $properties->Category->Name, 'title' => 'Категория'],
                ['name' => 'closure_plan', 'value' => $properties->TheRegulatoryClosureDate, 'title' => 'Нормативная дата закрытия'],
                ['name' => 'closure_fact', 'value' => $properties->TheActualClosingDate, 'title' => 'Фактическая дата закрытия'],
                ['name' => 'files', 'value' => ($properties->ThereAreFiles ? '&times;' : ''), 'title' => 'Файлы']
            ];
            $this->recordset[] = $fields;
        }

        return $this->recordset;
    }

    private function createJSRecordset()
    {
        if (!isset($this->recordset)) return 'window.recordset = null;';

        $html =
            "window.recordset = \n" .
            "[\n";
        $row = [];
        foreach ($this->recordset as $record) {
            $rowHtml = "[";
            $cells = [];
            foreach ($record as $property) {
                $cells[] = sprintf("\n{\"%s\": \"%s\"}", $property['name'], $property['value']);
            }
            $rowHtml .= implode(',', $cells);
            $row[] = $rowHtml . "\n]\n";
        }
        $html .= implode(',', $row);
        $html .= "];";

        return $html;
    }

    private function createJSColumns()
    {
        if (!isset($this->recordset)) return 'window.columns = null;';

        $html =
            "window.columns = \n" .
            "[\n";
        $row = [];
        foreach ($this->recordset as $record) {
            $rowHtml = "[";
            $columns = [];
            foreach ($record as $property) {
                $items = [];
                $items[] = sprintf('"title": "%s\"', $property['title']);
                $items[] = sprintf('"data": "%s\"', $property['name']);
                $visible = true;
                if (isset($property['visible']))
                    if (!$property['visible'])
                        $visible = false;
                $items[] = sprintf("\"visible\": \"%s\"", $visible);

                $columns[] = sprintf("\n{%s}", implode(',', $items));
            }
            $rowHtml .= implode(',', $columns);
            $row[] = $rowHtml . "\n]\n";
        }
        $html .= implode(',', $row);
        $html .= "];";

        return $html;
    }
}