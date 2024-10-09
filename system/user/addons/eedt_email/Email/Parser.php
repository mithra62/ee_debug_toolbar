<?php

namespace DebugToolbar\Email\Email;

class Parser
{
    /**
     * @param array $email
     * @return array
     */
    static public function parse(array $email): array
    {
        $header_str = $email['header_str'] ?? '';
        $return = ['text' => '', 'html' => '', 'attachments' => []];
        if (ee()->config->config['mail_protocol'] == 'mail') {
            if (strpos($header_str, "Content-Type: text/plain") !== false) {
                $return['text'] = $email['finalbody'];
            } elseif (strpos($email['header_str'], "Content-Type: text/html") !== false) {
                $return['html'] = $email['finalbody'];
            } else {
                preg_match('/Content-Type: multipart\/[^;]+;\s*boundary="([^"]+)"/i', $email['header_str'], $matches);
            }
        } else {
            if (stripos($email['finalbody'], "Content-Type: text/plain") === 0) {
                $return['text'] = self::removeChunks($email['finalbody']);
            } elseif (stripos($email['finalbody'], "Content-Type: text/html") === 0) {
                $return['html'] = self::removeChunks($email['finalbody']);
            } else {
                preg_match('/^Content-Type: multipart\/[^;]+;\s*boundary="([^"]+)"/i', $email['finalbody'], $matches);
            }
        }

        if (!empty($matches) && !empty($matches[1])) {
            $boundary = $matches[1];
            $chunks = explode('--' . $boundary, $email['finalbody']);
            foreach ($chunks as $chunk) {
                if (stristr($chunk, "Content-Type: text/plain") !== false) {
                    $return['text'] = self::removeChunks($chunk);
                }

                if (stristr($chunk, "Content-Type: text/html") !== false) {
                    $return['html'] = self::removeChunks($chunk);
                }

                if (stristr($chunk, "Content-Disposition: attachment") !== false) {
                    preg_match('/Content-Type: (.*?); name=["|\'](.*?)["|\']/is', $chunk, $attachment_matches);
                    if (!empty($attachment_matches)) {
                        $type = $name = '';
                        if (!empty($attachment_matches[1])) {
                            $type = $attachment_matches[1];
                        }

                        if (!empty($attachment_matches[2])) {
                            $name = $attachment_matches[2];
                        }

                        $attachment = array(
                            'type' => trim($type),
                            'name' => trim($name),
                            'content' => self::removeChunks($chunk)
                        );
                        $return['attachments'][] = $attachment;
                    }
                }

                if (stristr($chunk, "Content-Type: multipart") !== false) {
                    preg_match('/Content-Type: multipart\/[^;]+;\s*boundary="([^"]+)"/i', $chunk, $inner_matches);
                    if (!empty($inner_matches) && !empty($inner_matches[1])) {
                        $inner_boundary = $inner_matches[1];
                        $inner_chunks = explode('--' . $inner_boundary, $chunk);
                        foreach ($inner_chunks as $inner_chunk) {
                            if (stristr($inner_chunk, "Content-Type: text/plain") !== false) {
                                $return['text'] = self::removeChunks($inner_chunk);
                            }

                            if (stristr($inner_chunk, "Content-Type: text/html") !== false) {
                                $return['html'] = self::removeChunks($inner_chunk);
                            }
                        }
                    }
                }
            }
        }

        if (!empty($return['html'])) {
            $return['html'] = quoted_printable_decode($return['html']);
        }

        return $return;
    }

    /**
     * @param string $chunk
     * @return string
     */
    static protected function removeChunks(string $chunk): string
    {
        return trim(preg_replace("/Content-(Type|ID|Disposition|Transfer-Encoding):.*?" . NL . "/is", "", $chunk));
    }
}