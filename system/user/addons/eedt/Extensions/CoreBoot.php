<?php

namespace DebugToolbar\Extensions;

class CoreBoot extends AbstractHook
{
    /**
     * @return void
     */
    public function process()
    {
        if (REQ === 'CP') {
            if ($this->shouldDisplayError()) {
                ee('CP/Alert')->makeBanner('shared-form')
                    ->asIssue()
                    ->withTitle(lang('eedt.profiler_enabled_non_sa'))
                    ->canClose()
                    ->now();
            }
        }
    }

    /**
     * @return bool
     */
    protected function shouldDisplayError(): bool
    {
        $settings = $this->toolbar->getSettings();
        $roles = $settings['allowed_roles'];
        foreach($roles AS $key => $value) {
            if($value == '') {
                unset($roles[$key]);
            }
        }

        return count($roles) >= 2 || (count($roles) >= 1) && !in_array(1, $roles);
    }
}
