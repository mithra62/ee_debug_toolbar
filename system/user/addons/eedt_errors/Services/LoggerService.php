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
        $this->settings = ee('ee_debug_toolbar:ToolbarService')->getSettings();
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
        return trim(json_encode($message));
    }

    /**
     * @param string $level
     * @return bool
     */
    public function shouldLog(array $message): bool
    {
        $settings = ee('ee_debug_toolbar:SettingsService')->getSettings();
        return in_array($message['code'], $settings['log_error_codes']);
    }
}