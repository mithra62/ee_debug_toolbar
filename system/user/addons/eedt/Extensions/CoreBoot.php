<?php

namespace DebugToolbar\Extensions;

class CoreBoot extends AbstractHook
{
    public function process()
    {
        if (REQ === 'CP') {
            $settings = $this->toolbar->getSettings();
            if (count($settings['allowed_roles']) >= 2 || !in_array(1, $settings['allowed_roles'])) {
                ee('CP/Alert')->makeBanner('shared-form')
                    ->asIssue()
                    ->withTitle(lang('eedt.profiler_enabled_non_sa'))
                    ->canClose()
                    ->now();
            }
        }
    }
}
