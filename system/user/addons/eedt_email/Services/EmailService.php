<?php

namespace DebugToolbar\Email\Services;

use DebugToolbar\Email\Email\Parser;
use ExpressionEngine\Library\String\Str;
use ExpressionEngine\Service\Logger\File;

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
            $path = PATH_CACHE . 'eedt_emails';
        }

        if (!is_dir($path)) {
            mkdir($path);
        }

        $log_file = ee()->localize->now . '.' .
            implode('.', $email_content['recipients']) . '.' .
            Str::snakecase($email_content['subject']);

        $path .= '/' . $log_file;
        if (!is_dir($path)) {
            mkdir($path);
        }

        $file = new File($path . '/email.log', ee('Filesystem'));
        $file->log(json_encode($email_content, JSON_PRETTY_PRINT));

        $email = Parser::parse($email_content);
        $file = new File($path . '/email.html', ee('Filesystem'));
        $file->log($email['html']);

        $file = new File($path . '/email.text', ee('Filesystem'));
        $file->log($email['text']);
    }
}