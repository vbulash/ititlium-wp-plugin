<?php
/**
 * Создан в PhpStorm.
 * Автор: vbulash
 * Дата и время: 2019-08-05 12:22
 */

namespace Frontend;

use Backend\Connection;

class Details
{
    private $UID = null;

    public function __construct($shortcode = null)
    {
        if ($shortcode)
            add_shortcode($shortcode, [$this, 'render']);
    }

    public function render(): ?string
    {
        $this->UID = $_SESSION['UID'];
        if (!isset($this->UID)) return 'Не установлен UID инцидента из 1С Итилиум';

        $html = '';

        $connection = new Connection();
        $data = json_decode($connection->getIncident($this->UID));
        if (!$data) {
            $html = sprintf("<div id=\"message-area\">%s</div>\n", $connection->createBootstrapAlert());
            return $html;
        };
        //$html .= print_r($data, true);

        // Детали по обращению
        $html .= sprintf("<h2>Обращение № %s от %s</h2>",
            $data->Number, $data->Data);
        $html .=
            "<div id=\"message-area\"></div>\n" .
            "<table id=\data_table\" class=\"display\" style=\"width:100%\">\n" .
            "\t<thead>\n" .
            "\t\t<tr>\n" .
            "\t\t\t<td style=\"background-color: lightgrey;\"><strong>Тема</strong></td>\n" .
            sprintf("\t\t\t<td>%s</td>\n", $data->Topic) .
            "\t\t</tr>\n" .
            "\t\t<tr>\n" .
            "\t\t\t<td style=\"background-color: lightgrey;\"><strong>Описание</strong></td>\n" .
            sprintf("\t\t\t<td>%s</td>\n", $data->Description) .
            "\t\t</tr>\n" .
            "\t\t<tr>\n" .
            "\t\t\t<td style=\"background-color: lightgrey;\"><strong>Услуга</strong></td>\n" .
            sprintf("\t\t\t<td>%s</td>\n", $data->MembershipServices->Service->Name) .
            "\t\t</tr>\n" .
            "\t\t<tr>\n" .
            "\t\t\t<td style=\"background-color: lightgrey;\"><strong>Состав услуги</strong></td>\n" .
            sprintf("\t\t\t<td>%s</td>\n", $data->MembershipServices->Name) .
            "\t\t</tr>\n" .
            "\t\t<tr>\n" .
            "\t\t\t<td style=\"background-color: lightgrey;\"><strong>Нормативная дата закрытия</strong></td>\n" .
            sprintf("\t\t\t<td>%s</td>\n", $data->TheRegulatoryClosureDate) .
            "\t\t</tr>\n" .
            "\t\t<tr>\n" .
            "\t\t\t<td style=\"background-color: lightgrey;\"><strong>Фактическая дата закрытия</strong></td>\n" .
            sprintf("\t\t\t<td>%s</td>\n", $data->TheActualClosingDate) .
            "\t\t</tr>\n" .
            "\t</thead>\n" .
            "</table>";

        // Общение по документу
        $html .= "<h2>Общение по документу</h2>\n";
        $html .= "<p>Не реализовано: нужна информация (данные) по общению для отображения внутри шорткода</p>\n";

        // Прикрепленные файлы
        if ($data->ThereAreFiles == 1) {
            $html .= "<h2>Прикрепленные файлы</h2>\n";

            if ($files = $data->FilesDefinitions) {
                $html .= "<ul>\n";
                foreach ($files as $file) {
                    $html .= sprintf("<li><a href=\"%s\">%s</a></li>\n",
                        esc_url(admin_url('admin-post.php')) .
                        '?action=download_file&UID=' . $file->UID,
                        $file->Name);
                }
                $html .= "</ul>\n";
            }
        }

        return $html;
    }
}