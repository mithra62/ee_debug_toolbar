<?php

namespace DebugToolbar\Email\Services;

use ExpressionEngine\Library\String\Str;
use ExpressionEngine\Service\Logger\File;
use DebugToolbar\Email\Email\Parser;

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
        if (!$path) {
            $path = PATH_CACHE . 'emails';
        }

        if (!is_dir($path)) {
            mkdir($path);
        }

        $log_file = $path . '/' . ee()->localize->now . '.' .
            implode($email_content['recipients']) . '.' . Str::snakecase($email_content['subject']);
        $file = new File($log_file . '.txt', ee('Filesystem'));
        $file->log(print_r($email_content, true));

        $email = Parser::parse($email_content);
        $file = new File($log_file . '.html', ee('Filesystem'));
        $file->log($email['html']);

        $file = new File($log_file . '.text.txt', ee('Filesystem'));
        $file->log($email['text']);
    }
}