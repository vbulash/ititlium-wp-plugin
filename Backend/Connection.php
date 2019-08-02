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

    private function detectPOST(&$key, $postParam)
    {
        if (!isset($key) && isset($_POST[$postParam]))
            $key = $_POST[$postParam];
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

        $this->detectPOST($this->URL, 'URL');
        $this->detectPOST($this->login, 'login');
        $this->detectPOST($this->password, 'password');

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
        if(!isset($this->context)) {
            $connected = $this->connect();
            $this->notice();
        } else {
            $connected = true;
        }

        if(!$connected) return false;   // Не удалось законнектиться к базе 1С Итилиум

        // Можно получать данные
        $data = file_get_contents($this->URL . 'getListIncidents', false, $this->context);
        if(!$data) {
            $this->messageType = self::ERROR;
            $this->message = error_get_last()['message'];
            return false;
        }

        return json_decode($data);
    }

    public function test()
    {
        $this->connect();
        echo $this->notice();

        // Отладка getAll
        /*
        $resultSet = $this->getAll(); // -- массив объектов-записей
        foreach ($resultSet as $row) {
            $properties = get_object_vars($row);
            error_log('Инцидент из 1С Итилиум');
            foreach ($properties as $property => $value) {
                error_log($property . ' = ' . $value);
            }
        }
        echo $this->notice();
        */

        wp_die();
    }
}

