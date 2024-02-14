<?php

namespace DebugToolbar\Errors\Services;

use ExpressionEngine\Service\Logger\File;

class LoggerService
{
    /**
     * @var File|null
     */
    protected ?File $logger = null;

    protected array $settings;

    public function __construct()
    {
        $this->settings = ee('eedt:ToolbarService')->getSettings();
    }

    /**
     * @return string
     */
    public function getLogFilePath(): string
    {
        $log_file = PATH_CACHE . 'error.log';
        if(!empty($this->settings['error_log_path']) && file_exists($this->settings['error_log_path'])) {
            $log_file = $this->settings['error_log_path'];
        }

        return $log_file;
    }

    /**
     * @return bool
     */
    public function deleteLog(): bool
    {
        if(file_exists($this->getLogFilePath())) {
            return unlink($this->getLogFilePath());
        }

        return false;
    }

    /**
     * @return File
     * @throws \Exception
     */
    public function getLogger(): File
    {
        if (is_null($this->logger)) {
            $this->logger = new File($this->getLogFilePath(), ee('Filesystem'));
        }

        return $this->logger;
    }

    /**
     * @param string $level
     * @param string $message
     * @param array $context
     * @return string
     */
    public function format(array $message): string
    {
        $message['datetime'] = ee()->localize->now;
        return trim(json_encode($message));
    }

    /**
     * @param string $level
     * @return bool
     */
    public function shouldLog(array $message): bool
    {
        $settings = ee('eedt:SettingsService')->getSettings();
        return in_array($message['code'], $settings['log_error_codes']);
    }

    /**
     * @return array
     * @throws \ExpressionEngine\Dependency\Safe\Exceptions\FilesystemException
     */
    public function getLogContents()
    {
        $path = $this->getLogFilePath();
        $return = [];
        if(!file_exists($path)) {
            return $return;
        }

        $contents = \ExpressionEngine\Dependency\Safe\file_get_contents($path);
        if($contents) {
            $errors = explode(ee('eedt_errors:LoggerService')->logDelimiter(), $contents);
            if($errors) {
                $errors = array_reverse($errors);
                foreach($errors AS $error) {
                    $error = json_decode($error, true);
                    if($error) {
                        $error['datetime'] = date('r', $error['datetime']);
                        $return[] = $error;
                    }
                }
            }
        }

        return $return;
    }

    /**
     * @return string
     */
    public function logDelimiter(): string
    {
        return str_repeat('+', 10);
    }
}