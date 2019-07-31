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

    // TODO: отладить отображение сообщений
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

    private function connect()
    {
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
        }

        return true;
    }

    public function test()
    {
        unset($this->context);
        unset($this->message);
        $this->messageHeader = 'Проверка соединения с 1С Итилиум';
        $this->messageType = self::INFO;

        if ($this->connect()) {
            $this->message = 'Успешное соединение с сервером';
        }
        echo $this->notice();
        wp_die();
    }
}

