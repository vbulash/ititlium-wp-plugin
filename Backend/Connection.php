<?php
/**
 * Создан в PhpStorm.
 * Автор: vbulash
 * Дата и время: 2019-07-29 16:09
 */

namespace Backend;

// Wrapper
class Connection
{
    // Типы системных сообщений
    const ERROR = 2;
    const WARNING = 1;
    const INFO = 0;

    private $URL = null;
    private $login = null;
    private $password = null;
    private $context = null;

    // Сообщения
    private $messageType = self::INFO;
    private $messageHeader = null;
    private $message = null;

    public function __construct()
    {
    }

    private function detectParam(&$key, $postParam, $value)
    {
        if (isset($_POST[$postParam])) {
            $key = $_POST[$postParam];
        } else {
            $key = $value;
        }
    }

    public function init()
    {
        add_action('wp_ajax_itilium_test', [$this, 'test']);
    }

    /**
     * Упаковка полей сообщения в json
     * Очищает обработанные поля
     *
     * @return string Упакованная строка полей сообщения
     */
    public function notice()
    {
        if (!isset($this->message)) return null;  // Нечего отображать

        $result = json_encode([
            'message_type' => $this->messageType,
            'message_header' => $this->messageHeader,
            'message' => $this->message
        ]);

        unset($this->messageHeader);
        unset($this->message);

        return $result;
    }

    public function connect()
    {
        unset($this->context);
        unset($this->message);
        $this->messageHeader = 'Проверка соединения с 1С Итилиум';
        $this->messageType = self::INFO;

        if (!is_user_logged_in()) {
            $this->messageType = self::ERROR;
            $this->message = 'Анонимный пользователь. Необходимо авторизоваться на сайте';
            return false;
        }
        $this->detectParam($this->URL, 'URL', get_option('itilium_URL'));
        $this->detectParam($this->login, 'login', get_user_meta(get_current_user_id(), 'itilium_user', true));
        $this->detectParam($this->password, 'password', get_user_meta(get_current_user_id(), 'itilium_password', true));

        $options = array(
            'http' => array(
                'header' => "Authorization: Basic " . base64_encode("$this->login:$this->password"), // . "\nContent-type: application/x-www-form-urlencoded",
                'method' => "GET",
                'content' => ""
            )
        );
        $this->context = stream_context_create($options);

        $data = file_get_contents($this->URL . 'authenticate', false, $this->context);
        if ($data === false) {
            $this->messageType = self::ERROR;
            $this->message = error_get_last()['message'] . "<br/>Проверьте корректность URL, логина и пароля";
            return false;
        } else {
            $this->message = 'Успешное соединение с сервером';
            return true;
        }
    }

    public function getAll()
    {
        $connected = false;

        // Нет контекста - не было предыдущей аутентификации
        if (!isset($this->context)) {
            $connected = $this->connect();
            //$this->notice();
        } else {
            $connected = true;
        }

        if (!$connected) return false;   // Не удалось законнектиться к базе 1С Итилиум

        // Можно получать данные
        $data = file_get_contents($this->URL . 'getListIncidents', false, $this->context);
        if (!$data) {
            $this->messageType = self::ERROR;
            $this->message = error_get_last()['message'];
            return false;
        } else return $data;
    }

    public function getIncident($UID)
    {
        $connected = false;

        // Нет контекста - не было предыдущей аутентификации
        if (!isset($this->context)) {
            $connected = $this->connect();
            //$this->notice();
        } else {
            $connected = true;
        }

        if (!$connected) return false;   // Не удалось законнектиться к базе 1С Итилиум

        // Можно получать данные
        $data = file_get_contents($this->URL . 'getDetailInfoIncindent/' . $UID, false, $this->context);
        if (!$data) {
            $this->messageType = self::ERROR;
            $this->message = error_get_last()['message'];
            return false;
        } else return $data;
    }

    public function getFile($UID)
    {
        $connected = false;

        // Нет контекста - не было предыдущей аутентификации
        if (!isset($this->context)) {
            $connected = $this->connect();
            //$this->notice();
        } else {
            $connected = true;
        }

        if (!$connected) return false;   // Не удалось законнектиться к базе 1С Итилиум

        // Можно получать данные
        $data = file_get_contents($this->URL . 'getFileData/' . $UID, false, $this->context);
        if (!$data) {
            $this->messageType = self::ERROR;
            $this->message = error_get_last()['message'];
            return false;
        } else {
            /*
             $DataFile = json_decode($dataFileString);
    $Value = base64_decode($DataFile->Data);
    header ("Content-Type: application/octet-stream");
    header ("Accept-Ranges: bytes");
    header ("Content-Length: ".filesize($Value));
    header ("Content-Disposition: attachment; filename=".$DataFile->Name);
    echo "$Value";
             */
            $data = json_decode($data);
            $content = base64_encode($data->Data);

            $tmpFilename = get_temp_dir() . $data->Name;
            file_put_contents($tmpFilename, $content);
            header("Content-Type: application/octet-stream");
            header("Accept-Ranges: bytes");
            header("Content-Length: " . filesize($tmpFilename));
            header("Content-Disposition: attachment; filename=" . $data->Name);
            echo $content;
            return true;
        }
    }

    public function test()
    {
        $this->connect();
        echo $this->createBootstrapAlert();

        wp_die();
    }

    public function createBootstrapAlert()
    {
        $classes = 'alert alert-dismissible ';
        switch ($this->messageType) {
            case 0: // Info
                $classes .= 'alert-success';
                break;
            case 1: // Warning
                $classes .= 'alert-warning';
                break;
            case 2: // Error
                $classes .= 'alert-danger';
                break;
        }
        $message = sprintf(
            "<div class='%s' id='informer'>\n" .
            "\t<button type='button' id='informer_close' class='close' data-dismiss='alert'>&times;</button>\n" .
            "\t<p class='alert-heading'><strong >%s</strong></p>\n" .
            "\t<p>%s</p>\n" .
            "</div>",
            $classes, $this->messageHeader, $this->message
        );

        $this->messageType = self::INFO;
        unset($this->messageHeader);
        unset($this->message);

        return $message;
    }
}

