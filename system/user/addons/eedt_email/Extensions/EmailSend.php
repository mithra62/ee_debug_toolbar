<?php

namespace DebugToolbar\Email\Extensions;

class EmailSend extends AbstractHook
{
    /**
     * @param array $email_content
     * @return bool
     */
    public function process(array $email_content): bool
    {
        $settings = $this->toolbar->getSettings();
        if (!empty($settings['email_action']) && $settings['email_action'] == 'log') {
            ee()->extensions->end_script = true;
        }

        if(!empty($settings['email_action']) && in_array($settings['email_action'], ['log', 'log_send'])) {
            ee('eedt_email:EmailService')->log($email_content, $settings);
        }

        return true;
    }
}