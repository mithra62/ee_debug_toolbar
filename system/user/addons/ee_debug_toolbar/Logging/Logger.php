<?php

namespace DebugToolbar\Logging;

class Logger implements LoggerInterface
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
     * System is unusable.
     * @param string $message
     * @param array $context
     * @return void
     */
    public function emergency(string $message, array $context = []): void
    {
        $this->log(LogLevel::EMERGENCY, $message, $context);
    }

    /**
     * Action must be taken immediately.
     * @param string $message
     * @param array $context
     * @return void
     */
    public function alert(string $message, array $context = []): void
    {
        $this->log(LogLevel::ALERT, $message, $context);
    }

    /**
     * Critical conditions.
     * @param string $message
     * @param array $context
     * @return void
     */
    public function critical(string $message, array $context = []): void
    {
        $this->log(LogLevel::CRITICAL, $message, $context);
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function error(string $message, array $context = []): void
    {
        $this->log(LogLevel::ERROR, $message, $context);
    }

    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function warning(string $message, array $context = []): void
    {
        $this->log(LogLevel::WARNING, $message, $context);
    }

    /**
     * Normal but significant events.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function notice(string $message, array $context = []): void
    {
        $this->log(LogLevel::NOTICE, $message, $context);
    }

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function info(string $message, array $context = []): void
    {
        $this->log(LogLevel::INFO, $message, $context);
    }

    /**
     * Detailed debug information.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function debug(string $message, array $context = []): void
    {
        $this->log(LogLevel::DEBUG, $message, $context);
    }

    /**
     * Logs with an arbitrary level.
     * @param $level
     * @param string $message
     * @param array $context
     * @return void
     */
    public function log($level, string $message, array $context = []): void
    {
        if (ee('ee_debug_toolbar:LoggerService')->shouldLog($level)) {
            $logger = ee('ee_debug_toolbar:LoggerService')->getLogger();

            $message = ee('ee_debug_toolbar:LoggerService')->format($level, $message, $context);
            $logger->log($message . ' : ' . $this->getCalledClass());
            //throw new InvalidArgumentException();
        }
    }
}