<?php

namespace DebugToolbar\Email\Services;

use ExpressionEngine\Service\Logger\File;
use ExpressionEngine\Library\String\Str;

class EmailService
{
    /**
     * @return string[]
     */
    public function getActionOptions(): array
    {
        return [
            'send' => 'Send',
            'log_send' => 'Log & Send',
            'log' => 'Log Only',
        ];
    }

    /**
     * @param array $email_content
     * @param array $settings
     * @return void
     */
    public function log(array $email_content, array $settings)
    {
        $path = $settings['email_log_dir'];
        if(!$path) {
            $path = PATH_CACHE . 'emails';
        }

        if(!is_dir($path)) {
            mkdir($path);
        }

        $log_file = $path . '/'. ee()->localize->now . '.' .
            implode($email_content['recipients']) . '.' . Str::snakecase($email_content['subject']) .
            '.txt';
        $file = new File($log_file, ee('Filesystem'));
        $file->log(print_r($email_content, true));
    }
}