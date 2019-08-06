<?php
/**
 * Создан в PhpStorm.
 * Автор: vbulash
 * Дата и время: 2019-08-04 20:35
 */

namespace Services\Helpers;

/**
 * Утилита формирования сообщений
 * @package Services\Helpers
 */
class MessageHelper
{
    // Типы системных сообщений
    public const ERROR = 2;
    public const WARNING = 1;
    public const INFO = 0;

    // Сообщения
    private $messageType = self::INFO;
    private $messageHeader = null;
    private $message = null;

    public function __construct($type, $header, $message)
    {
        $this->messageType = $type;
        $this->messageHeader = $header;
        $this->message = $message;
    }

    /**
     * Генерация Bootstrap alert
     * @return string HTML-код bootstrap alert'а
     */
    public function generateBootstrapAlert(): string
    {
        $classes = 'alert alert-dismissible ';
        switch ($this->messageType) {
            case self::INFO:
                $classes = $classes . 'alert-success';
                break;
            case self::WARNING:
                $classes = $classes . 'alert-warning';
                break;
            case self::ERROR:
                $classes = $classes . 'alert-danger';
                break;
        }

        $header = (isset($this->messageHeader) ? sprintf('<strong>%s</strong>', $this->messageHeader) : null);

        $html = sprintf(
            '<div class="%s" id="informer">' .
            '<button type="button" id="informer_close" class="close" data-dismiss="alert">&times;</button>' .
            '%s' .
            '<p>%s</p>' .
            '</div>',
            $classes,
            $header,
            $this->message
        );

        return $html;
    }

    /**
     * Сброс полей сообщения
     */
    public function clear(): void
    {
        $this->setMessageType(self::INFO);
        $this->setMessageHeader(null);
        $this->setMessage(null);
    }

    /**
     * @return null
     */
    public function getMessageHeader()
    {
        return $this->messageHeader;
    }

    /**
     * @return null
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param null $messageHeader
     */
    public function setMessageHeader($messageHeader): void
    {
        $this->messageHeader = $messageHeader;
    }

    /**
     * @return int
     */
    public function getMessageType(): int
    {
        return $this->messageType;
    }

    /**
     * @param int $messageType
     */
    public function setMessageType(int $messageType): void
    {
        $this->messageType = $messageType;
    }

    /**
     * @param null $message
     */
    public function setMessage($message): void
    {
        $this->message = $message;
    }
}