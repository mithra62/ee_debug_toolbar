<?php

namespace DebugToolbar\Errors\Logging;

class Logger
{
    /**
     * @var string
     */
    protected string $called_class = '';

    /**
     * @param string $class
     * @return $this
     */
    public function setCalledClass(string $class): Logger
    {
        $this->called_class = $class;
        return $this;
    }

    /**
     * @return string
     */
    public function getCalledClass(): string
    {
        return $this->called_class;
    }

    /**
     * @param string $message
     * @param int $code
     * @param string $file
     * @param int $line
     * @param array $trace
     * @return void
     */
    public function error(string $message, int $code, string $file, int $line, array $trace = []): void
    {
        $msg = [
            'message' => $message,
            'code' => $code,
            'file' => $file,
            'line' => $line,
            'trace' => $trace,
        ];
        $this->log($msg);
    }

    /**
     * Logs with an arbitrary level.
     * @param $level
     * @param string $message
     * @param array $context
     * @return void
     */
    public function log(array $msg): void
    {
        if (ee('eedt_errors:LoggerService')->shouldLog($msg)) {
            $logger = ee('eedt_errors:LoggerService')->getLogger();
            $message = ee('eedt_errors:LoggerService')->format($msg);
            $logger->log($message . "\n" .$this->logDelimiter());
        }
    }

    /**
     * @return string
     */
    protected function logDelimiter(): string
    {
        return str_repeat('+', 10);
    }
}