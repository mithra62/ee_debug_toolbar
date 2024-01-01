<?php

namespace DebugToolbar\Services;

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
     * @return File
     * @throws \Exception
     */
    public function getLogger(): File
    {
        if (is_null($this->logger)) {
            $log_file = PATH_CACHE . 'error.log';
            if(!empty($this->settings['error_log_path'])) {
                $log_file = $this->settings['error_log_path'];
            }

            $this->logger = new File($log_file, ee('Filesystem'));
        }

        return $this->logger;
    }

    /**
     * @param string $level
     * @param string $message
     * @param array $context
     * @return string
     */
    public function format(string $level, string $message, array $context = []): string
    {
        $return = ' [' . date('r') . '] (' . $level . ') Message: "' . $message . '" ';
        if ($context) {
            $return .= json_encode($context);

        }

        return trim($return);
    }

    /**
     * @param string $level
     * @return bool
     */
    public function shouldLog(string $level): bool
    {
        $log_levels = ee()->config->config['ee_debug_log_levels'] ?? [];
        if (!is_array($log_levels)) {
            $log_levels = [];
        }

        $log_levels = array_merge([
            'error',
            'notice',
            'warning',
            'emergency',
            'alert',
            'critical'
        ], $log_levels);

        return in_array($level, $log_levels);
    }
}